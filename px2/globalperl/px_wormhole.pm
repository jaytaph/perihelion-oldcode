package px_wormhole;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling programs
return 1;

# ===========================================================================================================
# Create()
#
# Description:
#    Generates a new wormhole and returns it's distance and planet id
#
# ParamList
#    planet_name       Name of the wormhole (if applicable)
#    order             Planet order from the sun, earth = 3, mars = 2, venus = 4 etc..
#    sector_id         sector_id of sector for this planet
#    user_id           user_id of owner
#    distance          minimum distance from sun
#
# Returns:
#    distance          Distance from the sun
#    wormhole_id         ID of wormhole in the database
#
sub create {
  my $param = @_[0];

  my ($name, $state, $image, $new_distance, $radius);

  # Generate some standard planet stuff
  $image        = int(rand($px_config::galaxy->{wormhole_image_cnt})+1);
  $radius       = int(rand($px_config::galaxy->{planet_size_max})+$px_config::galaxy->{planet_size_min});
  $new_distance = $param->{distance} + int(rand($px_config::galaxy->{planet_distance_max})+$px_config::galaxy->{planet_distance_min}+$radius);
  $name         = "Wormhole ".$param->{order};

  my ($distance, $angle) = px_sector::get_random_coordinate ();
  my $next_jump = 1;


  # Add it to the planet table
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO s_anomalies (type, sector_id, user_id, state_id,
                                            unknown, name, class, image,
                                            radius, distance, population, population_capacity,
                                            water, temperature, can_mine, can_explore,
                                            next_explore, happieness, sickness)
                                    values ('W', ?, ?, ?,
                                            1, ?, '-', ?,
                                            ?, ?, 0, 0,
                                            0, 0, 0, 0,
                                            0, -1, -1)",
                                          $param->{sector_id}, $param->{user_id}, constants->PLANET_STATE_WORMHOLE,
                                          $name, $image,
                                          $radius, $new_distance);

  # Grab the id of that wormhole
  my $wormhole_id = px_mysql::get_last_insert_id ();

  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_ores (planet_id, cur_ores, max_ores, stock_ores) values (?, '', '', '')", $wormhole_id);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_surface (planet_id) VALUES (?)", $wormhole_id);

  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO w_wormhole (id, distance, angle, planet_id, sector_id, next_jump) VALUES (?, ?, ?, ?, ?, ?)", $wormhole_id, $distance, $angle, $wormhole_id, $param->{sector_id}, $next_jump);

  return ($wormhole_id, $new_distance);
}


# ===========================================================================================================
# Is_Stabilized ()
#
# Description:
#   Checks if the wormhole is stabilized or not
#
# Paramlist:
#     wormhole     hash of the wormhole
#
# Returns:
#     ERR_FALSE    not stabilized
#     ERR_TRUE     stabilized
#     ERR_*        other error
#
sub is_stabilized ($) {
  my $wormhole_id = shift;
  errors::assert (errors::is_value ($wormhole_id));

  if (! px_anomaly::is_wormhole ($wormhole_id)) { return errors->ERR_NO_WORMHOLE; }

  my $wormhole = px_wormhole::get_wormhole ($wormhole_id);
  if ($wormhole->{next_jump} < 0) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}

# ===========================================================================================================
# Get_Wormhole ()
#
# Description:
#   Returns wormhole information
#
# ParamList
#   wormhole_id    id to the wormhole
#
# Returns:
#   reference to wormhole hash
#
my $sth_get_wormhole = 0;
sub get_wormhole ($) {
  my ($wormhole_id) = @_;
  errors::assert (errors::is_value ($wormhole_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_wormhole == 0) {
    $sth_get_wormhole = $px_mysql::dbhandle->prepare ("SELECT * FROM w_wormhole WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_wormhole->execute ($wormhole_id) or die "Reason: $DBI::strerr";
  return $sth_get_wormhole->fetchrow_hashref;
}


# ===========================================================================================================
# Move_Through_Wormhole ()
#
# Description:
#   Moves a vessel through a wormhole
#
# Paramlist:
#     vessel_id       ID of the vessel
#     wormhole_id     ID of the wormhole
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub move_through_wormhole ($$) {
  my $wormhole_id = shift;
  my $vessel_id = shift;
  errors::assert (errors::is_value ($wormhole_id));
  errors::assert (errors::is_value ($vessel_id));

  if (not px_anomaly::is_wormhole ($wormhole_id)) { return errors->ERR_NO_WORMHOLE; }

  # Move the ship to the wormholes endpoint coordinates
  my $vessel   = px_vessel::get_vessel ($vessel_id);
  my $wormhole = px_wormhole::get_wormhole ($wormhole_id);
  px_vessel::set_distance_and_angle ($vessel_id, $wormhole->{distance}, $wormhole->{angle}, px_vessel->SET_VESSEL_DA_ABS);
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_vessels SET status='SPACE', sector_id=0, planet_id=0 WHERE id=?", $vessel_id);

  px_message::create_message (px_message->MSG_USER, $vessel->{user_id}, "Ships captain", "Wormhole passage",
                              "The captain of vessel ".$vessel->{name}." informs you of the passage through wormhole ".$wormhole->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_VESSEL);

  return errors->ERR_OK;
}


# ===========================================================================================================
# Stabilize_wormhole ()
#
# Description:
#   Stabilizes the wormhole so it cannot jump to another destination
#
# Paramlist:
#     wormhole_id          wormhole id
#
# Returns:
#     ERR_OK                    success
#     ERR_ALREADY_STABILIZED    wormhole is already stabilized
#     ERR_*                     failure
#
sub stabilize_wormhole {
  my $wormhole_id = shift;
  errors::assert (errors::is_value ($wormhole_id));

  # We aren't a wormhole, we don't care about a wormhole stabilizer
  if (not px_anomaly::is_wormhole ($wormhole_id)) { return errors->ERR_NO_WORMHOLE; }

  # Now, get the ammount of stabilizers already present...
  my $wormhole = px_wormhole::get_wormhole ($wormhole_id);
  my $tmp = px_planet::get_surface ($wormhole->{id});
  my $planet_items = $tmp->{cargo_ids};
  my $stabilizer_count = px_csl::count_items ($planet_items, constants->I_WORMHOLE_STABILIZER);

  # If we already have a wormhole stabilizer present, don't do anything...
  if ($stabilizer_count > 1) { return errors->ERR_ALREADY_STABILIZED; }

  # Stabilize the wormhole by setting the next_jump to a negative value
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE w_wormhole SET next_jump=-next_jump WHERE id=?", $wormhole_id);

  # Send a message
  px_message::create_message (px_message->MSG_USER, $wormhole->{user_id}, "Caretakers of ".$wormhole->{name}, "Wormhole stabilized",
                              "The caretakers of the wormhole reports that the wormhole has been artificially stabilized by an wormhole stabilizer.",
                              constants->MESSAGE_PRIO_HIGH, constants->MSG_TYPE_GLOBAL);

  return errors->ERR_OK;
}

# ===========================================================================================================
# Unstabilize_wormhole ()
#
# Description:
#   Unstabilizes the wormhole so it can jump again
#
# Paramlist:
#     planet          wormhole hash
#
# Returns:
#     ERR_OK                    success
#     ERR_STILL_STABILIZED      stabilzer removed, but wormhole still stabilized
#     ERR_*                     failure
#
sub unstabilize_wormhole {
  my $wormhole_id = shift;
  errors::assert (errors::is_value ($wormhole_id));

  # We aren't a wormhole, we don't care about a wormhole stabilizer
  if (not px_anomaly::is_wormhole ($wormhole_id)) { return errors->ERR_NO_WORMHOLE; }

  # Now, get the ammount of stabilizers already present...
  my $tmp = px_planet::get_surface ($wormhole_id);
  my $planet_items = $tmp->{cargo_ids};
  my $stabilizer_count = px_csl::count_items ($planet_items, constants->I_WORMHOLE_STABILIZER);

  # If we have 2 wormhole stabilizers present, don't do anything because 1 still will be there...
  if ($stabilizer_count >= 1) { return errors->ERR_STILL_STABILIZED; }

  # Unstabilize the wormhole by setting the next_jump back to a positive value
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE w_wormhole SET next_jump=-next_jump WHERE id=?", $wormhole_id);

  my $wormhole = px_wormhole::get_wormhole ($wormhole_id);
  px_message::create_message (px_message->MSG_USER, $wormhole->{user_id}, "Caretakers of ".$wormhole->{name}, "Wormhole unstabilized",
                              "The caretakers of the wormhole reports that the wormhole stabilizer for the wormhole has been removed a vessel.",
                              constants->MESSAGE_PRIO_HIGH, constants->MSG_TYPE_GLOBAL);

  return errors->ERR_OK;
}


# ===========================================================================================================
# Jump ()
#
# Description:
#   Generates a new destination for the wormhole's endpoint.
#
# Paramlist:
#     wormhole_id     id of the wormhole
#
# Returns:
#     ERR_OK                    success
#     ERR_*                     failure
#
sub jump ($) {
  my $wormhole_id = shift;
  errors::assert (errors::is_value ($wormhole_id));

  my ($distance, $angle) = px_sector::get_random_coordinate ();
 	my $nextjump = int(rand ($px_config::galaxy->{max_wormhole_jump}) + 2);
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE w_wormhole SET next_jump=?, distance=?, angle=? WHERE id=?", $nextjump, $distance, $angle, $wormhole_id);

  return errors->ERR_OK;
}



# ===========================================================================================================
# Time_For_Jump ()
#
# Description:
#   Returns if it's time for a wormhole jump
#
# Paramlist:
#     wormhole     hash of the wormhole
#
# Returns:
#     ERR_TRUE       success
#     ERR_FALSE      wormhole is already stabilized
#     ERR_*          failure
#
sub time_for_jump ($) {
  my $wormhole_id = shift;
  errors::assert (errors::is_value ($wormhole_id));

  if (not px_anomaly::is_wormhole ($wormhole_id)) { return errors->ERR_NO_WORMHOLE; }

  my $wormhole = px_wormhole::get_wormhole ($wormhole_id);
  if ($wormhole->{next_jump} == 0) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}



# ===========================================================================================================
# decrease_tick ()
#
# Description:
#   Decreases the wormhole jump tick counter
#
# Paramlist:
#     wormhole_id     id of the wormhole
#
# Returns:
#     int      number of ticks remaining before jump
#
sub decrease_tick ($) {
  my $wormhole_id = shift;
  errors::assert (errors::is_value ($wormhole_id));

  if (not px_anomaly::is_wormhole ($wormhole_id)) { return errors->ERR_NO_WORMHOLE; }
  if (px_wormhole::is_stabilized ($wormhole_id)) { return errors->ERR_ALREADY_STABILIZED; }

  my $wormhole = px_wormhole::get_wormhole ($wormhole_id);
  $wormhole->{next_jump}--;

 	px_mysql::query (constants->QUERY_NOKEEP, "UPDATE w_wormhole SET next_jump=? WHERE id=?", $wormhole->{next_jump}, $wormhole_id);

  return $wormhole->{next_jump};
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
  errors::assert (not errors::is_empty ($handle));

  return px_mysql::fetchhash ($handle);
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
  errors::assert (not errors::is_empty ($handle));

  if ($handle != 0) {
    px_mysql::query_finish ($handle);
  }

  return errors->ERR_OK;
}

# ===========================================================================================================
# init_get_all_wormholes ()
#
# Description:
#   Browses through all wormholes
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_wormholes () {
  my $sector_id = shift;
  errors::assert (errors::is_value ($sector_id));

  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE type='W'");
  return $new_handle;
}
