use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::logout_privilege = RESTRICTED;

# -------------------------------------------------------------------------
# Description:
#   Logs a user out from the system. Does some paperwork in the database.
#   We can't really rely on this function because not everybody logs off
#   the correct way (some just shuts their computer off etc).
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub logout () {
  my ($rx, $tx) = @_;
  my ($user_id, $session_id);

  item_add ($tx, "pkg_cmd", "LOGOUT");

  $user_id = $rx->{id};
  $session_id = $rx->{sess_id};

  # No decent params found...
  if ($user_id eq "" or $session_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  px_mysql::query (constants->QUERY_NOKEEP, "USE perihelion");
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE perihelion.u_access SET logout=NOW() WHERE user_id=? and php_session_id=?", $user_id, $session_id);
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

