<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  // Extra headers for TD..
  $extra_headers =
        "<STYLE TYPE=\"text/css\" > " .
        "  TD.t { color : white}    " .
        "  TD.f { color : red}      " .
        "</STYLE>";
  print_header ($extra_headers);
  print_title ("Manufacturing");


  $cmd = input_check ("manufacture", "!frmid", "iid", "aid", 0,
                      "show", "aid", 0
                     );

  if ($cmd == "show") {
    // Show homeworld when nothing is set...
    if ($aid == "") {
      show_inventions (user_get_home_planet (user_ourself()));
    } else {
      show_inventions ($aid);
    }
  }

  if ($cmd == "manufacture") {
    $ok = "";
    $errors['PARAMS']  = "Incorrect parameters specified..\n";
    $errors['CREDITS'] = "You don't have enough cash to construct the item...\n";
    $errors['ORE']     = "You don't have enough ores to construct the item...\n";
    $errors['MAX']     = "You cannot build anymore items of this type on the planet...\n";
    $errors['DEPS']    = "You cannot build this item before you have build all dependencies...\n";
    $data['anomaly_id'] = $aid;
    $data['item_id'] = $iid;
    if (comm_send_to_server ("MANUFACTURE", $data, $ok, $errors) == 1) {
      $invention = item_get_item ($data['item_id']);
      echo "<br><br><br><br>";
      echo "<table align=center border=0>";
      echo "  <tr><th>New construction in progress</th></tr>";
      echo "  <tr><td align=center><img align=center src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/inventions/".$invention['image'].".jpg\" width=150 height=150></td></tr>";
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
function show_inventions ($anomaly_id) {
  assert (!empty($anomaly_id));

  // Get global information stuff
  $planet = anomaly_get_anomaly ($anomaly_id);
  $user   = user_get_user ($planet['user_id']);

  // And get the ores from the planet
  $result = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$anomaly_id);
  $ores = sql_fetchrow ($result);
  $stock_ores = $ores['stock_ores'];

  // Get all buildings that are currently build on the planet
  $surface = planet_get_surface ($anomaly_id);
  $current_buildings = csl ($surface['csl_building_id']);
  $current_cargo = $surface['csl_cargo_id'];

  print_subtitle ("Produceable on planet ".$planet['name']);

  // Get all items, compare wether or not we may build them...
  $result = sql_query ("SELECT * FROM s_inventions ORDER BY type, id");
  while ($item = sql_fetchrow ($result)) {
    // Default, we can build this
    $cannot_build = false;

// Stage 1: Item Count Check
    if ($item['max'] > 0) {
      $times_already_build=0;
      for ($i=0; $i!=count ($current_cargo); $i++) {
        if ($current_cargo[$i]==$item['id']) {
          $times_already_build++;
        }
      }
      // Cannot build cause we already have MAX items of this kind.. :(
      if ($times_already_build == $item['max']) {
        $cannot_build = true;
      }
    }


// Stage 2: Dependency Check
    // Get all dependencies
    $items_needed = csl($item['csl_depends']);

    // Do we need them? If not, skip dependency-check.
    if (!empty($item['csl_depends'])) {
      $deps_found = count ($items_needed);  // Count to zero...
      while (list ($key, $item_dep_id) = each ($items_needed)) {
        if ($item_dep_id == "") { $deps_found--; continue; }

        // Get all dependencies
        if (in_array ($item_dep_id, $current_buildings)) {
          $deps_found--;     // Found in current_items?
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
      invention_show_details ($item['id'], $planet['id'], $user['user_id'], $stock_ores);
    }
  }
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
function obsolete_show_invention_table ($invention, $planet, $user, $stock_ores) {
    assert (!empty ($invention));
    assert (!empty ($planet));
    assert (!empty ($user));
    assert (!empty ($stock_ores));
    global $_GALAXY;

    $cannot_build = false;

    $invention_ores = ore_csl_to_list ($invention['initial_ores']);
    $planet_ores = $stock_ores;

    echo "<table border=1 cellpadding=0 cellspacing=0 align=center width=50%>";

// invention name
    echo "  <tr><th colspan=2>".$invention['name']."</th></tr>";

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
    echo "                  <td class=".$class.">&nbsp;".item_get_type ($invention['type'])."&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr><td colspan=3><hr></td></tr>\n";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Maximum</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$invention['max']."&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr><td colspan=3><hr></td></tr>\n";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Attack</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$invention['attack']."&nbsp;</td>";
    echo "                </tr>";
    echo "                <tr>";
    echo "                  <td class=".$class.">&nbsp;<strong>Defense</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                  <td class=".$class.">&nbsp;".$invention['defense']."&nbsp;</td>";
    echo "                </tr>";
    echo "              </table>";
    echo "    </td>";
    echo "  </tr>";

// Costs + ores  (initial / upkeep)
    echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";
    echo "  <tr bgcolor=black><td>";
    $cannot_build = smt_initial_ores ($cannot_build, $invention, $user, $planet_ores);
    echo "  </td><td>";
    $cannot_build = smt_upkeep_ores ($cannot_build, $invention, $user, $planet_ores);
    echo "  </td></tr>";
    echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";


// Print rule and description
    if ($invention['rule'] != "") {
      echo "<tr><td colspan=2><table border=0 cellspacing=5><tr><td>Effect: ".$invention['rule']."</td></tr></table></td></tr>";
    }

    if ($invention['description'] != "") {
      echo "<tr><td colspan=2><table border=0 cellspacing=5><tr><td>".$invention['description']."</td></tr></table></td></tr>";
    }

// Print building possibility
    if ($cannot_build == false) {
        echo "<tr><th colspan=2><a href=manufacture.php?cmd=".encrypt_get_vars ("manufacture").
                                                    "&iid=".encrypt_get_vars ($invention['id']).
                                                    "&aid=".encrypt_get_vars ($planet['id']).
                                                    ">BUILD IT</a></th></tr>";
    } else {
      echo "<tr><th colspan=2>CANNOT BUILD</th></tr>";
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
function obsolete_smt_initial_ores ($cannot_build, $invention, $user, $stock_ores) {

  $invention_ores = ore_csl_to_list ($invention['initial_ores']);

  echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "  <tr><th colspan=3>Initial costs</th></tr>";

  if ($invention['initial_costs'] > $user['credits']) {
    $class="f";
    $cannot_build = true;
  } else {
    $class="t";
  }
  echo "  <tr>\n";
  echo "    <td class=".$class." width=33%> &nbsp;<strong>Credits</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=1%>  &nbsp;<strong>:</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=34%> &nbsp;".$invention['initial_costs']."&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    if ($invention_ores[$i] > $stock_ores[$i]) {
      $class="f";
      $cannot_build = true;
    } else {
      $class="t";
    }
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$invention_ores[$i]."&nbsp;</td>\n";
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
function obsolete_smt_upkeep_ores ($cannot_build, $invention, $user, $stock_ores) {
  $invention_upkeep_ores = ore_csl_to_list ($invention['upkeep_ores']);

  $class="t";
  echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "  <tr><th colspan=3>Upkeep costs</th></tr>";
  echo "  <tr>\n";
  echo "    <td class=".$class." width=33%> &nbsp;<strong>Credits</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=1%>  &nbsp;<strong>:</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=34%> &nbsp;".$invention['upkeep_costs']."&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$invention_upkeep_ores[$i]."&nbsp;</td>\n";
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
function obsolete_item_get_type ($type) {
  $s = "Unknown";

  if ($type == "P") $s = "Planet";
  if ($type == "W") $s = "Weapon";
  if ($type == "V") $s = "Vessel";

  return $s;
}
?>
