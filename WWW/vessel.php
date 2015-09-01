<?php
  // Include Files
  include "includes.inc.php";


  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Vessel view", "You can select and view all your created vessels.");


  $cmd = input_check ("showuid", "uid", 0,
                      "showvid", "!vid", 0,
                      "showaid", "!aid", 0,
                      "load_p2v",  "!vid", "!aid", "!iid", 0,    // Load cargo from planet to vessel
                      "load_v2p",  "!vid", "!aid", "!iid", 0,    // Load cargo from vessel to planet
                      "cargo",     "!vid", "!aid", "!sl", "!pc", "!sp", 0
                     );

  if ($cmd == "showuid") {
    if ($uid == "") $uid = user_ourself();
    vessel_select_vessel_by_user ($uid, $_SERVER['PHP_SELF'], "Select one of your vessels to view:", SHOW_TRADEROUTES);
  }
  if ($cmd == "showvid") {
    vessel_show_details ($vid);
  }
  if ($cmd == "showaid") {
    vessel_show_vessels_orbiting_planet ($aid);
  }

  if ($cmd == "load_p2v") {
    $ok = "";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['vid'] = $vid;
    $data['aid'] = $aid;
    $data['iid'] = $iid;
    comm_send_to_server ("CARGO1", $data, $ok, $errors);
    vessel_show_details ($vid);
  }
  if ($cmd == "load_v2p") {
    $ok = "";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['vid'] = $vid;
    $data['aid'] = $aid;
    $data['iid'] = $iid;
    comm_send_to_server ("CARGO2", $data, $ok, $errors);
    vessel_show_details ($vid);
  }

  if ($cmd == "cargo") {
    $ok = "";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['vid'] = $vid;
    $data['pid'] = $aid;
    $data['sl']  = $sl;
    $data['pc']  = $pc;
    $data['sp']  = $sp;
    comm_send_to_server ("CARGO", $data, $ok, $errors);
    vessel_show_details ($vid);
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
function vessel_show_details ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);
  $vesseltype = vessel_get_vesseltype ($vessel_id);

  vessel_show_vessel_details ($vessel_id);
  scan_scan_area ($vessel_id, 1000);

  if ($vesseltype['type'] == VESSEL_TYPE_TRADE) vessel_show_trade_details ($vessel_id);
  if ($vesseltype['type'] == VESSEL_TYPE_EXPLORE) vessel_show_explore_details ($vessel_id);
  if ($vesseltype['type'] == VESSEL_TYPE_BATTLE) vessel_show_battle_details ($vessel_id);

  vessel_show_cargo ($vessel_id);


  create_submenu ( array (
                    "Move Vessel"    => "vesselmove.php?cmd=".encrypt_get_vars("showvid")."&vid=".encrypt_get_vars($vessel_id),
//                    "Upgrade Vessel" => "vesselupgrade.php?vid=".encrypt_get_vars($vessel_id),
                   )
                 );
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
function vessel_show_battle_details ($vessel_id) {
  assert (is_numeric ($vessel_id));

  global $_GALAXY;


  $vessel = vessel_get_vessel ($vessel_id);

  echo "<table border=0 width=75% align=center>\n";
  echo "  <tr class=wb><th colspan=5>Weaponry</th></tr>\n";

  echo "  <tr class=bl><th>Qty</th><th>Weapon</th><th>Attack</th><th>Defense</th><th>&nbsp;</th></tr>\n";

  // Get all items carrying in the cargobay, this also includes weaponry...
  $result   = sql_query ("SELECT * FROM i_vessels WHERE vessel_id=".$vessel_id);
  $current_items = csl_create_array ($result, "csl_weapon_id");
  $current_items = array_count_values ($current_items);

  $total_attack = 0;
  $total_defense = 0;

  reset ($current_items);
  while (list ($item_id, $quantity) = each ($current_items)) {
    $invention = item_get_item ($item_id);

    if (invention_is_active_on_vessel ($invention['id'], $vessel_id)) {
      $activeimg = "alt=Active src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/active.gif";
    } else {
      $activeimg = "alt=Inactive src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/inactive.gif";
    }

    echo "  <tr class=bl>\n";
    echo "    <td>&nbsp;".$quantity."&nbsp;</td>\n";
    echo "    <td>&nbsp;<img ".$activeimg.">&nbsp;".$invention['name']."&nbsp;</td>\n";
    echo "    <td>&nbsp;".$invention['attack'] * $quantity."&nbsp;</td>\n";
    echo "    <td>&nbsp;".$invention['defense'] * $quantity."&nbsp;</td>\n";
    if ($vessel['planet_id'] == 0) {
      echo "    <td nowrap>&nbsp;&nbsp;</td>\n";
    } else {
      echo "    <td nowrap>&nbsp;<a href=vessel.php?cmd=".encrypt_get_vars("load_v2p")."&vid=".encrypt_get_vars($vessel['id'])."&aid=".encrypt_get_vars($vessel['planet_id'])."&iid=".encrypt_get_vars($invention['id']).">Move to planet</a>&nbsp;</td>\n";
    }
    echo "  </tr>\n";

    $total_attack += $invention['attack'] * $quantity;
    $total_defense += $invention['defense'] * $quantity;
  }

  echo "  <tr class=bl><td colspan=2><b>&nbsp;Total Extra Attack and Defense Points: </b></td><td><b>&nbsp;".round($total_attack/100)."&nbsp;</b></td><td><b>&nbsp;".round($total_defense/100)."&nbsp;</b></td><td>&nbsp;</td></tr>\n";
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
function vessel_show_explore_details ($vessel_id) {
  assert (is_numeric ($vessel_id));
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
function vessel_show_trade_details ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);

  // Default action is not to show anything from a planet,
  // since we can be in flight
  for ($i=0; $i!=ore_get_ore_count(); $i++) {
    $planet_stock_ores[$i] = '?';
  }

  $status = vessel_get_current_status ($vessel_id);


  if (vessel_is_in_orbit ($vessel_id)) {
    $result = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$vessel['planet_id']);
    $tmp    = sql_fetchrow ($result);
    $planet_stock_ores = ore_csl_to_list ($tmp['stock_ores']);
  }
  $result = sql_query ("SELECT * FROM i_vessels WHERE vessel_id=".$vessel['id']);
  $tmp    = sql_fetchrow ($result);
  $vessel_stock_ores = ore_csl_to_list ($tmp['ores']);

  echo "<table border=0 width=75% align=center>\n";
  echo "  <tr class=wb><th colspan=2>Trade vessel: ".$vessel['name']."</th></tr>\n";
  echo "  <tr><td>\n";
    echo "    <table border=0 width=100% cellpading=0 cellspacing=0>\n";
    echo "    <tr class=bl><th colspan=2>Ores on planet:</th></tr>\n";
    for ($i=0; $i!=ore_get_ore_count(); $i++) {
      echo "    <tr class=bl><td>".ore_get_ore_name($i)."</td> <td>".$planet_stock_ores[$i]."</td></tr>\n";
    }
    echo "    </table>\n";
  echo "  </td><td>\n";
    echo "    <table border=0 width=100% cellpading=0 cellspacing=0>\n";
    echo "      <tr class=bl><th colspan=2>Ores on trader:</th></tr>\n";
    for ($i=0; $i!=ore_get_ore_count(); $i++) {
      echo "      <tr class=bl><td>".ore_get_ore_name($i)."</td> <td>".$vessel_stock_ores[$i]."</td></tr>\n";
    }
    echo "    </table>\n";
  echo "  </td></tr>\n";

  // Don't load/unload when we are in a trade route
  if (vessel_in_traderoute ($vessel_id)) {
    echo "  <tr class=wb><th colspan=2>Cannot load/unload because vessel is part of a trade route.</th></tr>";
  }

  // TODO: Don't load/unload when it's not our planet :)
  if (vessel_is_in_orbit ($vessel_id)) {
    $planet = anomaly_get_anomaly ($vessel['planet_id']);

    echo "  <tr class=bl align=center><td colspan=2>";
    form_start ();
    echo "    <input type=hidden name=vid value=".encrypt_get_vars($vessel['id']).">\n";
    echo "    <input type=hidden name=pid value=".encrypt_get_vars($vessel['planet_id']).">\n";
    echo "    <select name=sl>\n";
    echo "      <option value=dump>Unload from vessel</option>\n";
    echo "      <option value=store>Load into vessel</option>\n";
    echo "    </select>&nbsp;\n";
    echo "    <select name=pc>\n";
    echo "      <option value=5>5%</option>\n";
    echo "      <option value=10>10%</option>\n";
    echo "      <option value=25>25%</option>\n";
    echo "      <option value=50>50%</option>\n";
    echo "      <option value=75>75%</option>\n";
    echo "      <option value=100>100%</option>\n";
    echo "    </select>&nbsp;\n";
    echo "    <select name=sp>\n";
    for ($i=0; $i!=ore_get_ore_count(); $i++) {
      echo "      <option value=$i>".ore_get_ore_name($i)."</option>\n";
    }
    echo "      <option value=".ORE_ALL.">All ores</option>\n";
    echo "    </select>&nbsp;\n";
    echo "    <input type=submit name=submit value=go>\n";
    form_end ();
    echo "  </td></tr>\n";
  }

  echo "</table>";
  echo "<br><br>\n\n\n";
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
function vessel_show_vessels_orbiting_planet ($planet_id) {
  assert (is_numeric ($planet_id));

  $planet = anomaly_get_anomaly ($planet_id);

  if (! anomaly_is_planet ($planet_id)) {
    print_subtitle ("This is not a planet!");
    return;
  }

  print_subtitle ("All vessels orbiting planet ".$planet['name']);

  $firstrow = 1;
  $userid = 0;

  $result = sql_query ("SELECT g.* FROM g_vessels AS g, s_vessels AS s WHERE g.status='ORBIT' AND g.planet_id=$planet_id AND g.created=1 AND s.id = g.vessel_id ORDER BY g.user_id, s.type");
  while ($vessel = sql_fetchrow ($result)) {
    if ($vessel['user_id'] != $userid) {
      $userid = $vessel['user_id'];

      if ($firstrow == 0) {
        echo "</table>\n";
        echo "<br><br>\n";
      } else {
        $firstrow = 0;
      }

      echo "<table align=center border=0>\n";
      echo "  <tr class=wb>";
      echo "<th>Name</th>";
      echo "<th>Type</th>";
      echo "<th>Status</th>";
      echo "<th>Coords</th>";
      echo "<th>Race</th>";
      echo "</tr>\n";
    }

    vessel_show_table_row ($vessel['id'], $_SERVER['PHP_SELF'], "SHOW_SPECIES");
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
function vessel_show_vessel_details ($vessel_id) {
  assert (is_numeric ($vessel_id));

  global $_GALAXY;


  $vessel     =  vessel_get_vessel ($vessel_id);
  $vesseltype = vessel_get_vesseltype ($vessel_id);
  $status     = vessel_get_current_status ($vessel_id, "VESSEL_GETSTATUS_SHOW_HYPERLINKS");


  print_image ($_CONFIG['URL'].$_CONFIG['URL'].$_GALAXY['image_dir']."/vessels/".$vesseltype['image'].".jpg");

    echo "<table align=center border=0>";
      echo "<tr class=wb><th colspan=2>Global Information</th></tr>";
      echo "<tr class=bl><td>&nbsp;Name&nbsp;</td>";
      echo "<td>".$vessel['name']."</td></tr>";

      echo "<tr class=bl><td>&nbsp;Impulse / Max&nbsp;</td>";
      echo "<td>".$vessel['impulse']." / ".$vesseltype['max_impulse']."</td></tr>";

      echo "<tr class=bl><td>&nbsp;Warp / Max&nbsp;</td>";
      echo "<td>". number_format($vessel['warp']/10, 1)." / ".number_format($vesseltype['max_warp']/10, 1)."</td></tr>";

      echo "<tr class=bl><td>&nbsp;Status&nbsp;</td>";
      echo "<td>".$status."</td></tr>";

      echo "<tr class=bl><td>&nbsp;Location&nbsp;</td>";
      echo "<td>".$vessel['distance']."/".$vessel['angle']."</td></tr>";

      list ($extra_attack, $extra_defense) = vessel_calc_extra_attack_and_defense_points ($vessel_id);
      echo "<tr class=bl><td>&nbsp;A / D / S&nbsp;</td>";
      echo "<td>".$vessel['cur_attack']."<sup>(+".$extra_attack.")</sup> / ".$vessel['cur_defense']."<sup>(+".$extra_defense.")</sup> / ".$vessel['cur_strength']." (".$vessel['max_strength'].")</td></tr>";

    echo "</table>";

//  echo "</td></tr>";
//  echo "</table>";
  echo "<br><br>";
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
function vessel_show_cargo ($vessel_id) {
  assert (is_numeric ($vessel_id));

  global $_GALAXY;

  $vessel = vessel_get_vessel ($vessel_id);

  // Get all buildings that are currently build on the planet
  $result   = sql_query ("SELECT * FROM i_vessels WHERE vessel_id=".$vessel_id);
  $vessel_cargo = csl_create_array ($result, "csl_cargo_id");
  $vessel_cargo = array_count_values ($vessel_cargo);

  // Find out if we just travelled through a wormhole, or if we are nearby a wormhole.
  $located_in_wormhole = false;
  $result = sql_query ("SELECT * FROM w_wormhole");
  while ($wormhole = sql_fetchrow ($result)) {
    if ($vessel['distance'] == $wormhole['distance'] and $vessel['angle'] == $wormhole['angle']) {
      $located_in_wormhole = true;
    }
  }


  // Do not show the array when it's empty...
  if (count($vessel_cargo)==0) {
//    echo "<table align=center border=0 width=75%>";
//    echo "<tr class=wb><th colspan=2>No items on board</th></tr>";
//    echo "</table>";
//    echo "<br><br>";
  } else {
    echo "<table align=center border=0 width=75%>";
    echo "<tr class=wb><th colspan=3>Vessel Items</th></tr>";
    echo "<tr class=bl>";
    echo "<th>Quantity</th>";
    echo "<th width=100%>Name</th>";
    echo "<th>&nbsp;</th>";
    echo "</tr>";

    reset ($vessel_cargo);
    while (list ($cargo_id, $q) = each ($vessel_cargo)) {
      $invention = item_get_item ($cargo_id);

      if (invention_is_active_on_vessel ($invention, $vessel)) {
        $activeimg = "alt=Active src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/active.gif";
      } else {
        $activeimg = "alt=Inactive src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/inactive.gif";
      }


      echo "<tr class=bl>";
      echo "  <td>&nbsp;".$q."&nbsp;</td>";
      echo "  <td>&nbsp;<img ".$activeimg.">&nbsp;".$invention['name']."&nbsp;</td>";
      if ($vessel['planet_id'] == 0) {
        echo "  <td nowrap>&nbsp;&nbsp;</td>";
      } else {
        echo "  <td nowrap>&nbsp;<a href=vessel.php?cmd=".encrypt_get_vars("load_v2p")."&vid=".encrypt_get_vars($vessel['id'])."&aid=".encrypt_get_vars($vessel['planet_id'])."&iid=".encrypt_get_vars($invention['id']).">Move to planet</a>&nbsp;</td>";
      }
      echo "</tr>";
    }

    echo "</table>";
    echo "<br><br>";
  }


  // Only show planet cargo when there we are orbitting a planet.
  if ($vessel['planet_id'] == 0) return;

  // Get all buildings that are currently build on the planet
  $surface = planet_get_surface ($vessel['planet_id']);
  $planet_cargo = csl ($surface['csl_cargo_id']);
  $planet_cargo = array_count_values ($planet_cargo);

  // Do not show the array when it's empty...
  if (count($planet_cargo)==0) {
//    echo "<table align=center border=0 width=75%>\n";
//    echo "  <tr class=wb><th colspan=3>No items on planet</th></tr>\n";
//    echo "</table>\n";
//    echo "<br><br>\n";
  } else {

    form_start ();

    echo "<table align=center border=0 width=75%>\n";
    echo "  <tr class=wb><th colspan=3>Planet Items</th></tr>\n";
    echo "  <tr class=bl>";
    echo "<th>Qty</th>";
    echo "<th width=100%>Name</th>";
    echo "<th>&nbsp;</th>";
    echo "</tr>\n";

    reset ($planet_cargo);
    while (list ($cargo_id, $q) = each ($planet_cargo)) {
      $invention = item_get_item ($cargo_id);

      if (invention_is_active_on_planet ($invention)) {
        $activeimg = "alt=Active src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/active.gif";
      } else {
        $activeimg = "alt=Inactive src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/inactive.gif";
      }

      echo "  <tr class=bl>\n";
      echo "    <td>&nbsp;".$q."&nbsp;</td>\n";
      echo "    <td>&nbsp;<img ".$activeimg.">&nbsp;".$invention['name']."&nbsp;</td>\n";
      echo "    <td nowrap>&nbsp;<a href=vessel.php?cmd=".encrypt_get_vars("load_p2v")."&vid=".encrypt_get_vars($vessel['id'])."&aid=".encrypt_get_vars($vessel['planet_id'])."&iid=".encrypt_get_vars($invention['id']).">Move to vessel</a>&nbsp;</td>\n";
      echo "  </tr>\n";
    }

    echo "</table>\n";
    form_end ();
    echo "<br><br>\n";
  }
}

?>