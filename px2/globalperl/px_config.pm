package px_config;
use strict;
use English;
use px_mysql;	  		# Mysql connection routines
use lib '../globalperl/';
use constants;

my $current_galaxy = "";
set_galaxy ("");

# Return OK status to calling program
return 1;


# ====================================================================================
# Load a galaxy configuration or use the current one
sub set_galaxy ($) {
  my $galaxy = shift;

  if ($galaxy eq $px_config::current_galaxy) {
    $0 = "PXServer - Serving Galaxy $galaxy - Cached";
  } else {
    $0 = "PXServer - Serving Galaxy $galaxy";
    $px_config::current_galaxy = $galaxy;

    my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM ".$galaxy.".c_config ORDER BY priority DESC LIMIT 1");
    $px_config::galaxy = px_mysql::fetchhash ($sth);
    px_mysql::query_finish ($sth);
  }
}


# ====================================================================================
# Read configuration and set the alarm timer to reread it again after x minuts
sub readconfig () {
  my ($dbh, $sth);

  $dbh = px_mysql::connect ();
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM perihelion.c_config ORDER BY priority DESC LIMIT 1");
  $px_config::config = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);

  px_mysql::disconnect ($dbh);

  $SIG{'ALRM'} = \&px_config::readconfig;
  alarm ($px_config::config->{h_configread} * 60);
}

# ====================================================================================
#
# vim: ts=4 syntax=perl nowrap
#
