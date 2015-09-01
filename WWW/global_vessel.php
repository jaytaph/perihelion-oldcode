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
function vessel_select_vessel_by_user ($user_id, $href_url, $title, $showtrades) {
  assert (is_numeric ($user_id));
  assert (!empty($href_url));
  assert (!empty($title));
  assert (!empty($showtrades));
  
  global $_RUN;


  print_subtitle ($title);
  
  $template = new Smarty ();
  $template->debugging = true;

  $result = sql_query ("SELECT g.* FROM g_vessels AS g, s_vessels AS s WHERE g.user_id=".$user_id." AND g.created=1 AND g.vessel_id = s.id ORDER BY s.type, g.id");
  while ($vessel = sql_fetchrow ($result)) {  	
    if ($showtrades == NO_SHOW_TRADEROUTES) {
      $result2 = sql_query ("SELECT * FROM a_trades WHERE vessel_id = ".$vessel['id']);
      if (! sql_fetchrow ($result2)) {
      	$tmpvar[] = vessel_show_table_row ($vessel['id'], $href_url);
      }
    } else {
    	$tmpvar[] = vessel_show_table_row ($vessel['id'], $href_url);
    }  	
  }  
  
  $template->assign ("vessels", $tmpvar);
  $template->display ($_RUN['theme_path']."/vessel-user.tpl");   
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
function vessel_show_table_row ($vessel_id, $href_url, $show_species = "NO") {
  assert (is_numeric ($vessel_id));
  assert (!empty($href_url));

  global $_GALAXY;
  global $_CONFIG;

  // Get information
  $vessel     = vessel_get_vessel ($vessel_id);
  $vesseltype = vessel_get_vesseltype ($vessel_id);
  $planet     = anomaly_get_anomaly ($vessel['planet_id']);
  $race       = user_get_race ($vessel['user_id']);

  $img  = "explore.jpg";
  $type = "Unknown Type";
  if ($vesseltype['type'] == VESSEL_TYPE_EXPLORE) { $img = "explore.jpg"; $type = $vesseltype['name']; }
  if ($vesseltype['type'] == VESSEL_TYPE_TRADE)   { $img = "trade.jpg";   $type = $vesseltype['name']; }
  if ($vesseltype['type'] == VESSEL_TYPE_BATTLE)  { $img = "battle.jpg";  $type = $vesseltype['name']; }


  $tmp['image'] = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/ships/".$img;
  $tmp['href'] = $href_url."?cmd=".encrypt_get_vars("showvid")."&vid=".encrypt_get_vars($vessel['id']);
  $tmp['name'] = $vessel['name'];
  $tmp['type'] = $type;
  $tmp['status'] = vessel_get_current_status ($vessel_id, true);
  $tmp['status_nohref'] = vessel_get_current_status ($vessel_id, false);  
  $tmp['distance'] = $vessel['distance'];
  $tmp['angle'] = $vessel['angle'];
  $tmp['race'] = $race;
  
  return $tmp;
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
function vessel_show_type_details ($vessel_id, $anomaly_id, $user_id, $stock_ores) {
    assert (is_numeric ($vessel_id));
    assert (is_numeric ($anomaly_id));
    assert (is_numeric ($user_id));
    assert (is_string ($stock_ores));
    global $_GALAXY;
    $build_option = 1;

    // Check our mode, we can just look at details, or let the user build
    if ($anomaly_id == 0 and $user_id == 0 and $stock_ores == "") {
      $build_option = 0;
    }

    $vessel = vessel_get_type ($vessel_id);

    if ($build_option) {
      $cannot_build = false;
      $planet = anomaly_get_anomaly ($anomaly_id);
      $planet_ores = ore_csl_to_list ($stock_ores);
      $vessel_ores = ore_csl_to_list ($vessel['initial_ores']);
    } else {
      $planet_ores = ore_csl_to_list ("");
      $vessel_ores = ore_csl_to_list ("");
    }


    echo "<table border=1 cellpadding=0 cellspacing=0 align=center width=50%>";

// vessel name
    echo "  <tr class=wb><th colspan=2>".$vessel['name']."</th></tr>";

// Plaatje plus ADS etc
    echo "  <tr>";
    echo "    <td align=center valign=top bgcolor=black>";
    echo "              <table border=0 cellpadding=0 cellspacing=0>";
    echo "                <tr>";
    echo "                   <td >";
    echo "                    <table align=left border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                      <tr><td width=100><img src=\"".$_GALAXY['image_dir']."/vessels/".$vessel['image'].".jpg\" width=150 height=150></td></tr>";
    echo "                    </table>";
    echo "                 </td>";
    echo "               </tr>";
    echo "             </table>";
    echo "    </td>";
    echo "    <td align=left valign=top>";

    $class = 't';
    echo "             <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Attack</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$vessel['attack']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Defense</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$vessel['defense']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Strength</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$vessel['strength']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr><td colspan=3><hr></td></tr>\n";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Max Impulse</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$vessel['max_impulse']." %&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Max Warp</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".number_format($vessel['max_warp'] / 10, 1)."&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr><td colspan=3><hr></td></tr>\n";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Max Weapons</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$vessel['max_weapons']." pcs&nbsp;</td>";
    echo "                </tr>";
    echo "              </table>";
    echo "    </td>";
    echo "  </tr>";

    if ($build_option) {
      // Costs + ores  (initial / upkeep)
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
      echo "  <tr><td>";
      $cannot_build = svt_initial_ores ($cannot_build, $vessel_id, $user_id, $planet_ores);
      echo "  </td><td>";
      $cannot_build = svt_upkeep_ores ($cannot_build, $vessel_id, $user_id, $planet_ores);
      echo "  </td></tr>";
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
    } else {
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
      echo "  <tr><td>";
      svt_initial_ores (0, $vessel_id, $user_id, $planet_ores);
      echo "  </td><td>";
      svt_upkeep_ores (0, $vessel_id, $user_id, $planet_ores);
      echo "  </td></tr>";
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
    }

// Print description
    if ($vessel['description'] != "") {
      echo "<tr><td colspan=2><table border=0 cellspacing=5><tr><td>".$vessel['description']."</td></tr></table></td></tr>";
    }

// Print building possibility
    if ($build_option) {
      if ($cannot_build == false) {
        echo "<tr><th colspan=2><a href=vessel.php?cmd=".encrypt_get_vars ("create").
                                                 "&vid=".encrypt_get_vars ($vessel_id).
                                                 "&aid=".encrypt_get_vars ($anomaly_id).
                                                 ">BUILD IT</a></th></tr>";
      } else {
        echo "<tr><th colspan=2>CANNOT BUILD</th></tr>";
      }
   }

    echo "</table>\n";
    echo "<br><br>\n";
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
function vessel_calc_extra_attack_and_defense_points ($vessel_id) {
   assert (is_numeric ($vessel_id));

  $result   = sql_query ("SELECT * FROM i_vessels WHERE vessel_id=".$vessel_id);
  $current_items = csl_create_array ($result, "csl_weapon_id");
  $current_items = array_count_values ($current_items);

  $total_attack = 0;
  $total_defense = 0;

  reset ($current_items);
  while (list ($item_id, $quantity) = each ($current_items)) {
    $invention = item_get_item ($item_id);
    $total_attack += $invention['attack'] * $quantity;
    $total_defense += $invention['defense'] * $quantity;
  }

  return array (floor ($total_attack / 100), floor ($total_defense / 100));
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
function vessel_in_traderoute ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $result = sql_query ("SELECT id FROM a_trades WHERE vessel_id=".$vessel_id);
  if (sql_countrows ($result) == 0) return false;
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
function vessel_is_in_orbit ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);
  if ($vessel['status'] == "ORBIT") return true;
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
function vessel_is_flying ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);
  if ($vessel['status'] == "FLYING") return true;
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
function vessel_is_in_space ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);
  if ($vessel['status'] == "SPACE") return true;
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
function vessel_is_battleship ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);
  if ($vessel['type'] == VESSEL_TYPE_BATTLE) return true;
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
$cache_vgv = 0;
function vessel_get_vessel ($vessel_id) {
  assert (isset ($vessel_id));
  global $cache_vgv;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_vgv == 0 or $vessel_id != $cache_vgv['id']) {
    $result = sql_query ("SELECT * FROM g_vessels WHERE id=".$vessel_id." AND created=1");
    $tmp    = sql_fetchrow ($result);

    $cache_vgv = array();
    $cache_vgv['id'] = $vessel_id;
    $cache_vgv['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_vgv['query'];
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
$cache_vgvt = 0;
function vessel_get_vesseltype ($vessel_id) {
  assert (is_numeric ($vessel_id));
  global $cache_vgvt;

  $vessel = vessel_get_vessel ($vessel_id);

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_vgvt == 0 or $vessel_id != $cache_vgvt['id']) {
    $result = sql_query ("SELECT * FROM s_vessels WHERE id=".$vessel['vessel_id']);
    $tmp    = sql_fetchrow ($result);

    $cache_vgvt = array();
    $cache_vgvt['id'] = $vessel_id;
    $cache_vgvt['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_vgvt['query'];
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
$cache_vgt = 0;
function vessel_get_type ($vessel_id) {
  assert (isset ($vessel_id));
  global $cache_vgt;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_vgt == 0 or $vessel_id != $cache_vgt['id']) {
    $result = sql_query ("SELECT * FROM s_vessels WHERE id=".$vessel_id);
    $tmp    = sql_fetchrow ($result);

    $cache_vgt = array();
    $cache_vgt['id'] = $vessel_id;
    $cache_vgt['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_vgt['query'];
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
function vessel_get_current_status ($vessel_id, $create_hyperlink = true) {
  assert (is_numeric ($vessel_id));
  assert (is_bool ($create_hyperlink));

  $vessel = vessel_get_vessel ($vessel_id);


  $status = "Unknown status";

  // Are we in orbit?
  if (vessel_is_in_orbit ($vessel_id)) {
    $anomaly = anomaly_get_anomaly ($vessel['planet_id']);
    if (anomaly_is_planet ($anomaly['id'])) {
    	if ($create_hyperlink) {
        $status = "Orbiting planet <a href=anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly['id']).">".$anomaly['name']."</a>";
      } else {
      	$status = "Orbiting planet ".$anomaly['name'];
      }
    }
    if (anomaly_is_nebula ($anomaly['id'])) {
      if ($create_hyperlink) {
        $status = "Hiding in nebula <a href=anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly['id']).">".$anomaly['name']."</a>";
      } else {
      	$status = "Hiding in nebula ".$anomaly['name'];
      }
    }
    if (anomaly_is_starbase ($anomaly['id'])) {
    	if ($create_hyperlink) {
        $status = "Docking at starbase <a href=anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly['id']).">".$anomaly['name']."</a>";
      } else {
      	$status = "Docking at starbase ".$anomaly['name'];
      }
    }
  }

  // Is the vessel flying?
  if (vessel_is_flying ($vessel_id)) {
    if ($vessel['dst_planet_id'] == 0) {
      // Are we flying to a sector or deep space?
      if ($vessel['dst_sector_id'] == 0) {
          $status = "Flying to ".$vessel['dst_distance']." / ".$vessel['dst_angle'];
      } else {
        $tmp = sector_get_sector ($vessel['dst_sector_id']);
        if ($create_hyperlink) {
          $status = "Flying to sector <a href=sector.php?id=".encrypt_get_vars($tmp['id']).">".$tmp['name']."</a>";
        } else {
        	$status = "Flying to sector ".$tmp['name'];
        }
      }
    } else {
      // Are we flying to a planet?
      $tmp_anomaly = anomaly_get_anomaly ($vessel['dst_planet_id']);
      if (anomaly_is_planet ($tmp_anomaly['id'])) {
        if (anomaly_is_discovered_by_user ($tmp_anomaly['id'], user_ourself())) {
        	if ($create_hyperlink) {
            $status = "Flying to planet <a href=anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($tmp_anomaly['id']).">".$tmp_anomaly['name']."</a>";
          } else {
          	$status = "Flying to planet ".$tmp_anomaly['name'];
          }
        } else {
          $status = "Flying to an unknown anomaly.";
        }
      }
      if (anomaly_is_starbase ($tmp_anomaly['id'])) {
        $status = "Flying to starbase ".$tmp_anomaly['name'];
      }
      if (anomaly_is_nebula ($tmp_anomaly['id'])) {
        $status = "Flying to nebula ".$tmp_anomaly['name'];
      }
      if (anomaly_is_wormhole ($tmp_anomaly['id'])) {
        $status = "Flying to wormhole ".$tmp_anomaly['name'];
      }
    }
  }

  // Are we halted in deep space?
  if (vessel_is_in_space ($vessel_id) and $vessel['sector_id'] == 0) {
    $status = "Located in deep space (".$vessel['distance']." / ".$vessel['angle'].")";
  }

  // Or are we bordering a sector?
  if (vessel_is_in_space ($vessel_id) and $vessel['sector_id'] != 0) {
    $sector = sector_get_sector ($vessel['sector_id']);
    $status = "Bordering sector ".$sector['name'];
  }

  if (vessel_in_traderoute ($vessel_id)) {
    $status = "Part of traderoute.";
  }

  // TODO: change this into vessel_is_in_convoy ($vessel_id)
  if ($vessel['status'] == "CONVOY") {
    $result = sql_query ("SELECT * FROM s_convoys WHERE id=".$vessel['convoy_id']);
    $tmp = sql_fetchrow ($result);
    if ($create_hyperlink) {
      $status = "Part of convoy <a href=convoy.php?id=".encrypt_get_vars($tmp['id']).">".$tmp['name']."</a>";
    } else {
    	$status = "Part of convoy ".$tmp['name'];
    }
  }

  return $status;
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
function svt_initial_ores ($cannot_build, $vessel_id, $user_id, $stock_ores) {
  assert (is_bool ((bool)$cannot_build));
  assert (is_numeric ($vessel_id));
  assert (is_numeric ($user_id));
  assert (is_array ($stock_ores));

  $vessel = vessel_get_type ($vessel_id);
  $vessel_ores = ore_csl_to_list ($vessel['initial_ores']);
  $class = "t";

  if ($user_id == 0) {
    $build_option = 0;
  } else {
    $build_option = 1;
    $user = user_get_user ($user_id);
  }

  if ($build_option) {
    if ($vessel['initial_costs'] > $user['credits']) {
      $class = "f";
      $cannot_build = true;
    } else {
      $class = "t";
    }
  }

  echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "  <tr><th colspan=3>Initial costs</th></tr>";

  echo "  <tr>\n";
  echo "    <td class=".$class." width=33%> &nbsp;<strong>Credits</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=1%>  &nbsp;<strong>:</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=34%> &nbsp;".$vessel['initial_costs']." cr&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    if ($build_option) {
      if ($vessel_ores[$i] > $stock_ores[$i]) {
        $class = "f";
        $cannot_build = true;
      } else {
        $class = "t";
      }
    }
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$vessel_ores[$i]." tons&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";

  return $cannot_build;
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
function svt_upkeep_ores ($cannot_build, $vessel_id, $user_id, $stock_ores) {
  assert (is_bool ((bool)$cannot_build));
  assert (is_numeric ($vessel_id));
  assert (is_numeric ($user_id));
  assert (is_array ($stock_ores));

  $vessel = vessel_get_type ($vessel_id);
  $vessel_upkeep_ores = ore_csl_to_list ($vessel['upkeep_ores']);

  if ($user_id == 0) {
    $build_option = 0;
  } else {
    $build_option = 1;
    $user = user_get_user ($user_id);
  }

  $class="t";
  echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "  <tr><th colspan=3>Upkeep costs</th></tr>";
  echo "  <tr>\n";
  echo "    <td class=".$class." width=33%> &nbsp;<strong>Credits</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=1%>  &nbsp;<strong>:</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=34%> &nbsp;".$vessel['upkeep_costs']." cr&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$vessel_upkeep_ores[$i]." tons&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";

  return $cannot_build;
}





?>