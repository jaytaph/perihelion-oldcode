use strict;
use lib '../globalperl/';

$main::manufacture_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#  Puts a building into the build queue after checking if it can be build.
#  It will let the heartbeat do the actual building and placing on the
#  planet.
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    QUEUE               There are already a maximum number of buildings
#                        of this type in the queue.
#    CREDITS             Not enough credits to build this.
#    ORE                 Not enough ores to build this.
#    MAX                 Maximum number of this type of building reached
#                        on this planet.
#    DEPS                Not all dependencies functioning or available.
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
# This function will manufacture $bid on $pid
sub manufacture () {
  my ($rx, $tx) = @_;
  my ($planet_id, $item_id, $stock_ores, $item_ores);

  item_add ($tx, "pkg_cmd", "MANUFACTURE");

  # Get parameters from the receive hash
  $planet_id = $rx->{pid};
  $item_id = $rx->{bid};

  # No decent params found...
  if ($planet_id eq "" or $item_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "PARAMS");
    return;
  }

  my ($sth, $user, $items, $item, $planet, $ores, $item_queue_count);
  $planet   = px_planet::get_planet ($planet_id);
  $user     = px_user::get_user ($planet->{user_id});
  $ores     = px_planet::get_ores ($planet_id);
  $items    = px_planet::get_planet_items ($planet_id);
  $item     = px_item::get_item ($item_id);
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM h_queue WHERE planet_id=? AND type=?", $planet_id, constants->QUEUE_INVENTION);
  $item_queue_count = $sth->rows;
  px_mysql::query_finish ($sth);

  $stock_ores = $ores->{stock_ores};
  $item_ores = $item->{initial_ores};

  # Check if we already are building something
  if ($item_queue_count >= $px_config::galaxy->{max_item_cnt}-1) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "QUEUE");
    return;
  }
  # Check credits
  if ($item->{initial_costs} > $user->{credits}) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "CREDITS");
    return;
  }
  # Check ores
  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    if (px_ore::get_ore ($item_ores, $i) > px_ore::get_ore ($stock_ores, $i)) {
      item_add ($tx, "status", "STATUS_ERR");
      item_add ($tx, "msg", "ORE");
      return;
    }
  }

  # Check if we have reached maximum number of items
  my $nr_of_items = items_total ($planet->{id}, $item->{id});
  if ($item->{max} != 0 and $item->{max} <= $nr_of_items) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "MAX");
    return;
  }
  # Check dependencies
  if (check_manufacturing_dependencies ($planet->{id}, $item->{id}) == 0) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "DEPS");
    return;
  }

  # Everything is ok, pitty the fool and build the sucka...
  px_user::set_credits ($user->{user_id}, $item->{initial_costs}, $px_user::SET_CREDITS_SUB);

  my $item_ids = $items->{cargo_ids} . $item_id . ",";
  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    $stock_ores = px_ore::set_ore ($stock_ores, $i, px_ore::get_ore ($stock_ores, $i) - px_ore::get_ore ($item_ores, $i));
  }
  px_planet::update_ores ($planet->{id}, "", $stock_ores);


  # Add the building to the queue
  px_ef_item::execute_item_function ("QUEUE_IN", $item_id, $planet_id);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO h_queue (type, ticks, building_id, planet_id, user_id) VALUES (?, ?, ?, ?, ?)", constants->QUEUE_INVENTION, $item->{ticks}, $item_id, $planet_id, $user->{user_id});

  item_add ($tx, "status", "STATUS_OK");
  return;
}

# -------------------------------------------------------------------------
# Returns the number of items $item_id currently on planet $planet_id
sub items_total () {
  my ($planet_id, $item_id) = @_;

  my $items = px_planet::get_planet_items ($planet_id);
  my $count = () = $items->{cargo_ids} =~ /$item_id/g;
  return $count;
}

# -------------------------------------------------------------------------
# Returns 0 if item dependencies failes, 1 otherwise
sub check_manufacturing_dependencies () {
  my ($planet_id, $item_id) = @_;

  my ($sth, $tmp, $dependencies, $dep, $building);

  my $tmp = px_item::get_item ($item_id);
  my @dependencies = split (",", $tmp->{csl_depends});
  my $surface = px_planet::get_surface ($planet_id);

  foreach $dep (@dependencies) {
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

