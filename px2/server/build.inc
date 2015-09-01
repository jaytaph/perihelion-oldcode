use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::build_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   This function will build $building_id on $planet_id for the user $user_id
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub build () {
  my ($rx, $tx) = @_;
  my ($user_id, $planet_id, $building_id, $building_ores, $stock_ores);

  item_add ($tx, "pkg_cmd", "BUILD");

  # Get parameters from the receive hash
  $user_id = $rx->{uid};
  $planet_id = $rx->{pid};
  $building_id = $rx->{bid};

  # No decent params found...
  if ($user_id eq "" or $planet_id eq "" or $building_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "PARAMS");
    return;
  }

  my ($sth, $user, $building, $planet, $surface, $build_queue_count, $ores);
  $user     = px_user::get_user ($user_id);
  $planet   = px_planet::get_planet ($planet_id);
  $ores     = px_planet::get_ores ($planet_id);
  $surface  = px_planet::get_surface ($planet_id);
  $building = px_building::get_building ($building_id);
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM h_queue WHERE planet_id=? AND type=?", $planet_id, constants->QUEUE_BUILD);
  $build_queue_count = $sth->rows;
  px_mysql::query_finish ($sth);

  $stock_ores = $ores->{stock_ores};
  $building_ores = $building->{initial_ores};


  # Check if we have the correct building_level
  if ($user->{science_building} < $building->{science_level}) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "SCIENCE");
    return;
  }
  # Check if we already are building something
  if ($build_queue_count >= $px_config::galaxy->{max_building_cnt}-1) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "QUEUE");
    return;
  }
  # Check credits
  if ($building->{initial_costs} > $user->{credits}) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "CREDITS");
    return;
  }
  # Check ores
  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    if (px_ore::get_ore ($building_ores, $i) > px_ore::get_ore ($stock_ores, $i)) {
      item_add ($tx, "status", "STATUS_ERR");
      item_add ($tx, "msg", "ORE");
      return;
    }
  }

  # Check power
  my $total_power = px_planet::power_total ($planet->{id});
  if ($building->{power_in} > $total_power) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "POWER");
    return;
  }
  # Check maximum buildings
  my $nr_of_buildings = px_planet::count_buildings ($planet->{id}, $building->{id});
  if ($building->{max} != 0 and $building->{max} <= $nr_of_buildings) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "MAX");
    return;
  }

  # Check dependencies
  if (check_dependencies ($planet->{id}, $building->{id}) == 0) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "DEPS");
    return;
  }

  # Everything is ok, pitty the fool and build the sucka...
  px_user::set_credits ($user_id, $building->{initial_costs}, px_user->SET_CREDITS_SUB);

  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    $stock_ores = px_ore::set_ore ($stock_ores, $i, px_ore::get_ore ($stock_ores, $i) - px_ore::get_ore ($building_ores, $i));
  }
  px_planet::update_ores ($planet->{id}, "", $stock_ores);

  # Add the building to the queue
  px_ef_building::execute_building_function ("QUEUE_IN", $building_id, $planet_id, $user_id);
  $sth = px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO h_queue (type, ticks, building_id, planet_id, user_id) VALUES (?, ?, ?, ?, ?)", constants->QUEUE_BUILD, $building->{ticks}, $building_id, $planet_id, $user_id);

  item_add ($tx, "status", "STATUS_OK");
  return;
}


# -------------------------------------------------------------------------
# Returns 0 if building dependencies failes, 1 otherwise
sub check_dependencies () {
  my ($planet_id, $building_id) = @_;

  my $tmp = px_building::get_building ($building_id);
  my @dependencies = split (",", $tmp->{csl_depends});
  my $surface = px_planet::get_surface ($planet_id);

  # Browse trough all dependencies. Check if the dependency exist on the surface
  foreach my $dep (@dependencies) {
    $tmp = 0;
    if ($surface->{building_ids} =~ /$dep/) { $tmp = 1; }
    if ($tmp == 0) { return 0; }
  }

  return 1;
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

