<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification();

  // Extra headers for TD..
  $extra_headers =
        "<STYLE TYPE=\"text/css\" > " .
        "  TD.t { color : white}    " .
        "  TD.f { color : red}      " .
        "</STYLE>";
  print_header ($extra_headers);
  print_title ("Construction");


  $cmd = input_check ("build", "!frmid", "bid", "aid", 0,
                      "show", "aid", 0
                     );

  if ($cmd == "show") {
    // Show homeworld when nothing is set...
    if ($aid == "") {
      show_constructions (user_get_home_planet (user_ourself()));
    } else {
      show_constructions ($aid);
    }
  }

  if ($cmd == "build") {
    $ok = "";
    $errors['PARAMS']  = "Incorrect parameters specified..\n";
    $errors['CREDITS'] = "You don't have enough cash to construct the building...\n";
    $errors['ORE']     = "You don't have enough ores to construct the building...\n";
    $errors['POWER']   = "You need more (advanced) powerplants to construct the building...\n";
    $errors['MAX']     = "You cannot build anymore buildings of this type on the planet...\n";
    $errors['DEPS']    = "You cannot build this building before you have build all dependencies...\n";
    $errors['QUEUE']   = "Maximum number of constructions per planet reached. Wait until other buildings are finished...\n";
    $errors['SCIENCE'] = "You cannot build the construction since you haven't invented it yet...\n";
    $data['building_id'] = $bid;
    $data['anomaly_id'] = $aid;
    $data['user_id'] = user_ourself();
    if (comm_send_to_server ("BUILD", $data, $ok, $errors) == 1) {
      $building = building_get_building ($data['bid']);
      echo "<br><br><br><br>";
      echo "<table align=center border=0>";
      echo "  <tr><th>New construction in progress</th></tr>";
      echo "  <tr><td align=center><img align=center src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/buildings/".$building['image'].".jpg\" width=150 height=150></td></tr>";
      echo "</table>";
    }
  }

  print_footer();
  exit;



// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
//
function show_constructions ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  // Get global information stuff
  $planet  = anomaly_get_anomaly ($anomaly_id);
  $user    = user_get_user ($planet['user_id']);

  // And get the ores from the planet
  $result = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$anomaly_id);
  $ores = sql_fetchrow ($result);

  // Get all buildings that are currently build on the planet
  $surface = planet_get_surface ($anomaly_id);
  $current_buildings = csl ($surface['csl_building_id']);

  // If we've got an headquarter and it's inactive, we cannot build anything.. :(
  if (in_array (BUILDING_HEADQUARTER_INACTIVE, $current_buildings)) {
      print_line ("Your headquarter is currently inactive due to insufficent resources for its upkeep. You cannot build anything on this planet until you replenish your resources.");
      $cannot_build = true;
      return;
    }


  print_subtitle ("Construction on planet ".$planet['name']);

  // And get all buildings, compare wether or not we may build them...
  $result = sql_query ("SELECT * FROM s_buildings ORDER BY id");
  while ($building = sql_fetchrow ($result)) {
    // Default, we can build this
    $cannot_build = false;

// Stage -1: Check planet class when we want to build a headquarter
    if ($building['id'] == BUILDING_HEADQUARTER) {
      if (! planet_is_habitable ($anomaly_id)) { $cannot_build = true; }
    }


// Stage 0: Check building_level
    if ($building['build_level'] > $user['building_level']) {
      $cannot_build = true;
    }

// Stage 1: Building Count Check
    // Build counter check
    if ($building['max'] > 0) {
      $times_already_build=0;
      for ($i=0; $i!=count ($current_buildings); $i++) {
        if (building_active_or_inactive ($current_buildings[$i])==$building['id']) {
          $times_already_build++;
        }
      }
      // Cannot build cause we already have MAX buildings of this kind.. :(
      // building['max'] = 0 means unlimited buildings...
      if ($times_already_build == $building['max']) {
        $cannot_build = true;
      }
    }


// Stage 2: Dependency Check
    // Get all dependencies
    $buildings_needed = csl ($building['csl_depends']);

    // Do we need them? If not, skip dependency-check.
    if (!empty($building['csl_depends'])) {
      $deps_found = count ($buildings_needed);  // Count to zero...
      while (list ($key, $building_dep_id) = each ($buildings_needed)) {
        if ($building_dep_id == "") { $deps_found--; continue; }
        // Get all dependencies
        if (in_array ($building_dep_id, $current_buildings)) {
          $deps_found--;     // Found in current_buildings?
                             // Decrease counter
        }
      }
    } else {      // No need for deps
      $deps_found = 0;      // Zero is good...
    }
    // Not all dependencies found, we cannot build it.. :(
    if ($deps_found > 0) $cannot_build = true;

// Stage 3: Show building if we can build it..
    if ($cannot_build == false) {
      building_show_details ($building['id'], $planet['id'], $user['user_id'], $ores['stock_ores']);
    }
  }
}

?>