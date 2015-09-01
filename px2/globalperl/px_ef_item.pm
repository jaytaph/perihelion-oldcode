package px_ef_item;
use strict;

# -------------------------------------------------------------------------------------------------------
#   Standard function flow of a building
#      QUEUE_IN         Item enters the queue list and waits for being build
#      QUEUE_OUT        Item is constructed and leaves the queue
#      PLANET_INIT_0    Global item initialaztion function for item on the planet is called
#      PLANET_INIT_X    Specific item initialaztion function for item on the planet is called
#      INIT_X           Specific building initialization function is called next
#
#      TICK_VESSEL_0    Global building functions executed each tick
#      TICK_VESSEL_X    Specific building functions executed each tick
#      TICK_PLANET_0    Global building functions executed each tick
#      TICK_PLANET_X    Specific building functions executed each tick
#
#      FINI_0           Global building destruction is called
#      FINI_X           Specific building destruction is called
#
# -------------------------------------------------------------------------------------------------------


# -------------------------------------------------------------------------------------------------------
# We need to use AUTOLOAD to catch undefined functions.... crap...
sub AUTOLOAD {
}

# -------------------------------------------------------------------------------------------------------
# context "TICK"          executed each tick
# context "QUEUE_IN"      entry added to queue
# context "QUEUE_OUT"     entry deleted out queue
# context "INIT"          entry manufactured
# context "FINI"   entry destroyed
sub execute_item_function {
  my $context = shift @_;
  my $item = shift @_;

  # There is a 'second' context on a tick function.. An item can be on the vessel, or on the planet.
  if ($context eq "TICK") {
    my $tmp = shift @_;
    if ($tmp == constants->TICK_ITEM_ON_PLANET) { $context .= "_planet"; }
    if ($tmp == constants->TICK_ITEM_ON_VESSEL) { $context .= "_vessel"; }
  }

  my (@paramlist) = @_;

  # And place the item to it
  my $tmp = "px_ef_item::".lc($context)."_".$item;
  if (lc($context) ne "tick_planet" and lc($context) ne "tick_vessel") { print "Executing: $tmp\n"; }

  no strict 'refs';
  return &{$tmp} (@paramlist);
  use strict 'refs';
}

# -------------------------------------------------------------------------------------------------------
# Executed for every item that is put onto a planet
sub init_0 {
  my $planet_id = shift;
  my $item_id = shift;
  my $user_id = shift;

  # Add the item to the list of items on the planet...
  my $planetitems = px_planet::get_planet_items ($planet_id);
  my $item_ids = add_to_list ($item_id, $planetitems->{cargo_ids});
  px_planet::set_surface_cargo ($planet_id, $item_ids);
#  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_surface SET cargo_ids=? WHERE planet_id=?", $item_ids, $planet_id);

  # And mail the user...
  my $planet      = px_planet::get_planet ($planet_id);
  my $item        = px_mysql::get_item ($item_id);
  px_message::create_message (px_message->MSG_USER, $user_id, "Construction", "Item ready",
                              "The construction crew on planet ".$planet->{name}." inform you of the completion of a new ".$item->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);

  # TODO: add upkeep stufu
}

# Executed for every item that is taken from a planet
sub fini_0 {
    my ($item_id, $planet_id, $vessel_id) = @_;

    # TODO: Adjust planet's upkeep
}


# -------------------------------------------------------------------------------------------------------
# Item WORMHOLE_STABILIZER
sub planet_init_16 {
  my $planet_id = shift;

  my $planet = px_planet::get_planet ($planet_id);

  if (px_wormhole::is_wormhole ($planet_id)) {
    px_wormhole::stabilize_wormhole ($planet);
  }
}

sub planet_fini_16 {
  my $planet_id = shift;

  my $planet = px_planet::get_planet ($planet_id);

  if (px_wormhole::is_wormhole ($planet_id)) {
    px_wormhole::unstabilize_wormhole ($planet);
  }
}


# -------------------------------------------------------------------------------------------------------
# Item MINE_SWEEPER
sub tick_vessel_14 {
  my ($vessel) = @_;

  my ($sth, $planet);

  # If we aren't in orbit, set the counter to 10 again...
  if ($vessel->{status} ne "ORBIT") {
    $vessel->{sweep_tick} = 10;
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET sweep_tick=? WHERE id=?", $vessel->{sweep_tick}, $vessel->{id});
    return;
  }

  # If we are the same user, make sure we don't sweep
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE id=".$vessel->{planet_id});
  $planet = px_mysql::fetchhash ($sth);
  if ($vessel->{user_id} == $planet->{user_id}) {
    return;
  }

#  # TODO: check also we only sweep at enemy planets.
#  print "We are orbitting ".$planet->{name}."\n";
#  print "User of it is: ".$planet->{user_id}."\n";

  # Decrease sweep_tick counter
  $vessel->{sweep_tick} = $vessel->{sweep_tick} - 1;

  # Time for a sweep?
  if ($vessel->{sweep_tick} == 0) {
    # TODO: check if we have orbit mines on planet
    # TODO: remove one orbit mine from surface

    # Send a message to the user of the planet and the vessel.
    px_message::create_message (px_message->MSG_USER, $vessel->{user_id}, "Captain of the vessel ".$vessel->{name}, "Mine sweeped on planet ".$planet->{name},
	                  		 		    "The captain of vessel ".$vessel->{name}." has successfully removed a mine orbiting the planet ".$planet->{name}.".",
	                  		 		    constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_VESSEL);
    px_message::create_message (px_message->MSG_USER, $planet->{user_id}, "President of planet ".$planet->{name}, "Mine sweeped on planet ".$planet->{name},
	  		              		 	    "The president of planet ".$planet->{name}." reports a removal of a mine orbiting the planet. The vessel who has sweeped the mine is ".$vessel->{name},
	  		              		 	    constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);

    # And reset the sweep counter
    $vessel->{sweep_tick} = 10;
  }

  # Update the sweep counter
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET sweep_tick=? WHERE id=?", $vessel->{sweep_tick}, $vessel->{id});
}



return 1;