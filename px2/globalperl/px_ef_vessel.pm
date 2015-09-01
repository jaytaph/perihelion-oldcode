package px_ef_vessel;
use strict;


# -------------------------------------------------------------------------------------------------------
#   Standard function flow of a building
#      QUEUE_IN         Building enters the queue list and waits for being build
#      QUEUE_OUT        Building is constructed and leaves the queue
#      INIT_0           Global building initialization function is called
#      INIT_X           Specific building initialization function is called next
#
#      TICK_0           Global building functions executed each tick
#      TICK_X           Specific building functions executed each tick
#
#      FINI_0           Global building destruction is called
#      FINI_X           Specific building destruction is called
#
# -------------------------------------------------------------------------------------------------------
#   Other defined functions
#      BORDERING        vessel is bordering a sector
#      STARTMOVE        vessel starts moving
#      STOPMOVE         vessel stops moving
#      ORBITING         vessel enters an orbit
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

# context "STARTMOVE"     start moving
# context "STOPMOVE"      stop moving
# context "BORDERING"     bordering sector
# context "ORBITING"      orbiting sector
sub execute_vessel_function {
  my $context = shift @_;
  my $item = shift @_;
  my (@paramlist) = @_;

  # And place the item to it
  my $tmp = "px_ef_vessel::".lc($context)."_".$item;
  if (lc($context) ne "tick") { print "Executing: $tmp\n"; }


  no strict 'refs';
  return &{$tmp} (@paramlist);
  use strict 'refs';
}

# -------------------------------------------------------------------------------------------------------
# Item 0 is executed for each item
sub init_0 {
  my $vessel_id = shift;
  my $planet_id = shift;
  my $user_id = shift;

  print "Global vessel init  (vessel type: ".$vessel_id.")\n";

  # Set the vessel status as created and mail the user
  px_vessel::set_vessel_ready ($vessel_id);

  # Send message
  my $planet = px_planet::get_planet ($planet_id);
  my $vessel = px_vessel::get_vessel ($vessel_id);
  px_message::create_message (px_message->MSG_USER, $user_id, "Construction", "New vessel ready",
                              "The engineers on planet ".$planet->{name}." inform you of the completion of a new vessel named ".$vessel->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);
}

sub fini_0 {
  my ($vessel_id) = @_;

  print "Global vessel fini  (vessel type: ".$vessel_id.")\n";
}



# -------------------------------------------------------------------------------------------------------
# ADVANCED_EXPLORATION vessel discoveres ALL planets and anomalies when bordering a sector.
sub bordering_2 {
  my ($vessel_id) = @_;

  my ($vessel, $user, $sth, $count, $undiscovered_ids, $planet_ids, $planet, $sector, $tmp);

  my $vessel = px_vessel::get_vessel ($vessel_id);
  my $user = px_user::get_user ($vessel->{user_id});
  my $sector = px_sector::get_sector ($vessel->{sector_id});

  # Check if the one of the planets is in the undiscovered list, then remove it
  # from there and place it in the planet_ids
  $sth = px_mysql::query (constants->QUERY_NOKEEP, "SELECT * FROM g_planets WHERE user_id=?", $user->{id});
  $tmp = px_mysql::fetchhash ($sth);
  $undiscovered_ids = $tmp->{undiscovered_ids};
  $planet_ids = $tmp->{planet_ids};
  $count = 0;

  # Get all planets from this sector and place them in the discovered status
  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE sector_id=?", $sector->{id});
  while ($planet = px_mysql::fetchhash ($sth)) {
	  if (px_csl::in_list ($undiscovered_ids, $planet->{id})) {
	    $undiscovered_ids = px_csl::remove_from_list ($undiscovered_ids, $planet->{id});
    }
    if (! px_csl::in_list ($planet_ids, $planet->{id})) {
 	    $planet_ids = px_csl::add_to_list ($planet_ids, $planet->{id});
      $count++;
    }
  }
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_planets SET planet_ids=?, undiscovered_ids=? WHERE user_id=?", $planet_ids, $undiscovered_ids, $user->{id});

  # Did we explore any planets or anomalies? If so, tell it to the user
  if ($count > 0) {
    px_message::create_message (px_message->MSG_USER, $user->{id}, "Ships captain", "Discovered new planets",
                                "The captain of vessel ".$vessel->{name}." informs you of the discovery of ".$count." new planets and/or anomalies in sector ".$sector->{name},
                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_VESSEL);
  }
}


return 1;