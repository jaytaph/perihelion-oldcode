<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification();

  print_header ();
  print_title ("Vessel upgrade");

  // can we build ships already?
  $user = user_get_user ($_USER['id']);
  if ($user['impulse'] == 0) {
    print_line ("You cannot build ships yet");
    print_footer ();
    exit;
  }

  // Get a ship if we didn't select one
  if (!isset ($_GET['vid'])) {
    choose_vessel ($_USER['id']);
    print_footer ();
    exit;
  }
  $vid = decrypt_get_vars ($_GET['vid']);

  // Go to the first stage if no stage is selected
  if (isset ($_GET['stage'])) {
    $stage = decrypt_get_vars ($_GET['stage']);
  } else {
    $stage = 1;
  }

  // Do upgrade (depending on stage)
  if ($stage == 1) {
     upgrade_speed ($_USER, $vid);
  }

  print_footer ();
  exit;



/******************************************************************************
 *
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
function upgrade_speed ($_USER, $vessel_id) {
  // Get global information
  $user = user_get_user ($_USER['id']);
  $result  = sql_query ("SELECT * FROM g_flags WHERE user_id=".$_USER['id']);
  $flags   = sql_fetchrow ($result);
  $vessel = vessel_get_vessel ($vessel_id);


  // Show Ship and User Capabilities
  echo "<table align=center border=1>";
  echo "<tr><td>";
  echo "<table width=100% border=0 cellpadding=0 cellspacing=0>";
  echo "<tr><th colspan=2>Current Ship Capabilities</th></tr>";
  echo "<tr><td>Impulse Speed: </td><td>".$vessel['impulse']."%</td></tr>";
  echo "<tr><td>Warp Speed:    </td><td>".number_format($vessel['warp']/10, 1)."</td></tr>";
  echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
  echo "</table>";
  echo "</td><td>";
  echo "<table width=100% border=0 cellpadding=0 cellspacing=0>";
  echo "<tr><th colspan=2>User Statistics</th></tr>";
  echo "<tr><td>Credits:</td><td>".$user['credits']."</td></tr>";
  echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
  echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
  echo "</table>";
  echo "</td></tr>";
  echo "</table>";
  echo "<br>";
  echo "<br>";

  if ($vessel['impulse'] == $user['impulse'] and
    $vessel['warp'] == $user['warp']) {
    echo "  <table align=center>";
    echo "    <tr><td>Ship Name:          </td><td>".$vessel['name']."</td></tr>";
    echo "    <tr><td>&nbsp;</td><td>No upgrade Possible</td></tr>";
    echo "  </table>";
  }

  // Stage 1: Create Ship and ship name
  if (!isset ($stage) || ($stage == 1)) {
    form_start ();
    echo "<input type=hidden name=vid value=$vid>";

    echo "  <table align=center>";
    echo "    <tr><td>Ship Name:          </td><td>".$vessel['name']."</td></tr>";
    echo "    <tr><td colspan=2></td></tr>";
    echo "    <tr><td>Impulse speed: </td><td>";
    if ($flags['can_warp'] == 1) {
      echo "<input type=hidden name=impulse value=100>100 % (".($config['s_impulse_costs']*100)." Credits)";
    } else {
      echo " <select name='impulse'>";
      for ($i = $vessel['impulse']+1; $i!=$user['impulse']+1; $i++) {
        echo "<option value=".$i.">".$i." % (".(($i-$vessel['impulse']) * $config['s_impulse_costs'])." Credits)</option>";
      }
      echo " </select>";
    }
    echo "    </td></tr>";
    echo "    <tr><td>Warp Speed: </td><td>";
    if ($flags['can_warp'] == 1) {
      echo " <select name=warp>";
      for ($i = $vessel['warp']+1; $i!=$user['warp']+1; $i++) {
        echo "<option value=".$i."> Warp ".number_format($i/10, 1)." (".(($i-$vessel['warp']) * $config['s_warp_costs'])." Credits)</option>";
      }
      echo " </select>";
    } else {
      echo "<input type=hidden name=warp value=0>";
      echo "None";
    }
    echo "    </td></tr>";
    echo "    <tr><td>&nbsp;</td><td><input type=submit name=submit value=\"Upgrade Ship\"></td></tr>";
    echo "  </table>";
    form_end ();
  }

  //  Stage 2: Add or Delete weaponary
  if ($stage == 2 and ($vessel['type']==VESSEL_TYPE_TRADE or $vessel['type']==VESSEL_TYPE_EXPLORE)) $stage = 3;

  if ($stage == 2) {
    // Get all weapons we can view
    $visible_weapons = array ();
    $result = sql_query ("SELECT * FROM g_weapons WHERE user_id=".$_USER['id']);
    $visible_weapons = csl_create_array ($result, "csl_weapon_id");

    // And dump them into the table
    echo "<table border=1 align=center>";
    echo "<tr><th colspan=8>Weaponary</th></tr>";
    echo "<tr>";
    echo "<th>Name</th>";
    echo "<th>Costs</th>";
    echo "<th>Power</th>";
    echo "<th>Attack</th>";
    echo "<th>Defense</th>";
    echo "<th>Qty</th>";
    echo "<th colspan=2>Action</th>";
    echo "</tr>";

    reset ($visible_weapons);
    while (list($key, $weapon_id) = each ($visible_weapons)) {
      $result = sql_query ("SELECT * FROM s_weapons WHERE id=".$weapon_id);
      $weapon = sql_fetchrow ($result);
      echo "<tr>";
      echo "<td>".$weapon['name']."</td>";
      echo "<td>".$weapon['costs']."</td>";
      echo "<td>".$weapon['power']."</td>";
      echo "<td>".$weapon['attack']."</td>";
      echo "<td>".$weapon['defense']."</td>";
      echo "<td><input type=text size=3 maxlength=3 value=0 name=T1></td>";
      echo "<td><b>Add</b></td>";
      echo "<td><b>Delete</b></td>";
      echo "</tr>";
    }
    echo "</table>";
    echo "<br><br>";
  }

  if ($stage == 3) {
    $ok = "Vessel upgrade in process..\n";
    $errors['PARAMS']  = "Incorrect parameters specified...\n";
    $errors['SPEED']   = "Incorrect speed settings...\n";
    $errors['CREDITS'] = "Not enough credits...\n";
    $data['impulse']  = $_POST['impulse'];
    $data['warp']     = $_POST['warp'];
    $data['vid']      = decrypt_get_vars($_POST['vid']);
    comm_send_to_server ("VESSELUPGRADE", $data, $ok, $errors);
  }
}



/******************************************************************************
 *
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
function choose_vessel ($user_id) {
  global $_GALAXY;

  echo "<table align=center border=1>";
  echo "<tr><th>Name</th><th>Impulse</th><th>Warp</th><th>Status</th></tr>";

  $result = sql_query ("SELECT g.* FROM g_vessels AS g, s_vessels AS s WHERE g.user_id=".$user_id." AND g.created=1 AND g.vessel_id = s.id ORDER BY s.type, g.id");
  while ($vessel  = sql_fetchrow ($result)) {
    // We can only upgrade if we are near a vessel station.
    if ($vessel['status'] != "ORBIT") {
      $nocando = 1;
    } else {
      // Check if we have a spacedock or vessel construction station
      $surface = planet_get_surface ($vessel['planet_id']);
      $buildings = csl ($surface['csl_building_id']);
      $vesseltype = vessel_get_vesseltype ($vessel['id']);

      $nocando = 0;
      if ($vesseltype['type'] == VESSEL_TYPE_BATTLE and !in_array (BUILDING_VESSEL_STATION, $buildings)) $nocando = 1;
      if ($vesseltype['type'] != VESSEL_TYPE_BATTLE and !in_array (BUILDING_SPACEDOCK, $buildings)) $nocando = 1;
    }

    if ($nocando == 0) {
      $status = "Nearby a vessel or docking station";
      $upgrade = 1;
    } else {
      $status = "Not in the vicinity of a vessel station";
      $upgrade = 0;
    }



    echo "<tr>";
    if ($upgrade == 1) {
      echo "<td>&nbsp;<img alt=Active src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/active.gif>&nbsp;<a href=vesselupgrade.php?vid=".encrypt_get_vars ($vessel['id']).">".$vessel['name']."</a>&nbsp;</td>";
    } else {
      echo "<td>&nbsp;<img alt=Inactive src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/inactive.gif>&nbsp;".$vessel['name']."</a>&nbsp;</td>";
    }
    echo "<td>&nbsp;Impulse: ".$vessel['impulse']."% &nbsp;</td>";
    echo "<td>&nbsp;Warp: "; printf ("%.1f", $vessel['warp']/10); echo "&nbsp;</td>";
    echo "<td>&nbsp;$status&nbsp;</td>";
    echo "</tr>";
  }

  echo "</table>";
}

?>


