use strict;
use lib '../globalperl/';

$main::setprefs_privilege = NORMAL;

# -------------------------------------------------------------------------
#  Registers a new user to perihelion.
#
#  Returns:
#    PARAMS        Unknown parameters specified
#
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub setprefs () {
  my ($rx, $tx) = @_;

  my ($tmp, $homeplanet_id, $distance);

  item_add ($tx, "pkg_cmd", "SETPREFS");

  my $name        = $rx->{name};
  my $email       = $rx->{email};
  my $inform      = $rx->{inform};
  my $gender      = $rx->{gender};
  my $city        = $rx->{city};
  my $country     = $rx->{country};
  my $tag         = $rx->{tag};
  my $dob         = $rx->{dob};
  my $login_pass  = $rx->{login_pass};
  my $user_id     = $rx->{user_id};
  my $avatar      = $rx->{avatar};
  my $theme_id    = $rx->{theme_id};

  # No decent params found...
  if ($user_id eq "" or $name eq "" or $email eq "" or $gender eq "" or $dob eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  # Set autocommit off. If the database supports it, we can issue a rollback if
  # the registration fails. Particulary interessting when we are debugging the stuff.
  my ($dbh, $sth, $user);
  px_mysql::query (constants->QUERY_NOKEEP, "SET AUTOCOMMIT=0");
  px_mysql::query (constants->QUERY_NOKEEP, "BEGIN");

  $inform = 'N';
  if ($inform eq "on") { $inform = 'Y'; }

  # Add the user and get the id
  px_mysql::query (constants->QUERY_NOKEEP, "USE perihelion");
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE perihelion.u_users SET name=?, email=?, inform=?, gender=?, city=?, country=?, tag=?, birthday=?, theme_id=? WHERE id=?", $name, $email, $inform, $gender, $city, $country, $tag, $dob, $theme_id, $user_id);

  # Set avatar if needed
  if ($avatar ne "") {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE perihelion.u_users SET avatar=? WHERE id=?", $avatar, $user_id);
  }

  # Set login pass if needed
  if ($login_pass ne "") {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE perihelion.u_users SET login_pass=PASSWORD(?) WHERE id=?", $login_pass, $user_id);
  }

  # Commit (only functional with different database tables)
  px_mysql::query (constants->QUERY_NOKEEP, "COMMIT");

  item_add ($tx, "status", "STATUS_OK");
  return;
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

