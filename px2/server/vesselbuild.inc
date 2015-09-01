use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::vesselbuild_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub vesselbuild () {
  my ($rx, $tx) = @_;
  my ($planet_id, $name, $impulse, $warp, $type, $user_id);

  item_add ($tx, "pkg_cmd", "VESSELBUILD");

  $planet_id = $rx->{home};
  $name = $rx->{name};
  $impulse = $rx->{impulse};
  $warp = $rx->{warp};
  $type = $rx->{type};
  $user_id = $rx->{uid};

  # No decent params found...
  if ($planet_id eq "" or $name eq "" or $impulse eq "" or $warp eq "" or $type eq "" or $user_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  my ($dbh, $sth, $user, $count, $planet, $sector);
  $user = px_user::get_user ($user_id);
  $planet = px_planet::get_planet ($planet_id);
  $sector = px_sector::get_sector ($planet->{sector_id});
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT COUNT(*) AS count FROM g_vessels WHERE name=?", $name);
  $count = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);


  # Check if the name already exist...
  if ($count->{count} > 0) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "NAME_EXISTS");
  	return;
  }

  # Check if the ship has the right impulse and warp
  if ($warp > $user->{warp} or $impulse > $user->{impulse}) {
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

  # Check if we use a home planet which is ours
  if ($planet->{user_id} != $user_id) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "HOME");
	  return;
  }

  # Check if the type is allright
  if ($type != "E" and $type != "B" and $type != "T") {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "TYPE");
  	return;
  }

  # Check the credits
  my $credits = $px_config::config->{s_ship_costs}*$user->{vessel_level} + ($impulse * $px_config::config->{s_impulse_costs}) + ($warp * $px_config::config->{s_warp_costs});
  if ($user->{credits} < $credits) {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "CREDITS");
  	return;
  }

  # Build it...
  px_user::set_credits ($user_id, $credits, px_user->SET_CREDITS_SUB);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_vessels (id, user_id, planet_id, sector_id, type, created, name, crew, distance, angle, impulse, warp, convoy_id, ore1, ore2, ore3, ore4, ore5, ore6, status) VALUES (0, ?, ?, ?, ?, 0, ?, 0, ?, ?, ?, ?, 0, 0, 0, 0, 0, 0, 0, 'ORBIT')", $user_id, $planet_id, $sector->{id}, "$type", $name, $planet->{distance}, $sector->{angle}, $impulse, $warp);
  my $new_id = px_mysql::get_last_insert_id ();

  px_ef_vessel::execute_vessel_function ("QUEUE_IN", $new_id, $planet_id, $user_id);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO h_queue (type, ticks, vessel_id, planet_id, user_id) VALUES (?, 6, ?, ?, ?)", constants->QUEUE_VESSEL, $new_id, $planet_id, $user_id);

  item_add ($tx, "status", "STATUS_OK");
  return;
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

