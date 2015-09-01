use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
# Check all vessels for items. Check all items and see if they need work...
sub do_items {
  my ($vessel, $planet, $item, $surface, $cargo);

  # Do the items on the vessels
  my $loop_id = px_vessel::init_get_all_vessel_items ();
  while ($cargo = px_vessel::get_next_entry ($loop_id)) {
    if ($cargo->{cargo_ids} eq "") { next; }
    $vessel = px_vessel::get_vessel ($cargo->{vessel_id});

    foreach $item (split (/,/, $cargo->{cargo_ids})) {
      px_ef_item::execute_item_function ("TICK", $item, constants->TICK_ITEM_ON_VESSEL, $vessel);
    }
  }
  px_vessel::fini_loop ($loop_id);


  # Do the items on the planets
  my $loop_id = px_planet::init_get_all_surface_items ();
  while ($surface = px_planet::get_next_entry ($loop_id)) {
    if ($surface->{cargo_ids} eq "") { next; }
    $planet = px_planet::get_planet ($surface->{planet_id});

    foreach $item (split (/,/, $surface->{cargo_ids})) {
      px_ef_item::execute_item_function ("TICK", $item, constants->TICK_ITEM_ON_PLANET, $planet);
    }
  }
  px_planet::fini_loop ($loop_id);
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#