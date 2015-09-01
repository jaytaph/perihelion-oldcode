use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::tradedelete_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub tradedelete () {
  my ($rx, $tx) = @_;
  my ($user_id, $traderoute_id);

  item_add ($tx, "pkg_cmd", "TRADEDELETE");

  $traderoute_id = $rx->{aid};

  # No decent params found...
  if ($traderoute_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "PARAMS");
    return;
  }

  # TODO: why wouldn't be a digit in the first place then???
  # Make sure aid is a digit
  if ($traderoute_id =~ /\d+/) {
    px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM a_trades WHERE id=?", $traderoute_id);
  }

  item_add ($tx, "status", "STATUS_OK");
  return;
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#

