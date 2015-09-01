<?php
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
function planet_show_planet ($planet_id) {
  assert (!empty($planet_id));
  global $_GALAXY;
  global $_CONFIG;
  global $_RUN;

  if (! anomaly_is_planet ($planet_id)) return ;
  
  $planet    = anomaly_get_anomaly ($planet_id);
  $result    = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$planet_id);
  $ore       = sql_fetchrow ($result);
  $stock_ore = ore_csl_to_list ($ore['stock_ores']);
  $race      = user_get_race ($planet['user_id']);
  $sector    = sector_get_sector ($planet['sector_id']);
  $result    = sql_query ("SELECT * FROM s_state WHERE id=".$planet['state_id']);
  $state     = sql_fetchrow ($result);

  if ($race == "") $race = "-";

  $extra_attack = 0;
  $extra_defense = 0;
  $attack = $planet['cur_attack'];
  $defense = $planet['cur_attack'];

  // Count all ships and their attack/defense currently in orbit around the planet
  $result = sql_query ("SELECT COUNT(*) FROM g_vessels AS g, s_vessels AS s WHERE g.planet_id=".$planet_id." AND s.id = g.vessel_id AND s.type='".VESSEL_TYPE_BATTLE."' AND status='ORBIT' AND created=1");
  $tmp = sql_fetchrow ($result);
  $battle_vessels = $tmp[0];

  $result = sql_query ("SELECT COUNT(*) FROM g_vessels AS g, s_vessels AS s WHERE g.planet_id=".$planet_id." AND s.id = g.vessel_id AND s.type='".VESSEL_TYPE_TRADE."' AND status='ORBIT' AND created=1");
  $tmp = sql_fetchrow ($result);
  $trade_vessels = $tmp[0];

  $result = sql_query ("SELECT COUNT(*) FROM g_vessels AS g, s_vessels AS s WHERE g.planet_id=".$planet_id." AND s.id = g.vessel_id AND s.type='".VESSEL_TYPE_EXPLORE."' AND status='ORBIT' AND created=1");
  $tmp = sql_fetchrow ($result);
  $explore_vessels = $tmp[0];

  $result = sql_query ("SELECT SUM(cur_attack) AS CA, SUM(cur_defense) AS CD FROM g_vessels AS g WHERE g.planet_id=".$planet_id." AND status='ORBIT' AND created=1");
  $tmp = sql_fetchrow ($result);
  if (isset ($tmp['CA'])) $extra_attack  = $extra_attack  + $tmp['CA'];
  if (isset ($tmp['CD'])) $extra_defense = $extra_defense + $tmp['CD'];
  
  
  $template = new Smarty ();
  $template->debugging = true;
  
  $template->assign ("sector_name", $sector['name']);
  $template->assign ("planet_name", $planet['name']);
  $template->assign ("image", $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/planets/".$planet['image'].".jpg");
  if ($planet['unknown'] == 1) {
    $template->assign ("cmd", encrypt_get_vars ("claim"));
    $template->assign ("formid", generate_form_id());
    $template->assign ("aid", encrypt_get_vars ($planet_id));
    $template->assign ("rename_form_visible", "true");
  } else {
  	$template->assign ("rename_form_visible", "false");
  }
  $template->assign ("class", $planet['class']);
  $template->assign ("race", $race);
  $template->assign ("state", $state['name']);
  $template->assign ("happieness", planet_get_happy_string ($planet['happieness']));
  $template->assign ("healtieness", planet_get_healty_string ($planet['sickness']));
  $template->assign ("population", $planet['population']);
  $template->assign ("radius", $planet['radius']);
  $template->assign ("distance", $planet['distance']);
  $template->assign ("water", $planet['water']);
  $template->assign ("temperature", $planet['temperature']); 
  for ($i=0; $i!=ore_get_ore_count(); $i++) {
    $tmp = array ();
    $tmp['name'] = ore_get_ore_name($i);
    $tmp['stock'] = $stock_ore[$i];
    $tmpvar[] = $tmp;
  }
  $template->assign ("ores", $tmpvar);
  $template->assign ("attack", $attack);
  $template->assign ("extra_attack", $extra_attack);
  $template->assign ("defense", $defense);
  $template->assign ("extra_defense", $extra_defense);
  $template->assign ("strength", $planet['cur_strength']);
  $template->assign ("orbit_battle", $trade_vessels);
  $template->assign ("orbit_trade", $battle_vessels);
  $template->assign ("orbit_explore", $explore_vessels);
  $template->assign ("description", convert_px_to_html_tags ($planet['description']));
  
  $template->display ($_RUN['theme_path']."/planet-details.tpl");

  


  $commands = array ();
  // If we have at least 1 ship in orbit, we can view them here...
  if ($explore_vessels + $trade_vessels + $battle_vessels > 0) {
    $commands['View Vessels'] = "vessel.php?cmd=".encrypt_get_vars("showaid")."&aid=".encrypt_get_vars($planet_id);
  }

  // If it's a solid-class planet (lower than M), we can view the surface.
  if (planet_is_habitable ($planet_id) or planet_is_minable ($planet_id)) {
    $commands['View Surface'] = "surface.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet_id);
  }

  // If it's our planet we might construct and manufacture,..
  if (anomaly_am_i_owner ($planet['id'])) {
    $commands['Change Description'] = "anomaly.php?cmd=".encrypt_get_vars("description")."&aid=".encrypt_get_vars($planet_id);

    // Only minable or habitable or lower can construct
    if (planet_is_habitable ($planet_id) or planet_is_minable ($planet_id)) {
      $commands['Construct'] = "construct.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet_id);
      $commands['Manufacture'] = "manufacture.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet_id);
    }

    if (planet_has_vesselbuilding_capability ($planet_id)) {
      $commands['Create Vessel'] = "vesselcreate.php?cmd=".encrypt_get_vars("showaid")."&aid=".encrypt_get_vars($planet_id);
    }
  }

  create_submenu ($commands);
}



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
// Calculates the planet output for power in, power out en crew needed.
function planet_calculate_power ($planet) {
  assert (is_array ($planet));

  $totals['power_in']   = 0;
  $totals['power_out']  = 0;


  // Get all buildings that are currently build on the planet
  $surface = planet_get_surface ($planet_id);
  $buildings = csl ($surface['csl_building_id']);
  reset ($buildings);
  while (list ($key, $building_id) = each ($buildings)) {
    if (! building_is_active ($building_id)) continue;

    $building = building_get_building ($building_id);
    $totals['power_in']   = $totals['power_in'] + $building['power_in'];
    $totals['power_out']  = $totals['power_out'] + $building['power_out'];
  }

  return $totals;
}




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
// Returns false is the planet class is not a habitable planet according to the database.
function planet_is_habitable ($planet_id) {
  assert (is_numeric ($planet_id));
  global $_GALAXY;

  $planet = anomaly_get_anomaly ($planet_id);
  if ($planet['class'] == "") return false;

  $i = strpos ($_GALAXY['class_habitable'], $planet['class']);
  if ($i === false) return false;
  return true;
}

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
// Returns false is the planet class is not a minable planet according to the database.
function planet_is_minable ($planet_id) {
  assert (is_numeric ($planet_id));
  global $_GALAXY;

  $planet = anomaly_get_anomaly ($planet_id);
  if ($planet['class'] == "") return false;

  $i = strpos ($_GALAXY['class_minable'], $planet['class']);
  if ($i === false) return false;
  return true;

}

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
function planet_has_vesselbuilding_capability ($planet_id) {
  assert (is_numeric ($planet_id));

  $result   = sql_query ("SELECT * FROM g_surface WHERE planet_id=".$planet_id);
  $buildings = csl_create_array ($result, "csl_building_id");
  reset ($buildings);
  while (list ($key, $building_id) = each ($buildings)) {
    if (! building_is_active ($building_id)) continue;
    if ($building_id == BUILDING_SPACEDOCK or
        $building_id == BUILDING_VESSEL_STATION) {
      return true;
    }
  }
  return false;
}




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
function planet_get_happy_string ($happy_percentage) {
  if ($happy_percentage == -1) return "---";

  if ($happy_percentage < 10) return "Outraged";
  if ($happy_percentage < 20) return "Massive disobedience";
  if ($happy_percentage < 40) return "Unhappy";
  if ($happy_percentage < 50) return "Neutral";
  if ($happy_percentage < 80) return "Content";
  return "Happy";
}

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
function planet_get_healty_string ($healty_percentage) {
  if ($healty_percentage == -1) return "---";

  $healty_percentage = 100 - $healty_percentage;
  if ($healty_percentage < 10) return "Massive Epidemic Breakout";
  if ($healty_percentage < 20) return "Major Epidemic Breakout";
  if ($healty_percentage < 40) return "Epidemic Breakout";
  if ($healty_percentage < 50) return "Major deceases";
  if ($healty_percentage < 80) return "Minor deceases";
  return "Healty";
}





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
$cache_pgs = 0;
function planet_get_surface ($planet_id) {
  assert (isset ($planet_id));
  global $cache_pgs;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_pgs == 0 or $planet_id != $cache_pgs['id']) {
    $result = sql_query ("SELECT * FROM g_surface WHERE planet_id=".$planet_id);
    $tmp    = sql_fetchrow ($result);

    $cache_pgs = array();
    $cache_pgs['id'] = $planet_id;
    $cache_pgs['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_pgs['query'];
}


?>