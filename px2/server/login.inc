use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::login_privilege = RESTRICTED;

# -------------------------------------------------------------------------
# Description:
#   Logs into the system. Sets up housekeeping and administrative paperwork
#   into the database.
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub login () {
  my ($rx, $tx) = @_;
  my ($user_id, $session_id);

  item_add ($tx, "pkg_cmd", "LOGIN");

  $user_id = $rx->{id};
  $session_id = $rx->{sess_id};


  # No decent params found...
  if ($user_id eq "" or $session_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  px_mysql::select_user_db ();
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO perihelion.u_access (user_id, php_session_id, login) VALUES (?, ?, NOW())", $user_id, $session_id);
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE perihelion.u_users SET login_count=login_count+1 WHERE id=?", $user_id);
  px_mysql::select_db_by_id ($user_id);
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET active=1 WHERE user_id=?", $user_id);
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

