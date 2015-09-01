package px_nebula;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

# ===========================================================================================================
# Create()
#
# Description:
#    Generates a new nebula and returns it's distance and planet id
#
# ParamList
#    planet_name       Name of the nebula (if applicable)
#    order             Planet order from the sun, earth = 3, mars = 2, venus = 4 etc..
#    sector_id         sector_id of sector for this planet
#    user_id           user_id of owner
#    distance          minimum distance from sun
#
# Returns:
#    distance          Distance from the sun
#    nebula_id         ID of nebula in the database
#
sub create {
  my $param = @_[0];

  my ($name, $state, $image, $new_distance, $radius);

  # Generate some standard planet stuff
  $image        = int(rand($px_config::galaxy->{nebula_image_cnt})+1);
  $radius       = int(rand($px_config::galaxy->{planet_size_max})+$px_config::galaxy->{planet_size_min});
  $new_distance = $param->{distance} + int(rand($px_config::galaxy->{planet_distance_max})+$px_config::galaxy->{planet_distance_min}+$radius);
  $name         = "Nebula ".$param->{order};

  # Add it to the planet table
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO s_anomalies (type, sector_id, user_id, state_id,
                                            unknown, name, class, image,
                                            radius, distance, population, population_capacity,
                                            water, temperature, can_mine, can_explore,
                                            next_explore, happieness, sickness)
                                    values ('N', ?, ?, ?,
                                            1, ?, '-', ?,
                                            ?, ?, 0, 0,
                                            0, 0, 0, 0,
                                            0, -1, -1)",
                                          $param->{sector_id}, $param->{user_id}, constants->PLANET_STATE_NEBULA,
                                          $name, $image,
                                          $radius, $new_distance);

  # Grab the id of that nebula
  my $nebula_id = px_mysql::get_last_insert_id ();

  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_ores (planet_id, cur_ores, max_ores, stock_ores) values (?, '', '', '')", $nebula_id);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_surface (planet_id) VALUES (?)", $nebula_id);

  return ($nebula_id, $new_distance);
}



