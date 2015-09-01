use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::vesselupgrade_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub vesselupgrade () {
  my ($rx, $tx) = @_;
  my ($user, $vessel, $vessel_id, $impulse, $warp);

  item_add ($tx, "pkg_cmd", "VESSELUPGRADE");

  $impulse = $rx->{impulse};
  $warp = $rx->{warp};
  $vessel_id = $rx->{vid};

  # No decent params found...
  if ($impulse eq "" or $warp eq "" or $vessel_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  $vessel = px_vessel::get_vessel ($vessel_id);
  $user = px_user::get_user ($vessel->{user_id});

  # Check if the ship has the right impulse and warp
  if ($warp > $user->{warp} || $impulse > $user->{impulse}) {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "SPEED");
  	return;
  }

  # Check if the ship has 100 impulse when it has warp
  if ($warp > 0 and $impulse != 100) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "SPEED");
	  return;
  }

  # Check the credits
  my $credits = (($impulse - $vessel->{impulse}) * $px_config::config->{s_impulse_costs}) + (($warp-$vessel->{warp}) * $px_config::config->{s_warp_costs});
  print "Costs for this will be: $credits\n";
  if ($user->{credits} < $credits) {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "CREDITS");
  	return;
  }

  px_user::set_credits ($vessel->{user_id}, $credits, px_user->SET_CREDITS_SUB);

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET created=0, impulse=?, warp=? WHERE id=?", $impulse, $warp, $vessel_id);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO h_queue (type, ticks, vessel_id, planet_id, user_id) VALUES (?, 6, ?, 0, ?)", constants->QUEUE_UPGRADE, $vessel->{id}, $vessel->{user_id});

  item_add ($tx, "status", "STATUS_OK");
  return;
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

