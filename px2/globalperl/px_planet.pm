package px_planet;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

use constant SET_POPULATION_ABS    => 1;
use constant SET_POPULATION_ADD    => 2;
use constant SET_POPULATION_SUB    => 3;


# ===========================================================================================================
# Create()
#
# Description:
#    Generates a new planet and returns it's distance and planet id
#
# ParamList
#    is_home_planet    [yes/no]
#    planet_name       Name of the planet (if applicable)
#    order             Planet order from the sun, earth = 3, mars = 2, venus = 4 etc..
#    sector_id         sector_id of sector for this planet
#    user_id           user_id of owner
#    distance          minimum distance from sun
#
# Returns:
#    distance          Distance from the sun
#    planet_id         ID of planet in the database
#
sub create {
  my $param = @_[0];


  my ($planetclass, $name, $state, $pax, $paxcap, $happy, $sick, $image, $new_distance, $radius, $water, $temp);

  # If it's our homeplanet, we add already some people onto the planet, otherwise it's just deserted.
  if ($param->{is_home_planet} eq "yes") {
    # Make sure our homeplanet is a livable planet.
    do {
      $planetclass  = chr(65+rand(26));
  	} while (not px_planet::is_habitable ( { class=>$planetclass } ));
  	$state  = constants->PLANET_STATE_NORMAL;
	  $pax    = $px_config::galaxy->{initial_pax};
  	$paxcap = $px_config::galaxy->{initial_pax};
  	$name   = $param->{planet_name};
    $sick   = 0;
    $happy  = 100;
  } else {
    $planetclass  = chr(65+rand(26));
    $state  = constants->PLANET_STATE_UNINHABITATED;
    $pax    = 0;
    $paxcap = 0;
    $name   = "";
    $sick   = -1;
    $happy  = -1;
  }


  # Generate some standard planet stuff
  $image        = int(rand($px_config::galaxy->{planet_image_cnt})+1);
  $radius       = int(rand($px_config::galaxy->{planet_size_max})+$px_config::galaxy->{planet_size_min});
  $new_distance = $param->{distance} + int(rand($px_config::galaxy->{planet_distance_max})+$px_config::galaxy->{planet_distance_min}+$radius);
  $name         = "Planet ".$param->{order};


  # The PlanetClass generated above defines how the water and temperature is on the planet.
  # It's properties are stored in the database like this:
  #   100,273,E,10,0,500,500,J,50,20,500,300,N,45,50,200,100,P,20,0,5000,1000,Z,0,0,10000,5000
  # first 2 items are default W and T, thirth is the class (less or equal) and 4 random numbers, 2 for water, 2 for temp
  my @wt_arr = split (',', $px_config::galaxy->{class_watertemp});

  # First item is default water, second default temperature
  $water = $wt_arr[0];
  $temp  = $wt_arr[1];

  # Now, browse all classes and find the correct values in the array
  my $i = 2;
  do {
    if ($wt_arr[$i] =~ /[A-Z]/) {
      if ($planetclass le $wt_arr[$i]) {
        $water = int ($wt_arr[$i+1]) + $wt_arr[$i+2];
        $temp  = int ($wt_arr[$i+3]) + $wt_arr[$i+4];
        $i = $#wt_arr - 1;
      }
    }
    $i++;
  } while ($i < $#wt_arr);

  # Add it to the planet table
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO s_anomalies (type, sector_id, user_id, state_id,
                                            unknown, name, class, image,
                                            radius, distance, population, population_capacity,
                                            water, temperature, can_mine, can_explore,
                                            next_explore, happieness, sickness)
                                    values ('P', ?, ?, ?,
                                            1, ?, ?, ?,
                                            ?, ?, ?, ?,
                                            ?, ?, 0, 0,
                                            0, ?, ?)",
                                          $param->{sector_id}, $param->{user_id}, $state,
                                          $name, $planetclass, $image,
                                          $radius, $new_distance, $pax, $paxcap,
                                          $water, $temp, $happy, $sick);

  # Grab the id of that planet
  my $planet_id = px_mysql::get_last_insert_id();


  #
  # Every planettype has it's own different ore configuration. This is sorted out in the following array type,
  #  'type', ore, multiplier,  < ore, multiplier, > ....
  #  'type', ore, multiplier,  < ore, multiplier, > ....
  #   ......
  #  '.'
  # So first is a char which gives us the planet type, next is the ore we have and next is the multiplier.
  # We go on for each array until we hit a '.' as planet class.
  #
  my ($max_ores, $stock_ores, @oremul);
  for ($i=1; $i!=px_ore::get_ore_count(); $i++) { $oremul[$i] = 1; }

  # Make it a nice array, we make sure it's terminated by a dot.
  my @oreconf_arr = split (",", $px_config::galaxy->{class_ores});
  push (@oreconf_arr, ".");

  my $i = 0;
  do {
    if ($oreconf_arr[$i] eq $planetclass) {
      do {
        my $a = $oreconf_arr[$i+1];
        my $b= $oreconf_arr[$i+2];
        $oremul[$a] = $b;
        $i += 2;
      } while ($oreconf_arr[$i+1] =~ /\d/);
    }
    $i++;
  } while ($oreconf_arr[$i] ne '.');

  for ($i=1; $i!=px_ore::get_ore_count(); $i++) {
    $max_ores = $max_ores . int(rand(2000 * $oremul[$i] ) + 0) . ",";
  }

  # We get some ores on our homeplanet
  if ($param->{is_home_planet} eq "yes") {
    $stock_ores =$px_config::galaxy->{initial_ores};
  } else {
    $stock_ores = "";
  }

  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_ores (planet_id, cur_ores, max_ores, stock_ores) values (?, '', ?, ?)", $planet_id, $max_ores, $stock_ores);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_surface (planet_id) VALUES (?)", $planet_id);

  return ($planet_id, $new_distance);
}

# ===========================================================================================================
# is_habitable()
#
# Description:
#    Returns wether or not a planet is habitable. Function either takes a planet_id or a class char (A..Z)
#
# ParamList
#    planet_id           id of the planet
#    class               planet class
#
# Returns:
#    ERR_FALSE           Not habitable
#    ERR_TRUE            Habitable
#
sub is_habitable {
  my $param = @_[0];
  my ($planet, $class);

  if (defined ($param->{planet_id})) {
    # Grab the id of that planet
    $planet = px_planet::get_planet ($param->{planet_id});
    $class = $planet->{class};
  }

  if (defined ($param->{class})) {
    $class = $param->{class};
  }

  # Search for the class in the class_habitable string
  if ( $px_config::galaxy->{class_habitable} =~ /$class/ ) { return errors->ERR_TRUE; }

  # Default to not habitable
  return errors->ERR_FALSE;
}


# ===========================================================================================================
# is_minable()
#
# Description:
#    Returns wether or not a planet is minable. Function either takes a planet_id or a class char (A..Z)
#
# ParamList
#    minable_id          id of the planet
#    class               planet class
#
# Returns:
#    ERR_FALSE           Not minable
#    ERR_TRUE            Minable
#
sub is_minable ($%) {
  my $param = @_[0];

  my ($planet, $class);

  if (defined ($param->{planet_id})) {
    # Grab the id of that planet
    $planet = px_planet::get_planet ($param->{planet_id});
    $class = $planet->{class};
  }

  if (defined ($param->{class})) {
    $class = $param->{class};
  }

  # Search for the class in the class_habitable string
  if ( $px_config::galaxy->{class_minable} =~ /$class/ ) { return errors->ERR_TRUE; }

  # Default to not habitable
  return errors->ERR_FALSE;
}


# ===========================================================================================================
# Set_Strength ()
#
# Description:
#   Sets the strength of a planet
#
# ParamList
#   planet_id   ID of the planet
#   strength    New strength to set
#
# Returns:
#   ERR_OK     success
#   ERR_*      failure
#
sub set_strength ($$) {
  my $planet_id = shift;
  my $strength = shift;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($strength));


  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET cur_strength=? WHERE id=?", $strength, $planet_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#   ERR_OK     success
#   ERR_*      failure
#
sub set_mining ($$) {
  my $planet_id = shift;
  my $mine_flag = shift;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($mine_flag));

 	px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET can_mine=? WHERE id=?", $mine_flag, $planet_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#   ERR_OK     success
#   ERR_*      failure
#
sub set_surface_cargo ($$) {
  my $planet_id = shift;
  my $cargo_csl = shift;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($cargo_csl));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_surface SET cargo_ids=? WHERE planet_id=?", $cargo_csl, $planet_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#   ERR_OK     success
#   ERR_*      failure
#
sub set_surface_buildings ($$) {
  my $planet_id = shift;
  my $building_csl = shift;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($building_csl));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_surface SET building_ids=? WHERE planet_id=?", $building_csl, $planet_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Population ()
#
# Description:
#    Sets the planet's population
#
# ParamList
#    planet | planet_id           planet hash | id of the planet
#
# Returns:
#
sub set_population ($$$) {
  my $planet_id = shift;
  my $ammount = shift;
  my $offset = shift;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($ammount));
  errors::assert (errors::is_in_range ($offset, 1, 3));

  if ($offset == SET_POPULATION_ABS) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET population=? WHERE id=?", $ammount, $planet_id);
  }
  if ($offset == SET_POPULATION_SUB) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET population=population-? WHERE id=?", $ammount, $planet_id);
  }
  if ($offset == SET_POPULATION_ADD) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET population=population+? WHERE id=?", $ammount, $planet_id);
  }
  return errors->ERR_OK;
}



# ===========================================================================================================
# Set_Next_Exploration_Level ()
#
# Description:
#    Sets the level at which this planet will discover another anomaly in the galaxy
#
# ParamList
#    planet_id   ID of the planet
#    level       new level
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_next_exploration_level ($$) {
  my $planet_id = shift;
  my $level = shift;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($level));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET next_explore=? WHERE id=?", $level, $planet_id);
  return errors->ERR_OK;
}



# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#
my $sth_get_surface = 0;
sub get_surface ($) {
  my ($planet_id) = @_;
  errors::assert (errors::is_value ($planet_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_surface == 0) {
    $sth_get_surface = $px_mysql::dbhandle->prepare ("SELECT * FROM g_surface WHERE planet_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_surface->execute ($planet_id) or die "Reason: $DBI::strerr";
  return $sth_get_surface->fetchrow_hashref;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#
my $sth_get_planet = 0;
sub get_planet ($) {
  my ($planet_id) = @_;
  errors::assert (errors::is_value ($planet_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_planet == 0) {
    $sth_get_planet = $px_mysql::dbhandle->prepare ("SELECT * FROM s_anomalies WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_planet->execute ($planet_id) or die "Reason: $DBI::strerr";
  return $sth_get_planet->fetchrow_hashref;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#
my $sth_get_ores = 0;
sub get_ores ($) {
  my ($planet_id) = @_;
  errors::assert (errors::is_value ($planet_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_ores == 0) {
    $sth_get_ores = $px_mysql::dbhandle->prepare ("SELECT * FROM g_ores WHERE planet_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_ores->execute ($planet_id) or die "Reason: $DBI::strerr";
  return $sth_get_ores->fetchrow_hashref;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#
my $sth_get_planet_items = 0;
sub get_planet_items ($) {
  my ($planet_id) = @_;
  errors::assert (errors::is_value ($planet_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_planet_items == 0) {
    $sth_get_planet_items = $px_mysql::dbhandle->prepare ("SELECT * FROM g_surface WHERE planet_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_planet_items->execute ($planet_id) or die "Reason: $DBI::strerr";
  return $sth_get_planet_items->fetchrow_hashref;
}

# ===========================================================================================================
# power_total()
#
# Description:
#    Calculates the total power usages of the planet
#
# ParamList
#    planet_id         id of the planet
#
# Returns:
#    int               total power output
#
sub power_total ($) {
  my $planet_id = $_[0];
  errors::assert (errors::is_value ($planet_id));

  my $power = 0;

  my $buildinglist = px_planet::get_surface ($planet_id);
  my @building_arr = split (",", $buildinglist->{building_ids});

  foreach my $building (@building_arr) {
    my $building_info = px_building::get_building ($building);
    $power += $building_info->{power_out};
  }

  return $power;
}

# ===========================================================================================================
# ()
#
# Description:
#
# ParamList
#
# Returns:
#
sub update_ores ($$$) {
  my ($planet_id, $csl_co, $csl_so) = @_;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (not errors::is_empty ($csl_co));
  errors::assert (not errors::is_empty ($csl_so));

  if ($csl_co != "") {
    my $query = $px_mysql::dbhandle->prepare ("UPDATE g_ores SET cur_ores=? WHERE planet_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
    $query->execute ($csl_co, $planet_id) or die "Reason: $DBI::strerr";
  }
  if ($csl_so != "") {
    my $query = $px_mysql::dbhandle->prepare ("UPDATE g_ores SET stock_ores=? WHERE planet_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
    $query->execute ($csl_so, $planet_id) or die "Reason: $DBI::strerr";
  }

  return errors->ERR_OK;
}


# ===========================================================================================================
# count_building()
#
# Description:
#    Returns the number of buildings $building_id build on planet $planet_id
#
# ParamList
#    planet_id         id of the planet
#    building_id       id of the building
#
# Returns:
#    int               number of buildings present
#
sub count_buildings ($$) {
  my ($planet_id, $building_id) = @_;
  errors::assert (errors::is_value ($planet_id));
  errors::assert (errors::is_value ($building_id));

  my $buildings = px_planet::get_surface ($planet_id);
  my $count = () = $buildings->{buildings_ids} =~ /$building_id/g;
  return $count;
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
# init_get_all_explorable_planets ()
#
# Description:
#   Browses through all planets that have exploration capabilities
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_explorable_planets () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE can_explore >= 2");
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_minable_planets ()
#
# Description:
#   Browses through all planets that have mining capabilities
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_minable_planets () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE can_mine = 1");
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_owned_planets ()
#
# Description:
#   Browses through all planets that are owned by a user. Which user doesn't matter
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_owned_planets () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE user_id != 0");
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_surface_items ()
#
# Description:
#   Browses through all surfaces of all planets. From which user doesn't matter
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_surface_items () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM i_vessels");
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_planets ()
#
# Description:
#   Browses through all planets
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_planets () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies");
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_planets_from_sector ()
#
# Description:
#   Browses through all planets from a particulair sector
#
# ParamList
#   $sector_id
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_planets_from_sector ($) {
  my $sector_id = shift;
  errors::assert (errors::is_value ($sector_id));

  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE sector_id=?", $sector_id);
  return $new_handle;
}

# ===========================================================================================================
# init_get_all_planets_with_population ()
#
# Description:
#   Browses through all planets with a population
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_planets_with_population () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_anomalies WHERE type='P' AND population != 0 ORDER BY user_id");
  return $new_handle;
}

