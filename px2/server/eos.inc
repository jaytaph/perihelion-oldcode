use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::eos_privilege = NONE;

# -------------------------------------------------------------------------
# Description:
#    End of session. After this command there will be no more other commands.
#    Usefull for administrative purposes and managment.
#
# Returns:
#
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub eos () {
  my ($rx, $tx) = @_;

  $main::privilege = NONE;
  print "* Setting privilege back to NONE\n";

  item_add ($tx, "pkg_cmd", "ID");
  item_add ($tx, "status", "STATUS_OK");
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

