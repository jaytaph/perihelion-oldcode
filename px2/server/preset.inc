use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::preset_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Creates or deletes a preset.
#     $action = "create"  -> insert new entry
#     $action = "delete"  -> delete entry $pid
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    UNKNOWN             Internal error
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub preset () {
  my ($rx, $tx) = @_;
  my ($action, $pid, $uid, $distance, $angle, $name);

  item_add ($tx, "pkg_cmd", "PRESET");

  $action   = $rx->{action};
  $pid      = $rx->{pid};
  $uid      = $rx->{uid};
  $distance = $rx->{distance};
  $angle    = $rx->{angle};
  $name     = $rx->{name};

  # No decent params found...
  if ($action eq "" or $pid eq "" or $uid eq "" or $distance eq "" or $angle eq "" or $name eq "") {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "PARAMS");
    return;
  }

  # If PID is given, we want to delete an item...
  if ($action eq "delete") {
    my $err = px_preset::delete_entry ($pid);
    if ($err == errors->ERR_OK) {
      item_add ($tx, "status", "STATUS_OK");
    } else {
      item_add ($tx, "status", "STATUS_ERR");
      item_add ($tx, "msg", "UNKNOWN");
    }
    return;
  }

  if ($action eq "create") {
    my $err = px_preset::create_entry ($uid, $name, $distance, $angle);
    if ($err == errors->ERR_ENTRY_ALREADY_EXISTS) {
      item_add ($tx, "status", "STATUS_ERR");
	    item_add ($tx, "msg", "NAME");
    } else {
      item_add ($tx, "status", "STATUS_OK");
    }
    return;
  }

}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

