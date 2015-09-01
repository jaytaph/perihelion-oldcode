use strict;
use lib '../globalperl/';
use px_ef_item;
BEGIN { require ('constants.pm'); }

$main::cargo1_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub cargo1 () {
  my ($rx, $tx) = @_;
  my ($sth, $tmp, $planet, $vessel, $planet_id, $vessel_id, $item_id, $planet_items, $vessel_weaponry, $vessel_items);

  item_add ($tx, "pkg_cmd", "CARGO1");

  $planet_id = $rx->{pid};
  $vessel_id = $rx->{vid};
  $item_id   = $rx->{iid};

  # No decent params found...
  if ($vessel_id eq "" or $planet_id eq "" or $item_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  $tmp = px_planet::get_surface ($planet_id);
  $planet_items = $tmp->{cargo_ids};

  $tmp = px_vessel::get_vessel_items ($vessel_id);
  $vessel_items = $tmp->{cargo_ids};
  $vessel_weaponry = $tmp->{weapon_ids};

  if (px_csl::in_list ($vessel_items, $item_id)) {
    $vessel_items = px_csl::remove_from_list ($vessel_items, $item_id);
  } else {
    $vessel_weaponry = px_csl::remove_from_list ($vessel_weaponry, $item_id);
  }
  $planet_items    = px_csl::add_to_list ($planet_items, $item_id);

  px_vessel::set_cargo ($vessel_id, $vessel_items);
  px_vessel::set_weaponry ($vessel_id, $vessel_weaponry);
  px_planet::set_surface_cargo ($planet_id, $planet_items);
#  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_surface SET cargo_ids=? WHERE planet_id=?", $planet_items, $planet_id);


  # Ok, if we need to do something special when deploying an item into the orbit of a planet, do it here...
  $planet = px_planet::get_planet ($planet_id);
  $vessel = px_vessel::get_vessel ($vessel_id);

  # Remove item from planet and add item to vessel
  px_ef_item::execute_item_function ("VESSEL_FINI", $item_id, $vessel_id);
  px_ef_item::execute_item_function ("PLANET_INIT", $item_id, $planet_id);

  item_add ($tx, "status", "STATUS_OK");
  return;
}



return 1;
#
# vim: ts=4 syntax=perl nowrap
#

