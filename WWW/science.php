<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Science office",
               "In the science office you can decide on what area your scientist will focus on. It will also give you the opportunity to increase or decrease your tax ratio. The higher this ratio the more credits you will receive each tick. The downside however is that people will get less happy and your scientists will invent at a slower level.");

  $cmd = input_check ("show", "uid", 0,     // Shows the table, we don't really use it, since we always show it
                      "setrate", "!frmid", "uid", "ne_building_rate", "ne_vessel_rate", "ne_invention_rate", "ne_explore_rate", "ne_science_ratio", 0);


  if ($cmd == "setrate") {
    if ($uid == "") $uid = user_ourself();
    $user = user_get_user ($uid);

    if ($ne_science_ratio == "")  $ne_science_ratio  = $user['science_ratio'];
    if ($ne_building_rate == "")  $ne_building_rate  = $user['science_building'];
    if ($ne_vessel_rate == "")    $ne_vessel_rate    = $user['science_vessel'];
    if ($ne_invention_rate == "") $ne_invention_rate = $user['science_invention'];
    if ($ne_explore_rate == "")   $ne_explore_rate   = $user['science_explore'];

    if (($ne_building_rate + $ne_vessel_rate + $ne_invention_rate + $ne_explore_rate) != 100) {
      print_line ("<font color=red><center><strong>Warning:</strong><br>Your percentage settings must be equal to 100%!<br>New rating is not set!</center></font>");
    } else {
      $ok = "";
      $errors['PARAMS']  = "Incorrect parameters specified..\n";
      $errors['100']     = "The rating must add to 100%\n";
      $data['id']        = user_ourself();
      $data['ratio']     = $ne_science_ratio;
      $data['invention'] = $ne_invention_rate;
      $data['building']  = $ne_building_rate;
      $data['vessel']    = $ne_vessel_rate;
      $data['explore']   = $ne_explore_rate;
      comm_send_to_server ("SCIENCE", $data, $ok, $errors);
    }
  }

  // Show user info
  if ($uid == "") $uid = user_ourself();
  $user    = user_get_user ($uid, NOCACHE);  // Reload, since the $user data may have changed
  show_invention_levels ($uid);
  show_science_table ($uid, $user['impulse'], $user['science_ratio'], $user['science_invention'], $user['science_building'], $user['science_vessel'], $user['science_explore']);

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
function show_science_table ($uid, $impulse, $science_ratio, $science_invention, $science_building, $science_vessel, $science_explore) {
    assert (isset ($uid));
    assert (isset ($science_ratio));
    assert (isset ($science_invention));
    assert (isset ($science_building));
    assert (isset ($science_vessel));
    assert (isset ($science_explore));

    echo "<hr>\n";
    show_science_tax_ratio_table ($science_ratio);

    echo "<hr>";
    show_improvement_percentages ($uid, $science_invention, $science_building, $science_vessel, $science_explore);
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
function show_improvement_percentages ($uid, $science_invention, $science_building, $science_vessel, $science_explore) {
  $user    = user_get_user ($uid);

  form_start ();
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("setrate").">\n";

  echo "<table align=center width=50% border=0>\n";

  show_improvement ("Global Improvement Rate",   "invention_rate", $science_invention);
  show_improvement ("Building Improvement Rate", "building_rate",  $science_building);
  if ($user['impulse'] > 0) {
    show_improvement ("Vessel Improvement Rate",   "vessel_rate",    $science_vessel);
    show_improvement ("Space Exploration Rate",    "explore_rate",   $science_explore);
  } else {
    echo "  <input type=hidden name=vessel_rate value=0>\n";
    echo "  <input type=hidden name=explore_rate value=0>\n";
  }

  echo "  <tr><td align=center colspan=11>Note that these settings should add up to 100%</td></tr>\n";
  echo "  <tr><td align=center colspan=11><input type=submit name=submit value=\"Set New Ratings\"></td></tr>\n";

  echo "</table>\n";
  form_end ();
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
function show_improvement ($title, $inputname, $value) {
  echo "  <tr><td colspan=11 align=left><b>".$title."</b></td></tr>\n";
  echo "  <tr>\n";
  for ($i=0; $i!=11; $i++) {
    if ($value==($i*10)) {
      $b1 = "<b>";
      $b2 = "</b>";
      $c1 = "";
    } else {
      $b1 = "";
      $b2 = "";
      $c1 = "class=ylw";
    }
    echo "    <td align=center ".$c1.">".$b1.($i*10)."%".$b2."</td>\n";
  }
  echo "  </tr>\n";
  echo "  <tr border=0>\n";
  for ($i=0; $i!=11; $i++) {
    if ($value==($i*10)) $ch = "checked"; else $ch="";
    echo "    <td align=center><input type=radio name=ne_".$inputname." value=\"".($i*10)."\" ".$ch."></td>\n";
  }
  echo "  </tr>\n";
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
function show_science_tax_ratio_table ($science_ratio) {
  print_remark ("Science tax ratio");

  form_start ();
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("setrate").">\n";
  echo "<table align=center width=50% border=0>\n";
  echo "  <tr>\n";
  echo "    <td colspan=5 align=left><b>Tax Rate</b></td>\n";
  echo "    <td colspan=6 align=right><b>Science Rate</b></td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  for ($i=10; $i!=5; $i--) {
    if ($science_ratio == (100-($i*10))) {
      echo "    <td align=center><b>".($i*10)."%</b></td>\n";
    } else {
      echo "    <td align=center class=ylw>".($i*10)."%</td>\n";
    }
  }
  for ($i=5; $i!=11; $i++) {
    if ($science_ratio == ($i*10)) {
      echo "    <td align=center><b>".($i*10)."%</b></td>\n";
    } else {
      echo "    <td align=center class=ylw>".($i*10)."%</td>\n";
    }
  }
  echo "</tr>\n";
  echo "<tr>\n";
  for ($i=0; $i!=11; $i++) {
    if ($science_ratio==($i*10)) {
      $ch = "checked";
    } else {
      $ch="";
    }
    echo "    <td align=center><input type=radio name=ne_science_ratio value=\"".($i*10)."\" ".$ch."></td>\n";
  }
  echo "  </tr>\n";
  echo "  <tr><td align=center colspan=11><input type=submit name=submit value=\"Set New Tax/Science Ratio\"></td></tr>\n";
  echo "</table>\n";
  form_end ();

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
function show_invention_levels ($uid) {
  $user    = user_get_user ($uid);

  print_remark ("Invention table");
  echo "<table align=center border=0 width=75%>\n";
  echo "  <tr valign=top><td valign=top>\n";

  echo "    <table align=center border=0 width=100%>\n";
  echo "      <tr><td>General improvement: </td><td>".$user['invention_level']." Points</td></tr>\n";
  echo "      <tr><td colspan=2 class=ylw>Invent new general inventions.</td></tr>\n";
  echo "      <tr><td colspan=2>&nbsp;</td></tr>\n";
  echo "      <tr><td>Building improvement: </td><td>".$user['building_level']." Points</td></tr>\n";
  echo "      <tr><td colspan=2 class=ylw>Invent and improve buildings. Higher ratings will invent different types of buildings.</td></tr>\n";
  echo "      <tr><td colspan=2>&nbsp;</td></tr>\n";
  echo "    </table>\n";

  if ($user['impulse'] > 0) {
    echo "  </td><td>\n";
    echo "    <table align=center border=0 width=100%>\n";
    echo "      <tr><td>Vessel improvement: </td><td>".$user['vessel_level']." Points</td></tr>\n";
    echo "      <tr><td colspan=2 class=ylw>Invent and improve vessels. Higher ratings will invent different types of vessels, faster possible speeds and weaponry.</td></tr>\n";
    echo "      <tr><td colspan=2>&nbsp;</td></tr>\n";
    echo "      <tr><td>Space exploration: </td><td>".$user['explore_level']." Points</td></tr>\n";
    echo "      <tr><td colspan=2 class=ylw>Explore new regions of space. Higher ratings will discover more planets and sectors and can intercept messages from further away.</td></tr>\n";
    echo "      <tr><td colspan=2>&nbsp;</td></tr>\n";
    echo "    </table>\n";
  }

  echo "  </td></tr>\n";

  echo "</table>\n";
  echo "<br><br>\n";
}

?>
