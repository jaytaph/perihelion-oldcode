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
function building_is_active ($building_id) {
  assert (is_numeric ($building_id));

  return ($building_id >= 0);
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
function building_active_or_inactive ($building_id) {
  assert (is_numeric ($building_id));

  return abs($building_id);
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
$cache_bgb = 0;
function building_get_building ($building_id) {
  assert (is_numeric ($building_id));

  global $cache_bgb;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_bgb == 0 or $building_id != $cache_bgb['id']) {
    $result = sql_query ("SELECT * FROM s_buildings WHERE id=".$building_id);
    $tmp    = sql_fetchrow ($result);

    $cache_bgb = array();
    $cache_bgb['id'] = $building_id;
    $cache_bgb['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_bgb['query'];
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
function building_show_details ($building_id, $planet_id, $user_id, $stock_ores) {
    assert (is_numeric ($building_id));
    assert (is_numeric ($planet_id));
    assert (is_numeric ($user_id));
    assert (is_string ($stock_ores));

    global $_GALAXY;
    global $_CONFIG;

    $build_option = 1;

    // Check our mode, we can just look at details, or let the user build
    if ($planet_id == 0 and $user_id == 0 and $stock_ores == "") {
      $build_option = 0;
    }

    $building = building_get_building ($building_id);

    if ($build_option) {
      $cannot_build = false;
      $planet = anomaly_get_anomaly ($planet_id);
      $totals = calc_planet_totals ($planet_id);
      $planet_ores = ore_csl_to_list ($stock_ores);
      $building_ores = ore_csl_to_list ($building['initial_ores']);
    } else {
      $cannot_build = true;
      $totals['power_out'] = 0;
      $totals['power_in'] = 0;
      $planet_ores = ore_csl_to_list ("");
      $building_ores = ore_csl_to_list ("");
    }
    

    $href = "construct.php?cmd=".encrypt_get_vars ("build").
                         "&bid=".encrypt_get_vars ($building_id).
                         "&aid=".encrypt_get_vars ($planet_id);
                         
    $template = new Template ($_CONFIG['TEMPLATE_PATH']."/building-details.tpl", E_YAPTER_ALL);

    $template->set ("name", $building['name']);
    $template->set ("image", $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/buildings/".$building['image'].".jpg");
    $template->set ("construction_href", $href);
    $template->set ("description", $building['description']);
    $template->set ("rule", $building['rule']);
    $template->set ("class", "");
    
    
    $template->set ("power_needed", $building['power_in']);
    $template->set ("power_output", $building['power_out']);
    $template->set ("attack", $building['attack']);
    $template->set ("defense", $building['defense']);
    $template->set ("strength", $building['strength']);

    if ($build_option) {
    	$template->hide ("block_build");
    	$template->hide ("block_build2");
    } else {
    	$template->hide ("block_nobuild");
    	$template->hide ("block_nobuild2");
    }
   
    $template->parse();
    $template->spit();


/*
    echo "<table border=1 cellpadding=0 cellspacing=0 align=center width=50%>";

// Building name
    echo "  <tr><th colspan=2>".$building['name']."</th></tr>";

// Plaatje plus ADS etc
    echo "  <tr>";
    echo "    <td align=center valign=top bgcolor=black>";
    echo "              <table border=0 cellpadding=0 cellspacing=0>";
    echo "                <tr>";
    echo "                   <td >";
    echo "                    <table align=left border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                      <tr><td width=100><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/buildings/".$building['image'].".jpg\" width=150 height=150></td></tr>";
    echo "                    </table>";
    echo "                 </td>";
    echo "               </tr>";
    echo "             </table>";
    echo "    </td>";
    echo "    <td align=left valign=top>";
    if ($build_option) {
      if (($totals['power_out']-$totals['power_in']) < $building['power_in']) {
          $class="f";
          $cannot_build = true;
      } else {
          $class="t";
      }
    } else {
      $class="t";
    }
    echo "             <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Power Needed</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$building['power_in']." uts&nbsp;</td>";
    echo "                </tr>";
    $class = 't';
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Power Output</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$building['power_out']." uts&nbsp;</td>";
    echo "                </tr>";
    echo "<tr><td colspan=3><hr></td></tr>";
    $class = 't';
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Attack</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$building['attack']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Defense</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$building['defense']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Strength</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$building['strength']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "              </table>";
    echo "    </td>";
    echo "  </tr>";

    if ($build_option) {
      // Costs + ores  (initial / upkeep)
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
      echo "  <tr><td>";
      $cannot_build = sbt_initial_ores ($cannot_build, $building_id, $user_id, $planet_ores);
      echo "  </td><td>";
      $cannot_build = sbt_upkeep_ores ($cannot_build, $building_id, $user_id, $planet_ores);
      echo "  </td></tr>";
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
    } else {
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
      echo "  <tr><td>";
      sbt_initial_ores (0, $building_id, $user_id, $planet_ores);
      echo "  </td><td>";
      sbt_upkeep_ores (0, $building_id, $user_id, $planet_ores);
      echo "  </td></tr>";
      echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
    }

// Print rule and description
    if ($building['rule'] != "") {
      echo "<tr><td colspan=2><table border=0 cellspacing=5><tr><td>Effect: ".$building['rule']."</td></tr></table></td></tr>";
    }

    if ($building['description'] != "") {
      echo "<tr><td colspan=2><table border=0 cellspacing=5><tr><td>".$building['description']."</td></tr></table></td></tr>";
    }

// Print building possibility
    if ($build_option) {
      if ($cannot_build == false) {
        echo "<tr><th colspan=2><a href=construct.php?cmd=".encrypt_get_vars ("build").
                                                    "&bid=".encrypt_get_vars ($building['id']).
                                                    "&aid=".encrypt_get_vars ($planet['id']).
                                                    ">BUILD IT</a></th></tr>";
      } else {
        echo "<tr><th colspan=2>CANNOT BUILD</th></tr>";
      }
   }
    echo "</table>\n";
    echo "<br><br>\n";
*/
    
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
function sbt_initial_ores ($cannot_build, $building_id, $user_id, $stock_ores) {
  assert (is_bool ((bool)$cannot_build));
  assert (is_numeric ($building_id));
  assert (is_numeric ($user_id));
  assert (is_array ($stock_ores));

  $building = building_get_building ($building_id);
  $building_ores = ore_csl_to_list ($building['initial_ores']);
  $class = "t";

  if ($user_id == 0) {
    $build_option = 0;
  } else {
    $build_option = 1;
    $user = user_get_user ($user_id);
  }

  if ($build_option) {
    if ($building['initial_costs'] > $user['credits']) {
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
  echo "    <td class=".$class." width=34%> &nbsp;".$building['initial_costs']." cr&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    if ($build_option) {
      if ($building_ores[$i] > $stock_ores[$i]) {
        $class = "f";
        $cannot_build = true;
      } else {
        $class = "t";
      }
    }
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$building_ores[$i]." tons&nbsp;</td>\n";
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
function sbt_upkeep_ores ($cannot_build, $building_id, $user_id, $stock_ores) {
  assert (is_bool ((bool)$cannot_build));
  assert (is_numeric ($building_id));
  assert (is_numeric ($user_id));
  assert (is_array ($stock_ores));

  $building = building_get_building ($building_id);
  $building_upkeep_ores = ore_csl_to_list ($building['upkeep_ores']);

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
  echo "    <td class=".$class." width=34%> &nbsp;".$building['upkeep_costs']." cr&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$building_upkeep_ores[$i]." tons&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";

  return $cannot_build;
}




?>