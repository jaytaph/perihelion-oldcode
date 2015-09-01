use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#   Mines ores from a planet.
#
#   Do all minable planets:
#     Mine random ammount of ore, multiply if mining droids are present.
#     Make sure we don't mine more than available in mines.
#     If nothing is left to mine, set the minable flag of planet to zero
#     Add ores to the stock on the planet.
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
# Extract ores from the planets depending on the miner droids
sub do_mining () {
  my ($sth, $planet);

  my $loop_id = px_planet::init_get_all_minable_planets ();
  while ($planet = px_planet::get_next_entry ($loop_id)) {
    my $planet_id  = $planet->{id};
    my $ores       = px_planet::get_ores ($planet_id);
    my $itemlist   = px_planet::get_planet_items ($planet_id);
	  my $droids     = px_csl::count_items ($itemlist->{cargo_ids}, constants->I_DROIDS);

    # Add ores, but for every droid we have, we add even more ores...
    my @new_ores = ();
    for (my $j = 0; $j != px_ore::get_ore_count(); $j++) {
	    $new_ores[$j] = 0;
      for (my $i = 0; $i != $droids+1; $i++) {
  	    $new_ores[$j] += int (rand ($px_config::config->{h_ores_rnd})) + $px_config::config->{h_ores_min};
  	  }
  	}

    # Add the ores to the current ores on the planet
  	for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
  	  my $tmp = px_ore::get_ore ($ores->{cur_ores}, $i);
  	  $ores->{cur_ores} = px_ore::set_ore ($ores->{cur_ores}, $i, $tmp+$new_ores[$i]);
    }

    # Make sures our current ores cannot exceed the maximum ores on the planet
	  my $no_more_ore = 0;
  	for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
  	  my $cur = px_ore::get_ore ($ores->{cur_ores}, $i);
  	  my $max = px_ore::get_ore ($ores->{max_ores}, $i);

      # If we reached max (or are above), set it to max
  	  if ($cur > $max) { $cur = $max; $new_ores[$i] = 0; $no_more_ore++; }
  	  $ores->{cur_ores} = px_ore::set_ore ($ores->{cur_ores}, $i, $cur);

      # And off course, we mined some stuff from the planet, and we store it on the planet,
  	  my $tmp = px_ore::get_ore ($ores->{stock_ores}, $i);
  	  $ores->{stock_ores} = px_ore::set_ore ($ores->{stock_ores}, $i, $tmp + $cur);
  	}

    # The planet is empty, set MINE to 0 again so it won't be used in the queue
  	if ($no_more_ore == px_ore::get_ore_count()) {
  	  px_planet::set_mining ($planet_id, constants->CAN_MINE_NOTHING);
  	}

    # And update it the ores..
	  px_planet::update_ores ($planet_id, $ores->{cur_ores}, $ores->{stock_ores});
  }
  px_planet::fini_loop ($loop_id);
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#