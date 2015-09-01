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
function invention_is_active_on_planet ($item) {
  if ($item['type'] == ITEM_TYPE_PLANET) return 1;
  return 0;
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
function invention_is_active_on_vessel ($item, $vessel) {
  if ($item['type'] == ITEM_TYPE_WEAPON and vessel_is_battleship ($vessel)) return true;
  if ($item['type'] == ITEM_TYPE_VESSEL) return true;
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
function item_get_type ($item_id) {
  $item = item_get_item ($item_id);

  if ($item['type'] == 'P') return "Planetary";
  if ($item['type'] == 'V') return "Vessel";
  if ($item['type'] == 'W') return "Weaponry";

  return "Unknown";
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
$cache_igi = 0;
function item_get_item ($item_id) {
  assert (isset ($item_id));
  global $cache_igi;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_igi == 0 or $item_id != $cache_igi['id']) {
    $result = sql_query ("SELECT * FROM s_inventions WHERE id=".$item_id);
    $tmp    = sql_fetchrow ($result);

    $cache_igi = array();
    $cache_igi['id'] = $item_id;
    $cache_igi['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_igi['query'];
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
function invention_show_details ($invention_id, $planet_id, $user_id, $stock_ores) {
    assert (is_numeric ($invention_id));
    assert (is_numeric ($planet_id));
    assert (is_numeric ($user_id));
    assert (is_string ($stock_ores));

    global $_GALAXY;

    $build_option = 1;

    // Check our mode, we can just look at details, or let the user build
    if ($planet_id == 0 and $user_id == 0 and $stock_ores == "") {
      $build_option = 0;
    }
    $invention = item_get_item ($invention_id);

    if ($build_option) {
      $cannot_build = false;
      $user = user_get_user ($user_id);
      $planet = anomaly_get_anomaly ($planet_id);
      $planet_ores = csl ($stock_ores);
      $invention_ores = ore_csl_to_list ($invention['initial_ores']);
    } else {
      $planet_ores = ore_csl_to_list ("");
      $invention_ores = ore_csl_to_list ("");
    }



    echo "<table border=1 cellpadding=0 cellspacing=0 align=center width=50%>";

// invention name
    echo "  <tr class=wb><th colspan=2>".$invention['name']."</th></tr>";

// Plaatje plus ADS etc
    echo "  <tr>";
    echo "    <td align=center valign=top bgcolor=black>";
    echo "              <table border=0 cellpadding=0 cellspacing=0>";
    echo "                <tr>";
    echo "                   <td >";
    echo "                    <table align=left border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                      <tr><td width=100><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/inventions/".$invention['image'].".jpg\" width=150 height=150></td></tr>";
    echo "                    </table>";
    echo "                 </td>";
    echo "               </tr>";
    echo "             </table>";
    echo "    </td>";
    echo "    <td align=left valign=top bgcolor=black>";

    $class = 't';
    echo "             <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Function</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".item_get_type ($invention['id'])."&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr><td colspan=3><hr></td></tr>\n";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Maximum Stock</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$invention['max']." pcs&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr><td colspan=3><hr></td></tr>\n";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Attack</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$invention['attack']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Defense</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$invention['defense']." pts&nbsp;</td>";
    echo "                </tr>";
    echo "              </table>";
    echo "    </td>";
    echo "  </tr>";

    if ($build_option) {
      // Costs + ores  (initial / upkeep)
      echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";
      echo "  <tr bgcolor=black><td>";
      $cannot_build = smt_initial_ores ($cannot_build, $invention_id, $user_id, $planet_ores);
      echo "  </td><td>";
      $cannot_build = smt_upkeep_ores ($cannot_build, $invention_id, $user_id, $planet_ores);
      echo "  </td></tr>";
      echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";
    } else {
      echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";
      echo "  <tr bgcolor=black><td>";
      smt_initial_ores (0, $invention_id, $user_id, $planet_ores);
      echo "  </td><td>";
      smt_upkeep_ores (0, $invention_id, $user_id, $planet_ores);
      echo "  </td></tr>";
      echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";
    }

// Print rule and description
    if ($invention['rule'] != "") {
      echo "<tr bgcolor=black><td colspan=2><table border=0 cellspacing=5><tr><td>Effect: ".$invention['rule']."</td></tr></table></td></tr>";
    }

    if ($invention['description'] != "") {
      echo "<tr bgcolor=black><td colspan=2 ><table border=0 cellspacing=5><tr><td>".$invention['description']."</td></tr></table></td></tr>";
    }

// Print building possibility
    if ($build_option) {
      if ($cannot_build == false) {
         echo "<tr bgcolor=black><th colspan=2><a href=manufacture.php?cmd=".encrypt_get_vars ("manufacture").
                                                    "&iid=".encrypt_get_vars ($invention['id']).
                                                    "&aid=".encrypt_get_vars ($planet['id']).
                                                    ">BUILD IT</a></th></tr>";
      } else {
        echo "<tr bgcolor=black><th colspan=2>CANNOT BUILD</th></tr>";
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
function smt_initial_ores ($cannot_build, $invention_id, $user_id, $stock_ores) {
  assert (is_bool ((bool)$cannot_build));
  assert (is_numeric ($invention_id));
  assert (is_numeric ($user_id));
  assert (is_array ($stock_ores));

  $invention = item_get_item ($invention_id);
  $invention_ores = ore_csl_to_list ($invention['initial_ores']);
  $class = "t";

  if ($user_id == 0) {
    $build_option = 0;
  } else {
    $build_option = 1;
    $user = user_get_user ($user_id);
  }

  if ($build_option) {
    if ($invention['initial_costs'] > $user['credits']) {
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
  echo "    <td class=".$class." width=34%> &nbsp;".$invention['initial_costs']." cr&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    if ($build_option) {
      if ($invention_ores[$i] > $stock_ores[$i]) {
        $class = "f";
        $cannot_build = true;
      } else {
        $class = "t";
      }
    }
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$invention_ores[$i]." tons&nbsp;</td>\n";
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
function smt_upkeep_ores ($cannot_build, $invention_id, $user_id, $stock_ores) {
  assert (is_bool ((bool)$cannot_build));
  assert (is_numeric ($invention_id));
  assert (is_numeric ($user_id));
  assert (is_array ($stock_ores));

  $invention = item_get_item ($invention_id);
  $invention_upkeep_ores = ore_csl_to_list ($invention['upkeep_ores']);

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
  echo "    <td class=".$class." width=34%> &nbsp;".$invention['upkeep_costs']." cr&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$invention_upkeep_ores[$i]." tons&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";

  return $cannot_build;
}

?>