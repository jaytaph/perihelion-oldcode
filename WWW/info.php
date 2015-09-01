<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();
  
  
  print_header ();
  print_title ("Statistics for ".user_get_fullname(user_ourself()),
               "On this page you can find all your current user information, statistical data and global game information. Use this page to find your weakest spots in your emperium.");

  $cmd = input_check ("show", "uid", 0);

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    show_info ($uid);
  }

  print_footer ();
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
function show_info ($user_id) {
	global $_USER;
	global $_CONFIG;
	global $_RUN;

  $user          = user_get_user ($user_id);

  // User Information     
  $template = new Smarty();  
  help_set_template_vars ($template, "INFO");
   
  $planets_owned = info_get_planetown_count ($user_id);
  $template->assign ("stardate", info_get_stardate());
  $template->assign ("credits", $user['credits']);
  $template->assign ("population", info_get_population_count ($user_id));
  $template->assign ("sectors_owned", info_get_sectorown_count ($user_id));
  $template->assign ("planets_owned", $planets_owned);
    
  list ($minable_count, $habitable_count, $unusable_count, $starbase_count, $wormhole_count, $blackhole_count, $anomalie_count) = info_get_anomaly_statistics ($user_id);
  $template->assign ("minable_count", $minable_count);
  $template->assign ("minable_percentage", round(($minable_count / $planets_owned * 100), 2));
  $template->assign ("habitable_count", $habitable_count);
  $template->assign ("habitable_percentage", round(($habitable_count / $planets_owned * 100), 2));
  $template->assign ("unusable_count", $unusable_count);
  $template->assign ("unusable_percentage", round(($unusable_count / $planets_owned * 100), 2));
  $template->assign ("wormhole_count", $wormhole_count);
  $template->assign ("wormhole_percentage", round(($wormhole_count / $planets_owned * 100), 2));
  $template->assign ("starbase_count", $starbase_count);
  $template->assign ("starbase_percentage", round(($starbase_count / $planets_owned * 100), 2));
  $template->assign ("anomalie_count", $anomalie_count);
  $template->assign ("anomalie_percentage", round(($anomalie_count / $planets_owned * 100), 2));
  
  
  // Tactical Information 
  list ($total_vessels, $evd, $tvd, $bvd) = info_get_vessel_statistics ($user_id);
  $template->assign ("total_vessels", $total_vessels);
  $template->assign ("bvd", $bvd);
  $template->assign ("bvd_percentage", round(($bvd / $total_vessels * 100), 2));
  $template->assign ("tvd", $tvd);
  $template->assign ("tvd_percentage", round(($tvd / $total_vessels * 100), 2));
  $template->assign ("evd", $evd);
  $template->assign ("evd_percentage", round(($evd / $total_vessels * 100), 2));
  
  list ($weak_ship_id, $ws_a) = user_get_weakest_ship ($user_id);
  $weak_ship     = vessel_get_vessel ($weak_ship_id);
  $template->assign ("weakship_name", $weak_ship['name']);
  $template->assign ("weakship_href", "vessel.php?cmd=".encrypt_get_vars("show")."&vid=".encrypt_get_vars($weak_ship_id));
  $template->assign ("weakship_percentage", $ws_a);

  list ($strong_ship_id, $ss_a) = user_get_strongest_ship ($user_id);
  $strong_ship   = vessel_get_vessel ($strong_ship_id);
  $template->assign ("strongship_name", $strong_ship['name']);
  $template->assign ("strongship_href", "vessel.php?cmd=".encrypt_get_vars("show")."&vid=".encrypt_get_vars($strong_ship_id));
  $template->assign ("strongship_percentage", $ss_a);

  list ($weak_planet_id, $wp_a) = user_get_weakest_planet ($user_id);
  $weak_planet   = anomaly_get_anomaly ($weak_planet_id);
  $template->assign ("weakplanet_name", $weak_planet['name']);
  $template->assign ("weakplanet_href", "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($weak_planet_id));
  $template->assign ("weakplanet_percentage", $wp_a);

  list ($strong_planet_id, $sp_a) = user_get_strongest_planet ($user_id);
  $strong_planet = anomaly_get_anomaly ($strong_planet_id);
  $template->assign ("strongplanet_name", $strong_planet['name']); 
  $template->assign ("strongplanet_href", "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($strong_planet_id));
  $template->assign ("strongplanet_percentage", $sp_a);
  
  // Other Information
  list ($bd, $vd, $id) = info_get_invention_statistics ($user_id);
  $template->assign ("buildings_discovered_percentage", $bd);
  $template->assign ("vessels_discovered_percentage", $vd);
  $template->assign ("inventions_discovered_percentage", $id);
  $template->assign ("impulse_discovered", $user['impulse']);
  $template->assign ("warp_discovered", number_format( $user['warp']/10, 1));
  
    
  // Server status
  if (check_heartbeat_online ()) {
    $template->assign ("heartbeat_status", "online");
  } else {
  	$template->assign ("heartbeat_status", "offline");
  }

  if (check_server_online ()) {
    $template->assign ("commserver_status", "online");

    $result = sql_query ("SELECT * FROM perihelion.pxs_info WHERE galaxy_id LIKE '".$_USER['galaxy_db']."'");  
    $server_status = sql_fetchrow ($result);

    $result = sql_query ("SELECT UNIX_TIMESTAMP()");
    $row = sql_fetchrow ($result);
    $server_status['uptime'] = $row['0'] - $server_status['uptime'];
    
    $template->assign ("commserver_uptime", calculate_uptime ($server_status['uptime']));
  	$template->assign ("commserver_spawns", $server_status['spawns']);
  	$template->assign ("commserver_commands", $server_status['commands']);
  	$template->assign ("commserver_status_ok", $server_status['status_ok']);
  	$template->assign ("commserver_status_err", $server_status['status_err']);
  } else {
  	$template->assign ("commserver_status", "offline");
  }

  if (check_mysql_online ()) {
    $template->assign ("mysql_status", "online");

    // Get the mysql status and make it a nice hash
    $result = sql_query ("SHOW STATUS");
    while ($row = sql_fetchrow ($result)) {
      $sql_status[$row['Variable_name']] = $row['Value'];
    }

    $template->assign ("mysql_uptime", calculate_uptime ($sql_status['Uptime']));
    $template->assign ("mysql_queries", $sql_status['Questions']);
    $template->assign ("mysql_select", $sql_status['Com_select']);
    $template->assign ("mysql_insert", $sql_status['Com_insert']);
    $template->assign ("mysql_update", $sql_status['Com_update']);
    $template->assign ("mysql_bytes_received", $sql_status['Bytes_received']);
    $template->assign ("mysql_bytes_sent", $sql_status['Bytes_sent']);
  } else {
  	$template->assign ("mysql_status", "offline");
  }
   
  $template->display ($_RUN['theme_path']."/info.tpl");
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
function check_heartbeat_online () {
  exec ("ps afx | grep perl", $output);
  foreach ($output as $line) {
    if (preg_match ("/heartbeat\.pl/", $line)) return true;
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
function check_server_online () {
  exec ("ps afx | grep PXServer", $output);
  foreach ($output as $line) {
    if (preg_match ("/\d PXServer/", $line)) return true;

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
function check_mysql_online () {
  exec ("ps -afx | grep mysqld", $output);
  foreach ($output as $line) {
    if (preg_match ("/safe_mysqld/", $line)) return true;
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
// Get the vessel statistics
function info_get_vessel_statistics ($user_id) {
  assert (isset ($user_id));

  // Get total number of vessels
  $result = sql_query ("SELECT COUNT(*) FROM g_vessels WHERE user_id = ".$user_id);
  $row = sql_fetchrow ($result);
  $total_vessels = $row['0'];

  // Get total number of exploration vessels
  $result = sql_query ("SELECT COUNT(*) FROM g_vessels AS g, s_vessels AS s WHERE g.vessel_id = s.id AND s.type='E' AND g.user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $total_exploration_vessels = $row['0'];

  // Get total number of trade vessels
  $result = sql_query ("SELECT COUNT(*) FROM g_vessels AS g, s_vessels AS s WHERE g.vessel_id = s.id AND s.type='T' AND g.user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $total_trade_vessels = $row['0'];

  // Get total number of battleship vessels
  $result = sql_query ("SELECT COUNT(*) FROM g_vessels AS g, s_vessels AS s WHERE g.vessel_id = s.id AND s.type='B' AND g.user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $total_battleship_vessels = $row['0'];

  return array ($total_vessels, $total_exploration_vessels, $total_trade_vessels, $total_battleship_vessels);
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
function info_get_anomaly_statistics ($user_id) {
  assert (isset ($user_id));

  $minable = 0;
  $habitable = 0;
  $unusable = 0;
  $starbase = 0;
  $wormhole = 0;
  $anomalies = 0;
  $blackhole = 0;

  $result = sql_query ("SELECT * FROM s_anomalies WHERE user_id = ".$user_id);
  while ($anomaly = sql_fetchrow ($result)) {
    if (anomaly_is_planet ($anomaly['id'])) {
      if (planet_is_habitable ($anomaly['id'])) $habitable++;
        elseif (planet_is_minable ($anomaly['id'])) $minable++;
        else $unusable++;
    } elseif (anomaly_is_wormhole ($anomaly['id'])) {
      $wormhole++;
    } elseif (anomaly_is_starbase ($anomaly['id'])) {
      $starbase++;
    } elseif (anomaly_is_blackhole ($anomaly['id'])) {
      $blackhole++;
    } else {
      $anomalies++;
    }
  }
  return array ($minable, $habitable, $unusable, $starbase, $wormhole, $blackhole, $anomalies);
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
// Get invention percentages
function info_get_invention_statistics ($user_id) {
  assert (isset ($user_id));

  $user = user_get_user ($user_id);

  $result = sql_query ("SELECT COUNT(*) FROM s_buildings");
  $row    = sql_fetchrow ($result);
  $tmp1   = $row['0'];
  $result = sql_query ("SELECT COUNT(*) FROM s_buildings WHERE build_level <= ".$user['building_level']);
  $row    = sql_fetchrow ($result);
  $tmp2   = $row['0'];
  $building_percentage = round (($tmp2 / $tmp1 * 100), 2);

  $result = sql_query ("SELECT COUNT(*) FROM s_vessels");
  $row    = sql_fetchrow ($result);
  $tmp1   = $row['0'];
  $result = sql_query ("SELECT COUNT(*) FROM s_vessels WHERE build_level <= ".$user['vessel_level']);
  $row    = sql_fetchrow ($result);
  $tmp2   = $row['0'];
  $vessel_percentage = round (($tmp2 / $tmp1 * 100), 2);

  $result = sql_query ("SELECT COUNT(*) FROM s_inventions");
  $row    = sql_fetchrow ($result);
  $tmp1   = $row['0'];
  $result = sql_query ("SELECT COUNT(*) FROM s_inventions WHERE build_level <= ".$user['building_level']);
  $row    = sql_fetchrow ($result);
  $tmp2   = $row['0'];
  $item_percentage = round (($tmp2 / $tmp1 * 100), 2);

  return array ($building_percentage, $vessel_percentage, $item_percentage);
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
// Get total population
function info_get_population_count ($user_id) {
  assert (isset ($user_id));

  $result = sql_query ("SELECT SUM(population) FROM s_anomalies WHERE user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $pax = $row['0'];

  return $pax;
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
// Get the number of sectors owned
function info_get_sectorown_count ($user_id) {
  assert (isset ($user_id));

  $result = sql_query ("SELECT COUNT(*) FROM s_sectors WHERE user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $sectors_owned = $row['0'];

  return $sectors_owned;
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
// Get the number of planets owned
function info_get_planetown_count ($user_id) {
  assert (isset ($user_id));

  $result = sql_query ("SELECT COUNT(*) FROM s_anomalies WHERE user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $planets_owned = $row['0'];

  return $planets_owned;
}


?>


