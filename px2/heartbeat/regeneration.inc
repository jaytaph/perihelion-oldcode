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
sub do_regeneration {
  my ($vessel, $planet, $surface, $ticks);

  # -- Regenerate the planets
  my $loop_id = px_planet::init_get_all_planets ();
  while ($planet = px_planet::get_next_entry ($loop_id)) {
    # Check if we need to regenerate
    if ($planet->{cur_strength} == $planet->{max_strength}) { next; }

    # Regenerate 1 tick
    $ticks = 1;

    # Add the ticks. If too much, than clip it to the maximum value...
    $planet->{cur_strength} = $planet->{cur_strength} + $ticks;
    if ($planet->{cur_strength} > $planet->{max_strength}) {
      $planet->{cur_strength} = $planet->{max_strength};
    }

    # Update it...
    px_planet::set_strength ($planet->{id}, $planet->{cur_strength});
  }
  px_planet::fini_loop ($loop_id);


  # -- Regenerate the vessels
  my $loop_id = px_vessel::init_get_all_vessels ();
  while ($vessel = px_vessel::get_next_entry ($loop_id)) {
    # Check if we need to regenerate
    if ($vessel->{cur_strength} == $vessel->{max_strength}) { next; }

    # Regenerate 1 tick
    $ticks = 1;

    # Check if we are on a vessel station, starbase or
    if ($vessel->{status} eq 'ORBIT') {
      # Check if it's a starbase or a planet,
      $planet = px_planet::get_planet ($vessel->{planet_id});

      if ($planet->{type} eq 'P') {
        # Now, check if we have a vesselsation or a spacedock on the planet...
        $surface = px_planet::get_surface ($vessel->{planet_id});

        if (px_csl::in_list ($surface->{building_ids}, constants->B_VESSELSTATION)) {
          $ticks = $ticks + 1;
        }
        if (px_csl::in_list ($surface->{building_ids}, constants->B_SPACEDOCK)) {
          $ticks = $ticks + 1;
        }
      }

      # Check if it's a starbase, which means 5 ticks
      if ($planet->{type} eq 'S') {
        $ticks = 5;
      }
    }

    # Add the ticks. If too much, than clip it to the maximum value...
    $vessel->{cur_strength} = $vessel->{cur_strength} + $ticks;
    if ($vessel->{cur_strength} > $vessel->{max_strength}) {
      $vessel->{cur_strength} = $vessel->{max_strength};
    }

    # Update it...
    px_vessel::set_strength ($vessel->{id}, $vessel->{cur_strength});
  }
  px_vessel::fini_loop ($loop_id);

}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#