use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# =================================================================================================
# Let planets discover other anomalies in the sector or the galaxy...
sub do_exploration () {
  my ($sth2, $user, $planet, $found, $planet_id, $anomalycount);

  # Only get the planets where we can discover...
  my $loop_id = px_planet::init_get_all_explorable_planets ();
  while ($planet = px_planet::get_next_entry ($loop_id)) {

    # Get the user from this planet
	  $user = px_user::get_user ($planet->{user_id});

    # Don't explore when it's not time yet..
    if ($user->{explore_level} < $planet->{next_explore}) { next; }

    # Create a new 'exploration date'
    my $next_level = $user->{explore_level} + $px_config::config->{h_explore_min} + int(rand($px_config::config->{h_explore_rnd}));
    px_planet::set_next_exploration_level ($planet->{id}, $next_level);

    # Don't explore anything if it's 0, this means that we just created the building in the last tick.
    if ($planet->{next_explore} == 0) { next; }


	  # Discover the planet, just because we can (thanks to a exploration station)
    if (rand(10) > 4 and $planet->{can_explore} == constants->CAN_EXPLORE_SURFACE) {
      discover_surface ($planet->{id});
	    next;
  	}



    $planet_id = 0;

    # To make sure the function always returns a id that already doesn't exist,
    # we get a random number from the number of planets in the sector. But
    # we only count the planets we haven't already discovered. This way, we
    # always get a undiscovered random planet in the first pass...
    # Notice that pre-warp (only discover in current sector) and warp (discover
    # all planets) are merged.

    my $planetlist = px_anomaly::get_user_anomalies ($user->{user_id});
	  my $nr_of_anomalies = px_csl::count_list ($planetlist->{planet_ids});
	  $nr_of_anomalies += px_csl::count_list ($planetlist->{undiscovered_ids});

	  if ($user->{warp} == 0) {
  	  # Get number of total planets
      $anomalycount = px_sector::get_anomaly_count ();

      # The number of planets visible by the user is the same as the number
      # of planets in the sector, this means: nothing left to discover :)
      if ($nr_of_anomalies+1 == $anomalycount) { next; }

      # We discover our planets in chronological order when we are still in our sector
      my $loop_id2 = px_planet::init_get_all_planets_from_sector ($user->{sector_id});
      while (my $target_planet = px_planet::get_next_entry ($loop_id2)) {
	      if (not px_csl::in_list ($planetlist->{planet_ids}, $target_planet->{id}) and
		        not px_csl::in_list ($planetlist->{undiscovered_ids}, $target_planet->{id})) {
	        $planet_id = $target_planet->{id};
  	      last;
   	  	}
	    }
	    px_planet::fini_loop ($loop_id2);
	  } else {
      # Get number of total planets
      $anomalycount = px_anomaly::get_total_anomaly_count();

      # The number of planets visible by the user is the same as the number
      # of planets in the sector, this means: nothing to discover :)
      if ($nr_of_anomalies+1 == $anomalycount) { next; }

	    # Find a random planet, in a random sector...

      # Get the $rnd'th planet that isn't discovered already
      my $rnd = int(rand($anomalycount)+1);
      $planet_id = 0;

      while (1) {
        my $loop_id2 = px_planet::init_get_all_planets ();
        while ($tmp = px_planet::get_next_entry ($loop_id2)) {
	        # Not found in the array? Decrease the random count
	        if (not px_csl::in_list ($planetlist->{planet_ids}, $tmp->{id}) and
		          not px_csl::in_list ($planetlist->{undiscovered_ids}, $tmp->{id})) {
		        $rnd--;
		      }

	        if ($rnd <= 0) {
	          $found = 1;
	          $planet_id = $tmp->{id};
	          last;
	        }
	      }
        px_planet::fini_loop ($loop_id2);
	      if ($found == 1) { last; }
	    } #  while (1)
    }

    # No planet id found... :(
    if ($planet_id == 0) { next; }

    # Make sure we don't discover 'private' sectors
	  my $tmp = px_mysql::get_planet ($planet_id);
    my $sector = px_mysql::get_sector ($tmp->{sector_id});
	  if ($sector->{private} == 1 and $sector->{user_id} != $planet->{user_id}) { next; }

    # Add the new planet to the users undiscovered list
    my $undiscovered_ids = px_csl::add_to_list ($planetlist->{undiscovered_ids}, $planet_id);
    my $planet_ids = $planetlist->{planet_ids};
    px_user::set_nomalies ($user->{user_id}, $planet_ids, $undiscovered_ids);


  	# And add the sector to the users discovered list if necessary
    my $sectorlist = px_sector::get_sectors_from_user ($user->{user_id});
	  my $newplanet  = px_planet::get_planet ($planet_id);
	  if (not px_csl::in_list ($sectorlist->{sector_ids}, $newplanet->{sector_id})) {
      $sectorlist->{sector_ids} = px_csl::add_to_list ($sectorlist->{sector_ids}, $newplanet->{sector_id});
      px_user::set_known_sectors ($user->{user_id}, $sectorlist->{sector_ids});
	  }

    # And notify the user
    my $type = px_anomaly::get_type ($newplanet->{id});

    if ($user->{warp} == 0) {
	    px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Exploration", "New $type discovered",
	                   					    "The observatory on planet ".$planet->{name}." has discovered a new ".$type." in the sector.",
	                   				      constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_EXPLORATION);
    } else {
  	  px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Exploration", "New $type discovered",
	  	             			    		  "The observatory on planet ".$planet->{name}." has discovered a new ".$type." in the galaxy.",
	                  				      constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_EXPLORATION);
    }
  }
  px_planet::fini_loop ($loop_id);
}


# =================================================================================================
# Discovers the surface of an anomaly. Basicly putting a random item
# from g_planets.undiscovered_ids into g_planets.planet_ids.
#
sub discover_surface ($) {
  my $planet_id = shift;

  # Get standard info
  my $planet = px_planet::get_planet ($planet_id);
  my $user = px_user::get_user ($planet->{user_id});

  # Get the list of undiscovered planets
 	my $anomalylist = px_anomaly::get_user_anomalies ($user->{user_id});

	# Don't discover when there's nothing left to discover...
  if ($anomalylist->{undiscovered_ids} eq "") { return; }

  # Find a random item, remove it from the undiscovered list and place it into the discovered list
	my $anomaly_id = px_csl::get_random_item ($anomaly_list->{undiscovered_ids});
	my $undiscovered_csl = px_csl::remove_from_list ($anomalylist>{undiscovered_ids}, $planet_id);
	my $discovered_csl = px_csl::add_to_list ($anomalylist>{planet_ids}, $planet_id);
  px_anomaly::set_user_anomalies ($user->{user_id}, $discovered_csl, $undiscovered_csl);

  # Let the user know we discovered something...
  my $dst_anomaly = px_anomaly::get_anomaly ($anomaly_id);
  my $dst_sector = px_sector::get_sector ($dst_planet->{sector_id});

  if (px_anomaly::is_planet ($dst_planet)) {
    px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Exploration", "Surface of unknown planet discovered",
 		              		 		      "The exploration station on planet ".$planet->{name}." has scanned the surface of planet ".$dst_anomaly->{name}." in sector ".$dst_sector->{name}.".",
 		              		 		      constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_EXPLORATION);
  } else {
    my $type = px_anomaly::get_type (dst_anomaly->{id});
    px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Exploration", $type." scanned",
 		              		 		      "The exploration station on planet ".$planet->{name}." has scanned the ".$type." ".$dst_anomaly->{name}." in sector ".$dst_sector->{name}.".",
 		              		 		      constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_EXPLORATION);
  }

}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#