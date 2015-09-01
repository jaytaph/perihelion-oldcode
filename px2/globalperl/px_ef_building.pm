package px_ef_building;
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


# -------------------------------------------------------------------------------------------------------
# We need to use AUTOLOAD to catch undefined functions.... crap...
sub AUTOLOAD {
}

# -------------------------------------------------------------------------------------------------------
# context "TICK"          executed each tick
# context "QUEUE_IN"      entry added to queue
# context "QUEUE_OUT"     entry deleted out queue
# context "INIT"          entry manufactured
sub execute_building_function {
  my $context = shift @_;
  my $item = shift @_;
  my (@paramlist) = @_;

  # And place the item to it
  my $tmp = "px_ef_building::".lc($context)."_".$item;
  if (lc($context) ne "tick") { print "Executing: $tmp\n"; }

  no strict 'refs';
  return &{$tmp} (@paramlist);
  use strict 'refs';
}


# -------------------------------------------------------------------------------------------------------
# Item 0 is executed for each item
sub init_0 {
  my $building_id = shift;
  my $planet_id = shift;
  my $user_id = shift;

  # Add the building to the list of buildings on the surface...
  my $surface  = px_planet::get_surface ($planet_id);
  my $building_ids = add_to_list ($building_id, $surface->{building_ids});
  px_planet::set_surface_buildings ($planet_id, $building_ids);
#  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_surface SET building_ids=? WHERE planet_id=?", $building_ids, $planet_id);

  # Add upkeep costs and ores to the planet
  my $planet   = px_planet::get_planet ($planet_id);
  my $building = px_building::get_building ($building_id);
  my $costs = $planet->{upkeep_costs};
  my $ores  = $planet->{upkeep_ores};
  # Add item upkeep to the planet
  $planet->{upkeep_costs} += $building->{upkeep_costs};
  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    my $ore = px_ore::get_ore ($planet->{upkeep_ores}, $i);
    $ore += px_ore::get_ore ($building->{upkeep_ores}, $i);
    $planet->{upkeep_ores} = px_ore::set_ore ($planet->{upkeep_ores}, $i, $ore);
  }
  # And update it
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET upkeep_costs=?, upkeep_ores=? WHERE id=?", $planet->{upkeep_costs}, $planet->{upkeep_ores}, $planet_id);


  # Increase the planets inhabitant capacity if this building has room for it...
  if ($building->{max_habitants} > 0) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET population_capacity=population_capacity+? WHERE id=?", $building->{max_habitants}, $planet_id);
  }

  # And mail the user...
  my $planet   = px_planet::get_planet ($planet_id);
  px_message::create_message (px_message->MSG_USER, $user_id, "Construction", "Building ready",
                              "The construction crew on planet ".$planet->{name}." inform you of the completion of a new ".$building->{name},
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_PLANET);
}

# -------------------------------------------------------------------------------------------------------
sub fini_0 {
  my $building_id = shift;
  my $planet_id = shift;
  my $user_id = shift;
  my ($sth, $ore);

  # Remove upkeep costs and ores to the planet
  my $planet = px_planet::get_planet ($planet_id);
  my $building = px_building::get_building ($building_id);

  # Remove item upkeep from the planet
  $planet->{upkeep_costs} -= $building->{upkeep_costs};
  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    my $ore = px_ore::get_ore ($planet->{upkeep_ores}, $i);
    $ore -= px_ore::get_ore ($building->{upkeep_ores}, $i);
    $planet->{upkeep_ores} = px_ore::set_ore ($planet->{upkeep_ores}, $i, $ore);
  }

  # And update it
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET upkeep_costs=?, upkeep_ores=? WHERE id=?", $planet->{upkeep_costs}, $planet->{upkeep_ores}, $planet_id);

}

# -------------------------------------------------------------------------------------------------------
sub tick_0 {
}


## -------------------------------------------------------------------------------------------------------
## Set temporary flag for mining. When the building is completed, the queue_out function will set this to 2, which means, extract ores...
#sub queue_in_3 {
#  my ($planet_id, $user_id) = @_;
#  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET can_mine=? WHERE id=?", constants->CAN_MINE_PENDING, $planet_id);
#}

# -------------------------------------------------------------------------------------------------------
# Set temporary flag for mining. The building is completed, set this to 2, which means, extract ores...
sub queue_out_3 {
  my ($queue) = @_;
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET can_mine=? WHERE id=?", constants->CAN_MINE, $queue->{planet_id});
}


# -------------------------------------------------------------------------------------------------------
# Obsolete function, we don't care entering the queue.
sub queue_in_6 {
  my ($planet_id, $user_id) = @_;
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET can_explore=? WHERE id=?", constants->CAN_EXPLORE_PENDING, $planet_id);
}

# -------------------------------------------------------------------------------------------------------
# Set the planet capability to exploration
sub queue_out_6 {
  my ($queue) = @_;
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET can_explore=? WHERE id=?", constants->CAN_EXPLORE_PLANETS, $queue->{planet_id});
}

# -------------------------------------------------------------------------------------------------------
# If we build a headquarter, we add 100 people to the planet
sub init_1 {
  my ($planet_id, $user_id) = @_;

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET population=100, state_id=2, happieness=100, sickness=0 WHERE id=?", $planet_id);
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET population=population+100 WHERE user_id=?", $user_id);
}

# -------------------------------------------------------------------------------------------------------
# If we build a vessel station, we add 1 impulse percent to the user.
sub init_9 {
  my ($planet_id, $user_id) = @_;

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET impulse=1 WHERE user_id=?", $user_id);
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_flags SET can_build_explorationship=1, can_build_tradeship=1 WHERE user_id=?", $user_id);
}

# -------------------------------------------------------------------------------------------------------
# If we build a space dock, we add 0.1 warpfactor to the user if we already have full impulse otherwise we wait...
sub init_11 {
  my ($planet_id, $user_id) = @_;

  my $user   = px_user::get_user ($user_id);
  my $sector = px_sector::get_sector ($user->{sector_id});
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_sectors SET private=0 WHERE id=?", $sector->{id});
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_flags SET can_build_battleship=1, can_warp=1 WHERE user_id=?", $user_id);
  if ($user->{impulse} == 100) { goto_warp ($user_id); }
}

# -------------------------------------------------------------------------------------------------------
# If we build a exploration station, we can also explore the surfaces
sub init_13 {
  my ($planet_id, $user_id) = @_;

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET can_explore=? WHERE id=?", constants->CAN_EXPLORE_SURFACE, $planet_id);
}



return 1;