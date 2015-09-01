<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Sector view",
               "Here you will find all discovered sectors and planets on one single page. It will also let you claim sectors if appropriate.");

  $cmd = input_check ("show", "sid", "uid", 0,
                      "claim", "!frmid", "!sid", "!ne_name", 0);
  if ($uid == "") $uid = user_ourself();

  if ($cmd == "show") {
   if ($sid == "") {
      sector_show_all_sectors ($uid);
    } else {
      sector_show_sector ($sid, $uid);
    }
  }
  if ($cmd == "claim") {
    $ok = "";
    $errors['PARAMS']  = "Incorrect parameters specified..\n";
    $errors['NAME']    = "The sector name already exists.\n";
    $data['sector_id'] = $sid;
    $data['name']      = convert_crlf_to_px_tag ($ne_name);
    comm_send_to_server ("SECTOR", $data, $ok, $errors);
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
function sector_show_all_sectors ($user_id) {
  assert (isset ($user_id));
  
  global $_RUN;
  
  $homesector = sector_get_sector (user_get_home_sector ($user_id));

  // Get all anomaly counts from the sectors the user has discovered. We DONT want to count
  // the anomalies that the user did not already discovered. Hence the big SQL statement :)
  $result = sql_query ("SELECT a.sector_id, COUNT(*) AS qty FROM s_anomalies AS a, g_anomalies AS g WHERE g.user_id = ".$user_id." AND ( FIND_IN_SET( a.id, g.csl_discovered_id ) OR FIND_IN_SET( a.id, g.csl_undiscovered_id) ) GROUP BY a.sector_id");
  while ($count = sql_fetchrow ($result)) {
    $anomaly_count[$count['sector_id']] = $count['qty'];
  }


  $result = sql_query ("SELECT s.* FROM s_sectors AS s, g_sectors AS g WHERE g.user_id = ".$user_id." AND FIND_IN_SET(s.id, g.csl_sector_id)");
  while ($sector = sql_fetchrow ($result)) {
    $distance = calc_distance ($homesector['distance'], $homesector['angle'], $sector['distance'], $sector['angle']);
    if ($sector['user_id'] == 0) {
      $owner = '<font color=red>unclaimed</font>';
    } else {
      $tmp = user_get_user ($sector['user_id']);
      $owner = $tmp['race'];
    }

    $tmp = array ();
    $tmp['href'] = "sector.php?cmd=".encrypt_get_vars("show")."&sid=".encrypt_get_vars($sector['id']);
    $tmp['id'] = $sector['sector'];
    $tmp['name'] = $sector['name'];
    $tmp['qty'] = $anomaly_count [$sector['id']];
    $tmp['owner'] = $owner;
    $tmp['coordinate'] = $sector['distance']." / ".$sector['angle'];
    $tmp['distance'] = round ($distance);
    $tmp['unround_distance'] = $distance;
    $sector_rows[] = $tmp;  
  }
  uasort ($sector_rows, "show_all_sectors_cmp");



  print_subtitle ("All known sectors and their planets");
  
  foreach ($sector_rows as $sector) {
  	$tmp = array();
  	$tmp['id'] = $sector['id'];
  	$tmp['name'] = $sector['name'];
  	$tmp['qty'] = $sector['qty'];
  	$tmp['owner'] = $sector['owner'];
  	$tmp['coordinate'] = $sector['coordinate'];
  	$tmp['distance'] = $sector['distance'];
  	$tmp['href'] = $sector['href'];
  	$tmpvar[] = $tmp;
  } 
 
  $template = new Smarty ();
  $template->debugging = true;
  help_set_template_vars ($template);
  $template->assign ("sectors", $tmpvar);

  if (isset ($_REQUEST['pager_pos'])) {
  	$template->assign ("pager_pos", $_REQUEST['pager_pos']);
  } else {
  	$template->assign ("pager_pos", 0);
  }
  $template->assign ("theme_path", $_RUN['theme_path']);
  $template->display ($_RUN['theme_path']."/sectors-all.tpl");   
  
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
function sector_show_sector ($sector_id, $user_id) {
  assert (!empty($sector_id));
  assert (!empty($user_id));
  
  global $_RUN;


  // Create the submenu where we can easily move between sectors
  $result = sql_query ("SELECT * FROM g_sectors WHERE user_id = ".$user_id);
  $tmp    = sql_fetchrow ($result);
  $sectors = csl ($tmp['csl_sector_id']);

  $idx = array_search ($sector_id, $sectors);
  $first = reset($sectors);
  $last  = end ($sectors);
  if ($idx == 0) $prev = $sectors[$idx]; else $prev = $sectors[$idx-1];
  if ($idx == count($sectors)-1) $next = $sectors[$idx]; else $next = $sectors[$idx+1];

  create_submenu ( array (
                   "First Sector"     => "sector.php?cmd=".encrypt_get_vars("show")."&sid=".encrypt_get_vars($first),
                   "Previous Sector"  => "sector.php?cmd=".encrypt_get_vars("show")."&sid=".encrypt_get_vars($prev),
                   "Next Sector"      => "sector.php?cmd=".encrypt_get_vars("show")."&sid=".encrypt_get_vars($next),
                   "Last Sector"      => "sector.php?cmd=".encrypt_get_vars("show")."&sid=".encrypt_get_vars($last),
                  )
                 );


  $sector = sector_get_sector ($sector_id);  


  $template = new Smarty ();
  $template->debugging = true;
  help_set_template_vars ($template);
  
  $template->assign ("sector_id", $sector['sector']);
  $template->assign ("sector_name", $sector['name']);
  $template->assign ("sector_coordinate", $sector['distance']." / ".$sector['angle']);
  if ($sector['user_id'] == UID_NOBODY) {
  	$template->assign ("rename_form_visible", "true");  	
  } else {
  	$template->assign ("rename_form_visible", "false");  	
  }
    

  // And now, do all anomalies
  $tmp = user_get_all_anomalies_from_user ($user_id);
  $anomalies = csl ($tmp['csl_discovered_id']);
  $undiscovered_anomalies = csl ($tmp['csl_undiscovered_id']);
  $anomalies = csl_merge_fields ($anomalies, $tmp['csl_undiscovered_id']);

  // We didn't find any discovered anomalies in this sector per default
  $i = 0;
  
  $tmpvar = array();

  // Get anomaly information for all anomalies in the sector
  $result = sql_query ("SELECT * FROM s_anomalies WHERE sector_id=".$sector_id." ORDER BY distance");
  while ($anomaly = sql_fetchrow ($result)) {
    // We didn't discover this anomaly yet, continue with another
    if (!in_array ($anomaly['id'], $anomalies)) continue;
   
    // Thread undiscovered planets different...
    if (in_array ($anomaly['id'], $undiscovered_anomalies)) {    		
    	$tmp['name'] = "Unknown";
    	$tmp['class'] = "";
    	$tmp['population'] = "";
    	$tmp['owner'] = "";
      $tmp['status'] = "";
      $tmp['radius'] = $anomaly['radius'];
      $tmp['distance'] = $anomaly['distance'];
      $tmpvar[] = $tmp;
      continue;
    }
    
    // If we are here, we have found an anomaly with we already
    // discovered and explored, show all the info about it...

    $state = sql_get_state ($anomaly['state_id']);

    if ($anomaly['user_id'] != 0) {
      $race = user_get_race ($anomaly['user_id']);
    } else {
      $race = "Nobody";
    }


    // Show the population status in a different color
    if ($anomaly['population_capacity'] == 0) {
      $p = 0;
    } else {
      $p = ($anomaly['population'] / $anomaly['population_capacity']) * 100;
    }
    $popcol = 'people_class1';
    if ($p > 50) $popcol = 'people_class2';
    if ($p > 75) $popcol = 'people_class3';
    if ($p > 99) $popcol = 'people_class4';

    if (!anomaly_is_planet ($anomaly['id'])) {
      $anomaly['class'] = "";
      $anomaly['population'] = "";
    }

    // Show the class in different colors, A..J are orange... K..M are white... L..Z are red
    $classcol = "class_normal";
    if (planet_is_habitable ($anomaly['id'])) {
      $classcol = 'class_habitable';
    } elseif (planet_is_minable ($anomaly['id'])) {
      $classcol = 'class_minable';
    } else {
      $classcol = 'class_unusable';
    }
   
    $tmp = array ();
    $tmp['name_href'] = "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly['id']);
    $tmp['name'] = $anomaly['name'];
  	$tmp['class'] = $anomaly['class'];
  	$tmp['class_class'] = $classcol;
    if (planet_is_habitable ($anomaly['id'])) {
	    $tmp['population'] = $anomaly['population'];
    } else {
	    $tmp['population'] =  "---";
    }  	
  	$tmp['population_class'] = $popcol;
  	$tmp['owner'] = $race;
    $tmp['status'] = $state;
    $tmp['radius'] = $anomaly['radius'];
    $tmp['distance'] = $anomaly['distance'];
    $tmpvar[] = $tmp;
  }
  
  $template->assign ("anomalies", $tmpvar);
  $template->display ($_RUN['theme_path']."/sectors-item.tpl");
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
// Compares distance from hash $a with hash $b
// This is needed to order the distance of the sectors
function show_all_sectors_cmp ($a, $b) {
  if ($a['unround_distance'] < $b['unround_distance']) return -1;
  if ($a['unround_distance'] > $b['unround_distance']) return 1;
  return 0;
}

?>
