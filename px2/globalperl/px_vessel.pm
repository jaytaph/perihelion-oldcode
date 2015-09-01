package px_vessel;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


use constant SET_VESSEL_DA_ABS    => 1;
use constant SET_VESSEL_DA_ADD    => 2;
use constant SET_VESSEL_DA_SUB    => 3;


# ===========================================================================================================
# Is_Battleship ()
#
# Description:
#   Checks if the vessel is of a battleship type
#
# ParamList
#   vessel_id       id of the vessel
#
# Returns:
#    ERR_TRUE     Vessel is a battleship
#    ERR_FALSE    Vessel is not a battleship
#
sub is_battleship ($) {
  my $vessel_id = shift;
  errors::assert (errors::is_value ($vessel_id));

  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_vessels WHERE id=?", $vessel_id);
  my $vesseltype = px_mysql::fetchhash ($sth);

  if ($vesseltype->{type} eq constants->VESSEL_TYPE_BATTLE) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}


# ===========================================================================================================
# set_vessel_ready ()
#
# Description:
#   Sets the vessel as being done.
#
# ParamList
#   vessel_id   Id of the vessel
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_vessel_ready ($) {
  my $vessel_id = shift;
  errors::assert (errors::is_value ($vessel_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET created=1 WHERE id=?", $vessel_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Strength ()
#
# Description:
#   Sets the strength of a vessel at a certain level
#
# ParamList
#   vessel_id   Id of the vessel
#   Strength    Strength to set
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_strength ($$) {
  my $vessel_id = shift;
  my $strength = shift;
  errors::assert (errors::is_value ($vessel_id));
  errors::assert (errors::in_range ($strength, 0, 100));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET cur_strength=? WHERE id=?", $strength, $vessel_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Angle_And_Distance ()
#
# Description:
#    Generates a new vessel
#
# ParamList
#
#     vessel_id     ID of the vessel
#     distance      Distance
#     angle         Angle
#     offset        SET_VESSEL_DA_*
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_distance_and_angle ($$$$) {
  my $vessel_id = shift;
  my $distance = shift;
  my $angle = shift;
  my $offset = shift;
  errors::assert (errors::is_value ($vessel_id));
  errors::assert (errors::is_value ($distance));
  errors::assert (errors::in_range ($angle, 0, 360000));
  errors::assert (errors::in_range ($offset, 1, 3));

  if ($offset == SET_VESSEL_DA_ABS) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET distance=?, angle=? WHERE id=?", $distance, $angle, $vessel_id);
  }
  if ($offset == SET_VESSEL_DA_SUB) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET distance=distance-?, angle=angle-? WHERE id=?", $distance, $angle, $vessel_id);
  }
  if ($offset == SET_VESSEL_DA_ADD) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET distance=distance+?, angle=angle+? WHERE id=?", $distance, $angle, $vessel_id);
  }
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Ores ()
#
# Description:
#   Sets the ores in a vessel
#
# ParamList
#     vessel_id       id of the vessel
#     vessel_ores     CSL of the ores
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_ores ($$) {
  my $vessel_id = shift;
  my $vessel_ores = shift;
  errors::assert (errors::is_value ($vessel_id));
  errors::assert (not errors::is_empty ($vessel_ores));


  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE i_vessels SET ores=? WHERE vessel_id=?", $vessel_ores, $vessel_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Weaponry ()
#
# Description:
#     Sets the weaponry in a vessel
#
# ParamList
#     vessel_id       id of the vessel
#     weaponry        CSL of the weapons
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_weaponry ($$) {
  my $vessel_id = shift;
  my $vessel_weaponry = shift;
  errors::assert (errors::is_value ($vessel_id));
  errors::assert (not errors::is_empty ($vessel_weaponry));


  if (not is_battleship ($vessel_id)) { return errors->ERR_NOT_A_BATTLESHIP; }

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE i_vessels SET weapon_ids=? WHERE vessel_id=?", $vessel_weaponry, $vessel_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Cargo ()
#
# Description:
#     Sets the cargo in a vessel
#
# ParamList
#     vessel_id       id of the vessel
#     cargo           CSL of the cargo
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_cargo ($$) {
  my $vessel_id = shift;
  my $vessel_items = shift;
  errors::assert (errors::is_value ($vessel_id));
  errors::assert (not errors::is_empty ($vessel_items));


  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE i_vessels SET cargo_ids=? WHERE vessel_id=?", $vessel_items, $vessel_id);
  return errors->ERR_OK;
}


# ===========================================================================================================
# Get_Sector_On_Coordinate ()
#
# Description:
#   Returns ERR_NOTHING_FOUND when no sector is found on coordinate, otherwise returns the sector id
#
# ParamList
#   distance        Distance of the coordinate
#   angle           Angle of the coordinate
#
# Returns:
#   ERR_NOTHING_FOUND   No sector found on the coordinate
#   sector_id           id of the sector found
#
sub get_sector_on_coordinate ($$) {
  my $distance = shift;
  my $angle = shift;
  errors::assert (errors::is_value ($distance));
  errors::assert (errors::in_range ($angle, 0, 360000));

    my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_sectors WHERE distance=? AND angle=?", $distance, $angle);
  if ($sth->rows == 0) {
    px_mysql::query_finish ($sth);
    return errors->ERR_NOTHING_FOUND;
  }

  my $tmp = px_mysql::fetchhash ($sth);
  my $sector = $tmp->{id};
  px_mysql::query_finish ($sth);

  # Return what we've found.
  return $sector;
}

# ===========================================================================================================
# Get_Vessel ()
#
# Description:
#   Returns vessel information
#
# ParamList
#   vessel_id     Id of the vessel
#
# Returns:
#   hash      Vessel information
#   ERR_*     Failure
#
my $sth_get_vessel = 0;
sub get_vessel ($) {
  my $vessel_id = shift;
  errors::assert (errors::is_value ($vessel_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_vessel == 0) {
    $sth_get_vessel = $px_mysql::dbhandle->prepare ("SELECT * FROM g_vessels WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_vessel->execute ($vessel_id) or die "Reason: $DBI::strerr";
  return $sth_get_vessel->fetchrow_hashref;
}


# ===========================================================================================================
# Get_Vessel_Items ()
#
# Description:
#   Returns vessel item information
#
# ParamList
#   vessel_id     Id of the vessel
#
# Returns:
#   hashref       hash reference with item information
#   ERR_*         Failure
#
my $sth_get_vessel_items = 0;
sub get_vessel_items ($) {
  my $vessel_id = shift;
  errors::assert (errors::is_value ($vessel_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_vessel_items == 0) {
    $sth_get_vessel_items = $px_mysql::dbhandle->prepare ("SELECT * FROM i_vessels WHERE vessel_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_vessel_items->execute ($vessel_id) or die "Reason: $DBI::strerr";
  return $sth_get_vessel_items->fetchrow_hashref;
}


# ===========================================================================================================
# Move()
#
# Description:
#    Generates a new vessel
#
# ParamList
#
#     vessel_id     ID of the vessel
#     distance      Distance for manual flight
#     angle         Angle for manual flight
#     sector        "yes"|"no"
#     planet_id     id of planet to travel
#     sector_id     id of sector to travel
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub move {
  my $param = @_[0];

  my $manual_flight;
  my ($delta_distance, $delta_angle, $ticks, $distance, $angle, $dst_planet_id, $dst_sector_id, $dst_planet, $dst_sector);

  # TODO
  # We should check if the vessel is an explorer and that the dst_id
  # is NOT on the planet_ids list, send NOEXPLORE if so...
  my $vessel_id  = $param->{vessel_id};
  my $vessel     = px_vessel::get_vessel ($vessel_id);
  my $src_planet = px_planet::get_planet ($vessel->{planet_id});
  my $src_sector = px_sector::get_sector ($vessel->{sector_id});
  my $user_id    = $vessel->{user_id};

  # Have we got a distance and angle? Then it's an manual flight
  if (defined ($param->{distance}) and defined ($param->{angle})) {
    $manual_flight = 1;
    $distance = $param->{distance};
    $angle = $param->{angle};
  } else {
    $manual_flight = 0;
  }

  if ($manual_flight == 1) {
    $ticks = calc_sector_ticks ($distance, $angle, $vessel->{distance}, $vessel->{angle}, $vessel->{warp});
    if (errors->is_error ($ticks)) { return errors->ERR_CANT_MOVE; }
    if ($ticks == 0) { $ticks = 1; }

    $delta_distance = ($distance - $vessel->{distance}) / $ticks;
    $delta_angle = ($angle - $vessel->{angle}) / $ticks;

    # if we find a sector on the same coordinates as we fly to, we basicly do a sector transfer
    $dst_sector_id = get_sector_on_coordinate ($distance, $angle);
    if ($dst_sector_id == errors->ERR_NOTHING_FOUND) { return errors->ERR_CANT_MOVE; }

    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET sun_distance=0, planet_id=0, sector_id=0 WHERE id=?", $vessel_id);
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET dst_distance=?, dst_angle=? WHERE id=?", $distance, $angle, $vessel_id);
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET dst_planet_id=0, dst_sector_id=?, status='FLYING' WHERE id=?", $dst_sector_id, $vessel_id);
    px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO h_queue (type, ticks, vessel_id, planet_id, user_id, delta_distance, delta_angle) VALUES ('F', ?, ?, ?, ?, ?, ?)", $ticks, $vessel_id, 0, $user_id, $delta_distance, $delta_angle);
  }

  if ($manual_flight == 0) {
    $delta_distance = 0;
    $delta_angle = 0;

    if ($param->{sector} =~ /yes/) {
      # TODO: Remove to px_sector
  	  $dst_sector = px_sector::get_sector ($param->{sector_id});
      if (errors->is_error ($dst_sector)) { return errors->ERR_CANT_MOVE; }

      $ticks = calc_sector_ticks ($dst_sector->{distance}, $dst_sector->{angle}, $vessel->{distance}, $vessel->{angle}, $vessel->{warp});
	    if (errors->is_error ($ticks)) { return errors->ERR_CANT_MOVE; }
	    if ($ticks == 0) { $ticks = 1; }

    	$delta_distance = ($dst_sector->{distance} - $vessel->{distance}) / $ticks;
    	$delta_angle = ($dst_sector->{angle} - $vessel->{angle}) / $ticks;
      $dst_sector_id = $dst_sector->{id};
	    $dst_planet_id = 0;

      # Set our planet and sector to 0, which means we are not in a sector at this point.
      # Also set the angle and distance to a transsector one
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET sun_distance=0, planet_id=0, sector_id=0 WHERE id=?", $vessel_id);
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET dst_distance=?, dst_angle=? WHERE id=?", $dst_sector->{distance}, $dst_sector->{angle}, $vessel_id);
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET dst_planet_id=0, dst_sector_id=? WHERE id=?", $dst_sector->{id}, $vessel_id);
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='FLYING' WHERE id=?", $vessel_id);

    } else {
      $dst_planet = px_planet::get_planet ($param->{planet_id});
      if (errors->is_error ($dst_sector)) { return errors->ERR_CANT_MOVE; }

      $ticks = calc_distance ($dst_planet->{distance}, $src_sector->{angle}, $vessel->{distance}, $vessel->{angle});
      if (errors->is_error ($dst_sector)) { return errors->ERR_CANT_MOVE; }

      $ticks = ( $ticks / 1000 ) / $vessel->{impulse} / 2;
      $ticks = int ($ticks + 0.5);
      if ($ticks == 0) { $ticks = 1; }

    	$delta_distance = 0;
    	$delta_angle = 0;
      $dst_sector_id = 0;
    	$dst_planet_id = $param->{planet_id};

      # Set our planet to 0, which means we are not near a planet, but still in the sector at this point.
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET planet_id=0 WHERE id=?", $vessel_id);
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET dst_planet_id=?, dst_sector_id=? WHERE id=?", $dst_planet->{id}, $dst_planet->{sector_id}, $vessel_id);
      px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='FLYING' WHERE id=?", $vessel_id);
    }

    print "Flight Information\n";
    print "Ticks         : $ticks\n";
    print "Delta Distance: $delta_distance\n";
    print "Delta Angle   : $delta_angle\n";
    print "Dst Sector Id : $dst_sector_id\n";
    print "Dst Planet Id : $dst_planet_id\n";
    print "\n";
    px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO h_queue (type, ticks, vessel_id, planet_id, user_id, delta_distance, delta_angle) VALUES ('F', ?, ?, ?, ?, ?, ?)", $ticks, $vessel_id, $dst_planet_id, $user_id, $delta_distance, $delta_angle);
  }
  return errors->ERR_OK;
}

# ===========================================================================================================
# Stop()
#
# Description:
#    Stops a vessel at its current location
#
# ParamList
#     vessel_id     ID of the vessel
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub stop {
  my $vessel_id = shift;
  errors::assert (errors::is_value ($vessel_id));

  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM h_queue WHERE vessel_id=?", $vessel_id);
  if ($sth->rows == 0) { return errors->ERR_VESSEL_NOT_MOVING; }
  my $queue_entry = px_mysql::fetchhash ($sth);

  px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM h_queue WHERE id=?", $queue_entry->{id});
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='SPACE', dst_sector_id=0, dst_planet_id=0, dst_distance=0, dst_angle=0, sector_id=0, planet_id=0 WHERE id=?", $vessel_id);

  return errors->ERR_OK;
}

# ===========================================================================================================
# Remove()
#
# Description:
#    Removes a vessel from the database
#
# ParamList
#     vessel_id     ID of the vessel
#
# Returns:
#     ERR_*    failure
#     ERR_OK   success
#
sub remove {
  my $vessel_id = shift;
  errors::assert (errors::is_value ($vessel_id));


  px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM g_vessels WHERE id=?", $vessel_id);
  px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM i_vessels WHERE vessel_id=?", $vessel_id);
  return errors->ERR_OK;
}



# ===========================================================================================================
# Calc_Sector_Ticks()
#
# Description:
#   Calculates the number of ticks between the 2 coordinats for that warpspeed
#
# ParamList
#   distance 1    Distance of of coordinate 1
#   angle 1       Angle of coordinate 1
#   distance 2    Distance of coordinate 2
#   angle 2       Angle of coordinate 2
#   warp          Warp speed
#
# Returns:
#   Number of ticks
#
sub calc_sector_ticks () {
  my ($r1, $a1, $r2, $a2, $warp) = @_;
  errors::assert (errors::is_value ($r1));
  errors::assert (errors::in_range ($a1, 0, 360000));
  errors::assert (errors::is_value ($r2));
  errors::assert (errors::in_range ($a2, 0, 360000));
  errors::assert (errors::in_range ($warp, 0, 100));

  if ($warp == 0) { return errors->ERR_NOWARP; }

  my $ticks = calc_distance ($r1, $a1, $r2, $a2);
  $ticks = $ticks * ((100 / ($warp / 10)) / 10);
  $ticks = int ( ($ticks / ($px_config::config->{warp_dividor})+0.5));

  return $ticks;
}

# ===========================================================================================================
# Deg2Rad ()
#
# Description:
#   Converts degrees into radians
#
# ParamList
#   int   degrees
#
# Returns:
#   int   radians
#
sub deg2rad {
  my $deg = shift;

  my $pi = atan2 (1, 1) * 4;
  return ($deg * $pi / 180);
}

# ===========================================================================================================
# Calc_Distance ()
#
# Description:
#   Calculate distance between 2 coordinates with a pythagoras function
#
# ParamList
#   distance 1    Distance of of coordinate 1
#   angle 1       Angle of coordinate 1
#   distance 2    Distance of coordinate 2
#   angle 2       Angle of coordinate 2
#
# Returns:
#   int       Distance between the coordinates
#
sub calc_distance ($r1, $a1, $r2, $a2) {
  my ($r1, $a1, $r2, $a2) = @_;

  my $Y1 = $r1 * cos (deg2rad($a1 / 1000));
  my $X1 = $r1 * sin (deg2rad($a1 / 1000));

  my $Y2 = $r2 * cos (deg2rad($a2 / 1000));
  my $X2 = $r2 * sin (deg2rad($a2 / 1000));

  my $DX = abs ($X1 - $X2);
  my $DY = abs ($Y1 - $Y2);

  my $c = sqrt (($DX * $DX) + ($DY * $DY));

  $c *= 1000;
  $c  = int ($c + 0.5);
  $c /= 1000;

  return $c;
}


# ===========================================================================================================
# Fini_Loop()
#
# Description:
#   Finishes an loop started with an init_get_all_* function
#
# ParamList
#   handle    id of the loop
#
# Returns:
#   int       error code
#
sub fini_loop ($) {
  my $handle = shift;
  errors::assert (not errors::is_empty($handle));

  if ($handle != 0) {
    px_mysql::query_finish ($handle);
  }
  return errors->ERR_OK;
}

# ===========================================================================================================
# Get_Next_Entry ()
#
# Description:
#   Get's the next item in the loop started with an init_get_all_* function
#
# ParamList
#     handle    handle of the loop
#
# Returns:
#     hash      hash of the item-data
#     false     beyond last item of the loop
#
sub get_next_entry ($) {
  my $handle = shift;
  errors::assert (not errors::is_empty($handle));

  return px_mysql::fetchhash ($handle);
}

# ===========================================================================================================
# init_get_all_vessels ()
#
# Description:
#    Browses through all vessels. From which user doesn't matter
#
# ParamList
#    none
#
# Returns:
#    int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_vessels () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM g_vessels WHERE created=1");
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_vessel_items ()
#
# Description:
#   Browses through the cargo of all vessels. From which user doesn't matter
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_vessel_items () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM i_vessels");
  return $new_handle;
}