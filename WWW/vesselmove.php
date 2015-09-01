<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Move vessel", "Move your vessels around the place in this screen. Select a vessel you want to move. Vessels that are part of traderoutes or of convoys are not shown.");


  $cmd = input_check ("showuid", "uid", 0,
                      "showvid", "!vid", 0,
                      "goauto", "!vid", "!did", 0,
                      "gopreset", "!vid", "!pid", 0,
                      "gomanual", "!vid", "!ne_distance", "!ne_angle", 0);

  // Shows all ships from user $uid or ourself
  if ($cmd == "showuid") {
    if ($uid == "") $uid = user_ourself();
    vessel_select_vessel_by_user ($uid, $_SERVER['PHP_SELF'], "Select one of your vessels to move:", NO_SHOW_TRADEROUTES);
  }

  // Shows vessel $vid and let the user select a destination
  if ($cmd == "showvid") {
    select_destination ($vid);
  }

  // Sets vessel $vid off to destination id $did (either 0 means hold, S001 means sector ID 1, or 001 means planet id 1)
  if ($cmd == "goauto") {
    if ($did == "0") {
      $ok = "Vessel stopped at current location..\n";
    } else {
      $ok = "Vessel flight in process..\n";
    }
    $errors['PARAMS']    = "Incorrect parameters specified..\n";
    $errors['NOEXPLORE'] = "You must use a exploration vessel when flying to an unknown planet...\n";
    $data['vid'] = $vid;
    $data['did'] = $did;
    comm_send_to_server ("MOVE", $data, $ok, $errors);
  }

  // Sets the vessel $vid off to preset $pid
  if ($cmd == "gopreset") {
    $result = sql_query ("SELECT * FROM g_presets WHERE id=".$pid);
    $flight = sql_fetchrow ($result);

    $ok = "Vessel flight to ".$flight['name']." in process..\n";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['vid'] = $vid;
    $data['distance'] = $flight['distance'];
    $data['angle'] = $flight['angle'];
    comm_send_to_server ("MMOVE", $data, $ok, $errors);
  }

  // Sets the vessel $vid off to the destination $distance/$angle
  if ($cmd == "gomanual") {
    $distance = substr ($ne_distance, 0, 5);
    $angle = substr($ne_angle, 0, 6);

    $try_again = true;
    if (! preg_match ("/^\d+$/", $distance)) {
      print_line ("<li><font color=red>You should enter a distance in the format ######.</font>\n");
    } elseif (! preg_match ("/^\d{1,6}$/", $angle)) {
      print_line ("<li><font color=red>You should enter an angle in the format ######.</font>\n");
    } else if ($distance < $_GALAXY['galaxy_core']) {
      print_line ("<li><font color=red>You cannot fly that far into the galaxy core. Try a higher distance (minimum is ".$_GALAXY['galaxy_core'].").</font>\n");
    } elseif ($distance > $_GALAXY['galaxy_size']) {
      print_line ("<li><font color=red>You cannot fly outside of the galaxy. Try a lower distance (maximum is ".$_GALAXY['galaxy_size'].").</font>\n");
    } else {
      $ok = "Manual vessel flight engaged to ".$distance." / ".$angle."\n";
      $errors['PARAMS'] = "Incorrect parameters specified..\n";
      $data['vid'] = $vid;
      $data['distance'] = $distance;
      $data['angle'] = $angle;
      comm_send_to_server ("MMOVE", $data, $ok, $errors);
      $try_again = false;
    }
    if ($try_again == true) select_destination ($vid);
  }

  print_footer ();
  exit;

/*
  if (isset ($go)) {
    if (decrypt_get_vars ($_POST['did']) == "0") {
      $ok = "Vessel stopped at current location..\n";
    } else {
      $ok = "Vessel flight in process..\n";
    }
    $errors['PARAMS']    = "Incorrect parameters specified..\n";
    $errors['NOEXPLORE'] = "You must use a exploration vessel when flying to an unknown planet...\n";
    $data['uid'] = decrypt_get_vars($_POST['uid']);
    $data['vid'] = decrypt_get_vars($_POST['vid']);
    $data['did'] = decrypt_get_vars($_POST['did']);
    comm_send_to_server ("MOVE", $data, $ok, $errors);

  } elseif (isset ($fgo)) {
    $result = sql_query ("SELECT * FROM g_presets WHERE id=".decrypt_get_vars($_POST['fid']));
    $flight = sql_fetchrow ($result);

    $ok = "Vessel flight to ".$flight['name']." in process..\n";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['uid'] = decrypt_get_vars($_POST['uid']);
    $data['vid'] = decrypt_get_vars($_POST['vid']);
    $data['distance'] = $flight['distance'];
    $data['angle'] = $flight['angle'];
    comm_send_to_server ("MMOVE", $data, $ok, $errors);

  } elseif (isset ($mgo)) {
    $distance = substr ($_POST['distance'], 0, 5);
    $angle = substr($_POST['angle'], 0, 6);

    $try_again = 1;
    if (! preg_match ("/^\d+$/", $distance)) {
      echo "<li><font color=red>You should enter a distance in the format ######.</font>\n";
    } elseif (! preg_match ("/^\d{1,6}$/", $angle)) {
      echo "<li><font color=red>You should enter an angle in the format ######.</font>\n";
    } else if ($distance < $_GALAXY['galaxy_core']) {
      echo "<li><font color=red>You cannot fly that far into the galaxy core. Try a higher distance (minimum is ".$_GALAXY['galaxy_core'].").</font>\n";
    } elseif ($distance > $_GALAXY['galaxy_size']) {
      echo "<li><font color=red>You cannot fly outside of the galaxy. Try a lower distance (maximum is ".$_GALAXY['galaxy_size'].").</font>\n";
    } else {
      $ok = "Manual vessel flight engaged to ".$distance." / ".$angle."\n";
      $errors['PARAMS'] = "Incorrect parameters specified..\n";
      $data['uid'] = decrypt_get_vars($_POST['uid']);
      $data['vid'] = decrypt_get_vars($_POST['vid']);
      $data['distance'] = $distance;
      $data['angle'] = $angle;
      comm_send_to_server ("MMOVE", $data, $ok, $errors);
      $try_again = 0;
    }
    if ($try_again == 1) {
      select_destination ($_USER, decrypt_get_vars($vid));
    }

  } elseif (isset ($pid)) {
    select_destination ($_USER, decrypt_get_vars($pid));
  } elseif (isset ($vid)) {
    select_destination ($_USER, decrypt_get_vars($vid));
  } elseif (isset ($id)) {
    vessel_show_vessel ($_USER, decrypt_get_vars($id));
  } else {
    vessel_select_vessel_by_user ($_USER, $PHP_SELF, "Select vessel to move:", NO_SHOW_TRADEROUTES);
  }

  print_footer ();
  exit;
*/


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
function select_destination ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);

  $result = sql_query ("SELECT * FROM g_flags WHERE user_id=".$vessel['user_id']);
  $flags = sql_fetchrow ($result);

  print_subtitle ("Select destination for vessel ".$vessel['name']);

  print_remark ("Select_destination");
  echo "<table align=center border=0>\n";
  echo "  <tr><td valign=top>\n";
  vessel_select_automatic ($vessel_id);

  if ($flags['can_warp']) {
    echo "  </td></tr><tr><td valign=top>\n";
    vessel_select_preset ($vessel_id);
    echo "  </td></tr><tr><td valign=top>\n";
    vessel_select_manual ($vessel_id);
  }
  echo "  </td></tr>\n";
  echo "</table>\n";

  create_submenu ( array (
                           "Show Vessel" => "vessel.php?cmd=".encrypt_get_vars ("showvid")."&vid=".encrypt_get_vars ($vessel_id),
//                           "Upgrade Vessel" => "vesselupgrade.php?cmd=".encrypt_get_vars ("showvid")."&vid=".encrypt_get_vars($vessel_id),
                         )
                 );

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
// Select automatic destination
function vessel_select_automatic ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);
  $vesseltype = vessel_get_vesseltype ($vessel_id);

  // Get all sectors that we own
  $result = sql_query ("SELECT * FROM g_sectors WHERE user_id=".$vessel['user_id']);
  $sectors = csl_create_array ($result, "csl_sector_id");

//  if ($vessel['sector_id'] == 0) {
//    $src_sector['distance'] = $vessel['distance'];
//    $src_sector['angle'] = $vessel['angle'];
//  } else {
//    $result = sql_query ("SELECT * FROM s_sectors WHERE id=".$vessel['sector_id']);
//    $src_sector = sql_fetchrow ($result);
//  }

  form_start ();
  echo "<table align=center>";
  echo "  <tr><th>Select destination</th></tr>";
  echo "  <tr><td>";
  echo "    <select name=did>";
  echo "      <option value=".encrypt_get_vars (0).">Hold at current position</option>";

  // Only show unknown planets when the vesseltype is exploration
  $ut = 'N';
  if ($vesseltype['type'] == VESSEL_TYPE_EXPLORE) $ut = 'Y';

  // First, show the planets of the current sector, if applicable
  if ($vessel['sector_id'] != 0) {
    show_sector ($vessel_id, $ut);
  }


  // Create a hash with all sectors
  $i = 0;
  foreach ($sectors as $sector_id) {
    if ($sector_id != $vessel['sector_id']) {
      if ($vessel['warp'] != 0) {
        $sector = sector_get_sector ($sector_id);

        $ticks = calc_sector_ticks ($sector['distance'], $sector['angle'], $vessel['distance'], $vessel['angle'], $vessel['warp']);
        if ($ticks == 0) $ticks = 1;

        $options[$i]['ticks'] = $ticks;
        $options[$i]['str'] = "<option value=".encrypt_get_vars ("S".$sector['id']).">".$sector['name']." ($ticks ticks)</option>";
      }
    }
    $i++;
  }

  // Sort the hash on ticks, and print it accordingly
  uasort ($options, "vessel_move_cmp");
  foreach ($options as $line) {
    echo $line['str'];
  }

  echo "    </select>";
  echo "  </td></tr>";
  echo "  <tr align=center><td><input type=submit name=submit value='Fly to destination'></td></tr>";
  echo "</table>";
  echo "<input type=hidden name=vid value=".encrypt_get_vars ($vessel_id).">";
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("goauto").">";
  form_end ();
  echo "<br>";
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
function vessel_select_manual ($vessel_id) {
  assert (is_numeric ($vessel_id));

  form_start ();
  echo "<table align=center>";
  echo "  <tr><th colspan=4>Select manual destination</th></tr>";
  echo "  <tr><td>D</td><td><input type=text name=ne_distance size=6></td>";
  echo "      <td>A</td><td><input type=text name=ne_angle size=7></td></tr>";
  echo "  <tr align=center><td colspan=4><input type=submit name=submit value='Fly to destination'></td></tr>";
  echo "</table>";
  echo "<input type=hidden name=vid value=".encrypt_get_vars ($vessel_id).">";
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("gomanual").">";
  form_end ();
  echo "<br>";
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
function vessel_select_preset ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);

  form_start ();
  echo "<table align=center>";
  echo "  <tr><th>Select preset destination</th></tr>";
  echo "  <tr><td>";
  echo "    <select name=pid>";
  $result = sql_query ("SELECT * FROM g_presets WHERE user_id=".$vessel['user_id']);
  while ($preset = sql_fetchrow ($result)) {
    $ticks = calc_sector_ticks ($preset['distance'], $preset['angle'], $vessel['distance'], $vessel['angle'], $vessel['warp']);
    echo "      <option value=".encrypt_get_vars ($preset['id']).">".$preset['name']." (".$ticks." ticks)</option>";
  }
  echo "    </select>";
  echo "  </td></tr>";
  echo "  <tr align=center><td><input type=submit name=submit value='Fly to destination'></td></tr>";
  echo "</table>";
  echo "<input type=hidden name=vid value=".encrypt_get_vars ($vessel_id).">";
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("gopreset").">";
  form_end ();
  echo "<br>";
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
function show_sector ($vessel_id, $unknowns_too) {
  assert (is_numeric ($vessel_id));
  assert (is_string ($unknowns_too));


  // Get sector information
  $vessel = vessel_get_vessel ($vessel_id);
  $user   = user_get_user ($vessel['user_id']);
  $sector = sector_get_sector ($vessel['sector_id']);
  $result = sql_query ("SELECT * FROM g_anomalies WHERE user_id=".$vessel['user_id']);
  $anomalies = csl_create_array ($result, "csl_discovered_id");
  if ($unknowns_too == "Y") {
    $undiscovered_anomalies = csl_create_array ($result, "csl_undiscovered_id");
    $anomalies = csl_merge_fields ($anomalies, $undiscovered_anomalies);
  } else {
    $undiscovered_anomalies = "";
  }


  // Get planet information for all planets in the sector
  $result = sql_query ("SELECT * FROM s_anomalies WHERE sector_id=".$sector['id']." ORDER BY distance");
  while ($anomaly = sql_fetchrow ($result)) {

    // If we can't view the planet, then don't show it...
    if (!in_array ($anomaly['id'], $anomalies)) continue;

    $ticks = calc_planet_ticks ($anomaly['distance'], $sector['angle'], $vessel['sun_distance'], $vessel['angle'], $vessel['impulse'], $vessel['warp']);

    // Check if ticks = 0 and the id of the planet is not the same as the
    // id of the planet we're curently orbitting...
    // If that's the case, the planet is really nearby, and we give it at
    // least 1 tick...
    if ($ticks == 0 && $anomaly['id'] != $vessel['planet_id']) {
      $ticks = 1;
    }

    // Don't show planet if it's 0 tick away (which means: it's our orbit)
    if ($ticks == 0) { continue; }

    // Thread undiscovered planets different...
    if (in_array ($planet['id'], $undiscovered_anomalies)) {
      echo "<option value=".encrypt_get_vars ($anomaly['id']).">".$sector['name']." / Unknown ($ticks ticks)</option>";
    } else {
      echo "<option value=".encrypt_get_vars ($anomaly['id']).">".$sector['name']." / ".$anomaly['name']." ($ticks ticks)</option>";
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
// Compares tick from hash $a with hash $b
// This is needed to order the distance of the sectors
function vessel_move_cmp ($a, $b) {
  if ($a['ticks'] < $b['ticks']) return -1;
  if ($a['ticks'] > $b['ticks']) return 1;
  return 0;
}

?>
