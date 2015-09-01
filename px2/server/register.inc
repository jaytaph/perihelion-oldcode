use strict;
use lib '../globalperl/';
use px_sector;
use px_planet;
BEGIN { require ('constants.pm'); }

$main::register_privilege = RESTRICTED;

# -------------------------------------------------------------------------
#  Registers a new user to perihelion.
#
#  Returns:
#    PARAMS        Unknown parameters specified
#
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub register () {
  my ($rx, $tx) = @_;

  my ($tmp, $homeplanet_id, $distance);

  item_add ($tx, "pkg_cmd", "REGISTER");

  my $login_user  = $rx->{login_name};
  my $login_pass  = $rx->{login_pass};
  my $sector_name = $rx->{sector};
  my $planet_name = $rx->{planet};
  my $species     = $rx->{species};
  my $name        = $rx->{name};
  my $email       = $rx->{email};
  my $inform      = $rx->{inform};
  my $gender      = $rx->{gender};
  my $city        = $rx->{city};
  my $country     = $rx->{country};

  # No decent params found...
  if ($login_user eq "" or $login_pass eq "" or $sector_name eq "" or $planet_name eq "" or $species eq "" or $name eq "" or $email eq "" or $gender eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  # Set autocommit off. If the database supports it, we can issue a rollback if
  # the registration fails. Particulary interessting when we are debugging the stuff.
  my ($dbh, $sth, $user);
  px_mysql::query (constants->QUERY_NOKEEP, "SET AUTOCOMMIT=0");
  px_mysql::query (constants->QUERY_NOKEEP, "BEGIN");

  $inform = 'N';
  if ($inform eq "on") { $inform = 'Y'; }

  # Add the user and get the id
  px_mysql::query (constants->QUERY_NOKEEP, "USE perihelion");
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO perihelion.u_users (login_name, login_pass, galaxy_db, name, email, inform, gender, city, country) VALUES (?, PASSWORD(?), ?, ?, ?, ?, ?, ?, ?)", $login_user, $login_pass, $px_config::config->{default_db}, $name, $email, $inform, $gender, $city, $country);
  my $user_id = px_mysql::get_last_insert_id ();

  # Get the galaxy configuration
  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM ".$px_config::config->{default_db}.".c_config ORDER BY priority DESC LIMIT 1");
  $px_config::galaxy = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);

  # Make sure we add our stuff in the correct galaxy...
  px_mysql::query (constants->QUERY_NOKEEP, "USE ".$px_config::config->{default_db});

  # --------------------
  # Create our sector
  my $sector_id = px_sector::create ( { user_id=>$user_id, name=>$sector_name });

  # --------------------
  # Create the planets
  my $planet_count = int(rand ($px_config::galaxy->{planet_max})) + $px_config::galaxy->{planet_min};
  my $homeplanet = int (rand ($planet_count)) + 1;  # TODO: Why can't this be random?

  print "planetcount is: $planet_count\n";
  print "homeplanet is: $homeplanet\n";

  for (my $i = 1; $i!= $planet_count+1; $i++) {
    if ($i == $homeplanet) {
       ($homeplanet_id, $distance) = px_planet::create ( { is_home_planet=>"yes", planet_name=>$planet_name, order=>$i, sector_id=>$sector_id, user_id=>$user_id, distance=>$distance } );
	  } else {
      my ($tmp, $distance) = px_planet::create ( { is_home_planet=>"no", order=>$i, sector_id=>$sector_id, user_id=>0, distance=>$distance } );
	  }
	  print "Home planet is: $homeplanet_id\n";
  }


  # --------------------
  # Add the default thingies
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_sectors (user_id, csl_sector_id) VALUES (?, ?)", $user_id, $sector_id.",");
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_anomalies (user_id, csl_discovered_id) VALUES (?, ?)", $user_id, $homeplanet_id.",");
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_users (active, user_id, race, home_planet_id, home_sector_id, credits, science_ratio, science_vessel, science_building, science_invention, science_explore, building_level, explore_level, vessel_level, invention_level, impulse, warp, population) VALUES (0, ?, ?, ?, ?, $px_config::galaxy->{initial_credits}, 50, 50, 50, 0, 0, 0, 0, 0, 0, 0, 0, 100)", $user_id, $species, $homeplanet_id, $sector_id);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_flags (user_id, can_build_explorationship, can_build_tradeship, can_build_battleship, can_warp) VALUES (?, 0, 0, 0, 0)", $user_id);
  px_message::create_message (px_message->MSG_USER, $user_id, "Perihelion", "Welcome to Perihelion",
                              "Welcome to Perihelion. Now, start inhabite your planet and conquer the galaxy...",
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_knownspecies (user_id) VALUES (?)", $user_id);

  # Commit (only functional with different database tables)
  px_mysql::query (constants->QUERY_NOKEEP, "COMMIT");


  item_add ($tx, "status", "STATUS_OK");
  return;
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

