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
# Build everything in the queue
#
sub do_queue () {
  my ($sth, $ticks, $queue);

  $handle = px_queue::init_get_all_entries ();
  while ($queue = px_queue::get_next_entry ($handle)) {

    # Decrease tick count
	  $queue->{ticks} = $queue->{ticks} - 1;
    px_queue::set_ticks ($queue->{id}, $queue->{ticks});

    # We always do these functions... they will take care of the tick-count
	  if ($queue->{type} eq constants->QUEUE_FLIGHT) { move_vessel ($queue); }

    # If not at the end of our tick count, go to the next...
	  if ($queue->{ticks} > 0) { next; }

    # Delete the entry from the queue when the tickcount is 0
    px_queue::delete_entry ($queue->{id});

	  # Get the type of entry that's in the queue, act accordingly
	  if ($queue->{type} eq constants->QUEUE_UPGRADE)   {
	    upgrade_vessel ($queue->{vessel_id}, $queue->{user_id});
	  }
    if ($queue->{type} eq constants->QUEUE_VESSEL)    {
      px_ef_vessel::execute_vessel_function ("QUEUE_OUT", $queue->{vessel_id}, $queue);
      px_ef_vessel::execute_vessel_function ("INIT", constants->VESSEL_GENERIC, $queue->{vessel_id}, $queue->{planet_id}, $queue->{user_id});
      px_ef_vessel::execute_vessel_function ("INIT", $queue->{vessel_id}, $queue->{planet_id}, $queue->{user_id});
    }
    if ($queue->{type} eq constants->QUEUE_BUILD)     {
      px_ef_building::execute_building_function ("QUEUE_OUT", $queue->{building_id}, $queue);
      px_ef_building::execute_building_function ("INIT", constants->BUILDING_GENERIC, $queue->{building_id}, $queue->{planet_id}, $queue->{user_id});
      px_ef_building::execute_building_function ("INIT", $queue->{building_id}, $queue->{planet_id}, $queue->{user_id});
    }
    if ($queue->{type} eq constants->QUEUE_INVENTION) {
      px_ef_item::execute_item_function ("QUEUE_OUT", $queue->{building_id}, $queue);
      px_ef_item::execute_item_function ("PLANET_INIT", constants->ITEM_GENERIC, $queue->{building_id}, $queue->{planet_id}, $queue->{user_id});
      px_ef_item::execute_item_function ("PLANET_INIT", $queue->{building_id}, $queue->{planet_id}, $queue->{user_id});
    }
  }
  px_queue::fini_loop ($handle);
}



# -------------------------------------------------------------------------
# Upgrade item
sub upgrade_vessel ($$) {
  my $vessel_id = shift;
  my $user_id = shift;
  return;
}

# -------------------------------------------------------------------------
sub move_vessel () {
  my $queue = $_[0];
  my ($sth, $p_ids, $u_ids);

  # Update the position of the ship
  px_vessel::set_distance_and_angle ($queue->{vessel_id}, $queue->{delta_distance}, $queue->{delta_angle}, px_vessel->SET_VESSEL_DA_ADD);

  # Return when we are not done...
  if ($queue->{ticks} > 0) { return; }

  # Get info
  my $user_id   = $queue->{user_id};
  my $planet_id = $queue->{planet_id};
  my $vessel    = px_vessel::get_vessel ($queue->{vessel_id});

  # Check if the ship is in a traderoute, if so, then don't show any flight-messages.
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT id FROM a_trades WHERE vessel_id=?", $vessel->{id});
  my $in_traderoute = $sth->rows;


  # No destination sector and no destination angle means we arrived into outer space... somewhere...
  if ($vessel->{dst_planet_id} == 0 and $vessel->{dst_sector_id} == 0) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='SPACE', dst_angle=0, dst_distance=0, dst_planet_id=0, dst_sector_id=0, sector_id=0, planet_id=0 WHERE id=?", $queue->{vessel_id});
    px_vessel::set_distance_and_angle ($queue->{vessel_id}, $vessel->{dst_distance}, $vessel->{dst_angle}, px_vessel->SET_VESSEL_DA_ABS);
    px_ef_vessel::execute_vessel_function ("STOPMOVE", $vessel->{id});

    px_message::create_message (px_message->MSG_USER, $user_id, "Ships captain", "Arriving at destination",
                                "The captain of vessel ".$vessel->{name}." informs you of the arrival at destination in outer space.",
                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_VESSEL);

  } elsif ($vessel->{dst_planet_id} == 0 and $vessel->{dst_sector_id} != 0) {
    # Just entered a sector
    my $sector = px_sector::get_sector ($vessel->{dst_sector_id});

    # Status  of the vessel is bording sector
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='SPACE', dst_angle=0, dst_distance=0, dst_planet_id=0, dst_sector_id=0, sector_id=?, planet_id=0 WHERE id=?", $vessel->{dst_sector_id}, $queue->{vessel_id});
    px_vessel::set_distance_and_angle ($queue->{vessel_id}, $sector->{distance}, $sector->{angle}, px_vessel::SET_VESSEL_DA_ABS);
    px_ef_vessel::execute_vessel_function ("BORDERING", $vessel->{vessel_id});

  	if (not $in_traderoute) {
      px_message::create_message (px_message->MSG_USER, $user_id, "Ships captain", "Arriving at sector border",
                                  "The captain of vessel ".$vessel->{name}." informs you of the arrival at the border of sector ".$sector->{name},
                                  constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_VESSEL);
	  }

    # If the sector is unclaimed, claim it...
    if ($sector->{user_id} == 0) { px_sector::set_owner ($sector_id, $user_id); }

  } elsif ($vessel->{dst_planet_id} != 0) {
    # Just entered orbit of a planet

    px_ef_vessel::execute_vessel_function ("ORBITING", $vessel->{vessel_id});

    my $planet = px_planet::get_planet ($vessel->{dst_planet_id});
	  my $sector = px_sector::get_sector ($planet->{sector_id});

    # Check if the planet is in the undiscovered list, then remove it
    # from there and place it in the planet_ids
    my $planetlist = px_user::get_anomaly_list ($user_id);

	  if (px_csl::in_list ($planetlist->{undiscovered_ids}, $planet_id)) {
      my $planet_ids = px_csl::add_to_list ($planetlist->{planet_ids}, $planet_id);
	    my $undiscovered_ids = px_csl::remove_from_list ($planetlist->{undiscovered_ids}, $planet_id);
      px_user::set_anomaly_lists ($user_id, $planet_ids, $undiscovered_ids);
    }

    if (px_anomaly::is_wormhole ($planet)) {
 	    px_wormhole::move_through_wormhole ($vessel->{id}, $planet->{id});

    } elsif (px_anomaly::is_blackhole ($planet)) {
      px_blackhole::move_into_blackhole ($vessel->{id}, $planet->{id});

 	  } else {
   	  # Set the vessel's distance and angle
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET sun_distance=? WHERE id=?", $planet->{distance}, $queue->{vessel_id});
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='ORBIT', sector_id=?, planet_id=? WHERE id=?", $sector->{id}, $vessel->{dst_planet_id}, $queue->{vessel_id});
      if (not $in_traderoute) {
 	      px_message::create_message (px_message->MSG_USER, $user_id, "Ships captain", "Arriving at destination",
                                    "The captain of vessel ".$vessel->{name}." informs you of the arrival at ".$planet->{name}." in the sector ".$sector->{name},
                                    constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_VESSEL);
      }
    }
  }
}


# -------------------------------------------------------------------------
# Create vessel
sub obsolete_create_vessel () {
  my ($planet_id, $vessel_id, $user_id) = @_;

  # Get info
  my $planet = px_planet::get_planet ($planet_id);
  my $vessel = px_vessel::get_vessel ($vessel_id);

  # Set the vessel status as created and mail the user
  px_vessel::set_vessel_ready ($vessel_id);

  px_message::create_message (px_message->MSG_USER, $vessel->{user_id}, "Construction", "New vessel ready",
                              "The engineers on planet ".$planet->{name}." inform you of the completion of a new vessel named ".$vessel->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);
}

# -------------------------------------------------------------------------
# Create building
sub obsolete_create_building () {
  my $planet_id = shift;
  my $building_id = shift;
  my $user_id = shift;

  # Get info
  my $surface  = px_planet::get_surface ($planet_id);
  my $building = px_building::get_building ($building_id);
  my $planet   = px_planet::get_planet ($planet_id);
  my $user     = px_user::get_user ($user_id);

  # Add the building to the list of buildings on the surface...
  my $building_ids = add_to_list ($building_id, $surface->{building_ids});
  px_planet::set_surface_buildings ($planet_id, $building_ids);

  # And mail the user...
  px_message::create_message (px_message->MSG_USER, $user_id, "Construction", "Building ready",
                              "The construction crew on planet ".$planet->{name}." inform you of the completion of a new ".$building->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);
}




# -------------------------------------------------------------------------
# Create item
sub obsolete_create_item ($$$) {
  my $planet_id = shift;
  my $item_id = shift;
  my $user_id = shift;

  # Get info
  my $planetitems = px_planet::get_planet_items ($planet_id);
  my $item        = px_mysql::get_item ($item_id);
  my $planet      = px_planet::get_planet ($planet_id);
  my $user        = px_user::get_user ($user_id);

  # Add the item to the list of items on the planet...
  my $item_ids = add_to_list ($item_id, $planetitems->{cargo_ids});
  px_planet::set_surface_cargo ($planet_id, $item_ids);

  # And mail the user...
  px_message::create_message (px_message->MSG_USER, $user_id, "Construction", "Item ready",
                              "The construction crew on planet ".$planet->{name}." inform you of the completion of a new ".$item->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);
}




return 1;

#
# vim: ts=4 syntax=perl nowrap
#