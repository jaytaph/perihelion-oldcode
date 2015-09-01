use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::messagedel_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Deletes a message from the user database. We can't really delete
#   alliance or galaxy messages. This will probably change in the future.
#   By selecting a negative $msg_id we could delete a alliance_message,
#   but we should check if it's beeing deleted by the owner_id of the
#   alliance
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub messagedel () {
  my ($rx, $tx) = @_;
  my ($msg_id);

  item_add ($tx, "pkg_cmd", "MESSAGEDEL");

  $msg_id = $rx->{mid};

  # No decent params found...
  if ($msg_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  px_message::delete_message ($msg_id, px_message->MSG_USER);
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

