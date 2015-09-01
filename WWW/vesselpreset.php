<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Vessel Flight presets",
                 "Flight presets are automated distance/angle coordinates which you can use in your vessel movements. Secret hiding places or alliance locations can be stored safely.");


    $cmd = input_check ("show",   "uid", 0,                 // Show presets
                        "delete", "uid", "!pid", 0,
                        "create", "uid", "!ne_name", "!ne_distance", "!ne_angle", 0);

    if ($cmd == "delete") {
        $ok = "";
        $errors['PARAMS'] = "Incorrect parameters specified..\n";
        $data['action']   = "delete";
        $data['pid']      = $pid;
        $data['distance'] = 0;
        $data['angle']    = 0;
        $data['name']     = 0;
        $data['uid']      = 0;
        comm_send_to_server ("PRESET", $data, $ok, $errors);
    }

    if ($cmd == "create") {
      $distance = substr ($ne_distance, 0, 5);
      $angle = substr($ne_angle, 0, 6);

      if (! preg_match ("/^\d+$/", $distance)) {
        print_line ("<li><font color=red>You should enter a distance in the format ######.</font>\n");
      } elseif (! preg_match ("/^\d{1,6}$/", $angle)) {
        print_line ("<li><font color=red>You should enter an angle in the format ######.</font>\n");
      } else if ($distance < $_GALAXY['galaxy_core']) {
        print_line ("<li><font color=red>You cannot fly that far into the galaxy core. Try a higher distance (minimum is ".$_GALAXY['galaxy_core'].").</font>\n");
      } elseif ($distance > $_GALAXY['galaxy_size']) {
        print_line ("<li><font color=red>You cannot fly outside of the galaxy. Try a lower distance (maximum is ".$_GALAXY['galaxy_size'].").</font>\n");
      } else {
        $ok = "";
        $errors['PARAMS'] = "Incorrect parameters specified..\n";
        $errors['NAME']   = "The preset name you already used.\n";
        $data['action']   = "create";
        $data['distance'] = $distance;
        $data['angle']    = $angle;
        $data['name']     = $ne_name;
        $data['uid']      = $uid;
        $data['pid']      = 0;
        comm_send_to_server ("PRESET", $data, $ok, $errors);
      }
    }


    // Show command, always executed.
    if ($uid == "") $uid = user_ourself();
    preset_show_all_presets ($uid);


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
function preset_show_all_presets ($user_id) {
  assert (is_numeric ($user_id));

  echo "<table align=center widht=75%>\n";
  echo "  <tr class=wb>\n";
  echo "    <th>Preset name</th>\n";
  echo "    <th>Distance</th>\n";
  echo "    <th>Angle</th>\n";
  echo "    <th>&nbsp;</th>\n";
  echo "  </tr>\n";

  // Get all presets
  $result = sql_query ("SELECT * FROM g_presets WHERE user_id=".$user_id);
  while ($preset = sql_fetchrow ($result)) {
    echo "  <tr class=bl>\n";
    echo "    <td>&nbsp;".$preset['name']."&nbsp;</td>\n";
    echo "    <td>&nbsp;".$preset['distance']."&nbsp;</td>\n";
    echo "    <td>&nbsp;".$preset['angle']."&nbsp;</td>\n";
    echo "    <td>&nbsp;[ <a href=vesselpreset.php?cmd=".encrypt_get_vars("delete")."&uid=".encrypt_get_vars($user_id)."&pid=".encrypt_get_vars($preset['id']).">Delete</a> ]&nbsp;</td>\n";
    echo "  </tr>\n";
  }

  // And add room to create a new one...
    echo "  <tr class=bl>\n";
    form_start ();
    echo "    <input type=hidden name=cmd value=".encrypt_get_vars ("create").">\n";
    echo "    <input type=hidden name=uid value=".encrypt_get_vars ($user_id).">\n";
    echo "    <td><input type=text name=ne_name size=20 maxlength=20></td>\n";
    echo "    <td><input type=text name=ne_distance size=6 maxlength=6></td>\n";
    echo "    <td><input type=text name=ne_angle size=7 maxlength=7></td>\n";
    echo "    <td><input type=submit name=name=submit value=Add></td>\n";
    form_end ();
    echo "  </tr>\n";

  echo "</table>\n";
}



?>
