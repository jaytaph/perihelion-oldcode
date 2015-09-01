package px_mysql;
use strict;
use DBI;
use lib '../globalperl/';
use constants;
use errors;

# ----------------------------------------------------------------------------
# Mysql configuration
my $sql_server		= "localhost";
my $sql_db				= "perihelion";
my $sql_user			= "px626106";
my $sql_pass			= "sifQ+inlajz5ch";

my $SQL_DEBUG = 0;

#my $dbhandle = 0;			# Global database handle

# Return OK status to calling program
return 1;


# ----------------------------------------------------------------------------
sub set_debug ($) {
  my $flag = shift;
  $SQL_DEBUG = $flag;

  return errors->ERR_OK;
}

# ----------------------------------------------------------------------------
sub get_last_insert_id () {
  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT LAST_INSERT_ID() AS id");
  my $res = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);

  return $res->{id};
}


# ----------------------------------------------------------------------------
# Create a connection to the mysql table
sub open_dbhandle () {
  if ($px_mysql::dbhandle == 0) {
    $px_mysql::dbhandle = px_mysql::connect();
    return errors->ERR_OK;
  }
  return errors->ERR_ALREADY_CONNECTED;
}

# ----------------------------------------------------------------------------
# Create a connection to the mysql table
sub connect () {
  my $datasource = "DBI:mysql:host=$sql_server;database=$sql_db";

  my $tmp_dbhandle;
  $tmp_dbhandle = DBI->connect ($datasource, $sql_user, $sql_pass) or die ("Cannot connect to database...\n");
  $tmp_dbhandle->{AutoCommit} = 1;
  $tmp_dbhandle->{RaiseError} = 0;

  $px_mysql::dbhandle = $tmp_dbhandle;
  return $tmp_dbhandle;
}

# ----------------------------------------------------------------
sub select_user_db () {
  select_db_by_name ($sql_db);
  return errors->ERR_OK;
}

# ----------------------------------------------------------------
sub select_db_by_name ($) {
  my ($dbname) = @_;

  px_mysql::query (constants->QUERY_NOKEEP, "use $dbname");
  return errors->ERR_OK;
}

# ----------------------------------------------------------------
sub select_db_by_id ($) {
  my ($user_id) = @_;

  px_mysql::query (constants->QUERY_NOKEEP, "use $sql_db");
  my $sth = px_mysql::query (constants->QUERY_KEEP, "select galaxy_db from perihelion.u_users where id=?", $user_id);
  my $row = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);

  px_mysql::query (constants->QUERY_NOKEEP, "use ".$row->{galaxy_db});
  return errors->ERR_OK;
}

# ----------------------------------------------------------------
sub select_galaxy ($) {
  my ($galaxy) = @_;

  px_mysql::query (constants->QUERY_NOKEEP, "use $galaxy");
  return errors->ERR_OK;
}

# ----------------------------------------------------------------------------
# Format: query ([constants->QUERY_KEEP|constants->QUERY], QUERY, ARGUMENTLIST);
# constants->QUERY_KEEP = selecthandle is kept.
# constants->QUERY_NOKEEP = selecthandle is destroyed before returning.
#
sub query {
  if ($SQL_DEBUG == 1) {
    print "ARGS: @_\n";
  }

  my @args = @_;
  my $keep = shift @args;
  my $qry = shift @args;

  # Only print the query when we need something..
  if ($SQL_DEBUG == 1) {
    print "QRY: $qry\n";
  }

  # If no arguments are given, the query will be the first in
  # the argument list, correct this..
  if ($qry eq "") {
    $qry = $args[0];
	  undef @args;
  }

  # Make sure we have a connection before we run the query
  if ($px_mysql::dbhandle == 0) { px_mysql::connect(); }

  my $arg_count = () = $qry =~ /\?/g;
  if ($arg_count != $#args+1 and $#args != -1) {
    print "WARNING ($arg_count / ".($#args+1).")!\n";
	  print "Query: $qry\n";
	  print "Args : @args\n";
  }

  my $sthandle = $px_mysql::dbhandle->prepare ($qry) or die "Cannot prepare query. Reason: $DBI::strerr";
  $sthandle->execute (@args) or die "Cannot execute query $qry with args: @args. Reason: $DBI::strerr";

  if ($keep == constants->QUERY_NOKEEP) { return undef; }
  return $sthandle;
}

# ----------------------------------------------------------------------------
sub query_finish ($) {
  my $handle = shift;

  if ($handle) { $handle->finish; }
  return errors->ERR_OK;
}

# ----------------------------------------------------------------------------
sub close_dbhandle () {
  my $handle = $px_mysql::dbhandle;
  if ($handle != 0) { $handle->disconnect(); }
  $px_mysql::dbhandle = 0;
  return errors->ERR_OK;
}

# ----------------------------------------------------------------------------
sub disconnect ($) {
  my $handle = shift;

  $handle->disconnect();
  $px_mysql::dbhandle = 0;
  return errors->ERR_OK;
}


# ----------------------------------------------------------------------------
sub fetchhash ($) {
  my $handle = shift;
  return $handle->fetchrow_hashref();
}


# ----------------------------------------------------------------------------
my $sth_update_science = 0;
sub update_science ($$$$$) {
  my ($user_id, $v, $i, $b, $e) = @_;

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_update_science == 0) {
    $sth_update_science = $px_mysql::dbhandle->prepare ("UPDATE g_users SET vessel_level=?, invention_level=?, building_level=?, explore_level=? WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_update_science->execute ($v, $i, $b, $e, $user_id) or die "Reason: $DBI::strerr";
}


# ===========================================================================
#
# vim: ts=4 syntax=perl nowrap
#
