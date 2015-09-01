<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Trade routes");

  $cmd = input_check ("show",   "uid", 0,
                      "delete", "uid", 0,
                      "create", "uid", 0,
                      "delete2", "!frmid", "!tid", 0,
                      "create2", "!frmid", "!vid", "!src_pid", "!dst_pid", 0);

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    trade_show_routes ($uid);
  } elseif ($cmd == "create") {
    if ($uid == "") $uid = user_ourself();
    trade_create_route ($uid);
  } elseif ($cmd == "delete") {
    if ($uid == "") $uid = user_ourself();
    trade_delete_route ($uid);
  };

  // Execute delete
  if ($cmd == "delete2") {
    $error = "";
    $ok = "";
    $data['aid'] = $tid;
    comm_send_to_server ("TRADEDELETE", $data, $error, $ok);
    print_line ("Traderoute deleted.\n");
  }

  // Execute create
  if ($cmd == "create2") {
    for ($i=0; $i!=ore_get_ore_count(); $i++) {
      $tmp1 = "src_ore_".$i;
      $tmp2 = "dst_ore_".$i;
      if (!isset ($$tmp1)) $$tmp1 = "";
      if (!isset ($$tmp2)) $$tmp2 = "";
    }

    $vid = decrypt_get_vars ($_POST['vid']);
    $src_pid = decrypt_get_vars ($_POST['src_pid']);
    $dst_pid = decrypt_get_vars ($_POST['dst_pid']);

    $ok = "";
    $errors['PARAMS']     = "Incorrect parameters specified.";
    $errors['SHORTROUTE'] = "The source and destination planets are the same.";
    $errors['INTRADE']    = "The vessel is already part of a traderoute.";
    $data['vid'] = $vid;
    $data['src_pid'] = $src_pid;
    $data['dst_pid'] = $dst_pid;

    // Nasty misuse of the $_REQUEST here, but since we don't know how many ores we have
    // we make direct use of the $_REQUEST array. Normally this is done by the input_check()
    // function.
    for ($i=0; $i!=ore_get_ore_count(); $i++) {
      $tmp1 = "src_ore_".$i;
      $tmp2 = "dst_ore_".$i;
      if (array_key_exists ($tmp1, $_REQUEST)) {
        $data[$tmp1] = $_REQUEST[$tmp1];
      } else {
        $data[$tmp1] = "";
      }
      if (array_key_exists ($tmp2s, $_REQUEST)) {
        $data[$tmp2] = $_REQUEST[$tmp2];
      } else {
        $data[$tmp2] = "";
      }
    }
    if (comm_send_to_server ("TRADECREATE", $data, $ok, $errors) == 1) {
      // Make sure we move our vessel to the source planet. After this, the heartbeat deamon
      // takes over and start moving the vessel from source to destination and back...
      $vessel = vessel_get_vessel ($vid);
      $data = "";
      $data['vid'] = $vid;
      $data['did'] = $src_pid;
      $data['uid'] = $vessel['user_id'];
      comm_send_to_server ("MOVE", $data, "", "");
      print_line ("Traderoute created.\n");
    }
  }


  create_submenu ( array (
                    "Show Traderoutes"   => "trade.php?cmd=".encrypt_get_vars("show"),
                    "Create Traderoutes" => "trade.php?cmd=".encrypt_get_vars("create"),
                    "Delete Traderoutes" => "trade.php?cmd=".encrypt_get_vars("delete")
                   )
                 );


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
function trade_create_route ($user_id) {
  assert (is_numeric ($user_id));

  $found_at_least_one_ship = false;
  $result = sql_query ("SELECT g.* FROM g_vessels AS g, s_vessels AS s WHERE g.user_id=$user_id AND s.id = g.vessel_id AND s.type='".VESSEL_TYPE_TRADE."'");
  while ($vessel = sql_fetchrow ($result)) {
    if (! vessel_in_traderoute ($vessel['id'])) $found_at_least_one_ship = true;
  }
  if ($found_at_least_one_ship == false) {
    print_line ("You do not have any tradeships currently available for setting up a traderoute");
    return;
  }


  print_remark ("Create form");
  form_start ();
  echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("create2").">\n";
  echo "  <table align=center>\n";

  echo "  <tr>\n";
  echo "    <td>&nbsp;Vessel: &nbsp;</td>\n";
  echo "    <td>\n";
      echo "      <select name=vid>\n";
      $result = sql_query ("SELECT g.* FROM g_vessels AS g, s_vessels AS s WHERE g.user_id=$user_id AND s.id = g.vessel_id AND s.type='".VESSEL_TYPE_TRADE."'");
      while ($vessel = sql_fetchrow ($result)) {
        if (! trade_is_vessel_in_route ($vessel['id'])) {
          echo "        <option value=".encrypt_get_vars ($vessel['id']).">".$vessel['name']."</option>\n";
        }
      }
      echo "      </select>\n";
  echo "    </td>\n";
  echo "  </tr>\n";
  echo "  <tr><td>&nbsp;</td></tr>\n";
  echo "  <tr>\n";
  echo "    <td>&nbsp;Source Planet: &nbsp;</td>\n";
  echo "    <td>\n";
      echo "      <select name=src_pid>\n";
      $result = sql_query ("SELECT a.*,s.name AS sectorname FROM s_anomalies AS a, s_sectors AS s WHERE a.user_id=$user_id AND a.type='P' AND a.sector_id = s.id");
      while ($planet = sql_fetchrow ($result)) {
        if (anomaly_is_planet ($planet['id']) and planet_is_minable ($planet['id'])) {
          echo "        <option value=".encrypt_get_vars ($planet['id']).">".$planet['sectorname']." / ".$planet['name']."</option>\n";
        }
      }
      echo "      </select>\n";
  echo "    </td>\n";
  echo "  </tr>\n";
      echo "  <tr>\n";
      echo "    <td>&nbsp;Ores: &nbsp;</td>\n";
      echo "    <td>";
      echo "      <table border=0 cellspacing=0 colspacing=0>\n";
      echo "        <tr>";
      for ($i=0; $i!=ore_get_ore_count(); $i++) {
        if ($i % 3 == 0) {
          echo "        </tr>\n";
          echo "        <tr>\n";
        }
        echo "          <td><input type=checkbox name=src_ore_".$i.">".ore_get_ore_name ($i)."</td>\n";
      }
      echo "        </tr>\n";
      echo "      </table>\n";
  echo "    </td>\n";
  echo "  </tr>\n";
  echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
  echo "  <tr>\n";
  echo "    <td>&nbsp;Destination Planet: &nbsp;</td>\n";
  echo "    <td>\n";
      echo "      <select name=dst_pid>\n";

      $result = sql_query ("SELECT * FROM g_anomalies WHERE user_id=$user_id");
      $planetlist = csl_create_array ($result, 'csl_discovered_id');

      // Get all planets which we own...
      foreach ($planetlist as $planet_id) {
        $planet = anomaly_get_anomaly ($planet_id);
        if ($planet['user_id'] == 0) continue;
        if ($planet['user_id'] != $user_id) continue;

        if (user_is_mutual_friend ($user_id, $planet['user_id']) and anomaly_is_planet ($planet['id']) and planet_is_minable ($planet['id'])) {
          $sector = sector_get_sector ($planet['sector_id']);
          echo "        <option value=".encrypt_get_vars ($planet['id']).">".$sector['name']." / ".$planet['name']."</option>\n";
        }
      }
      // And now, all other planets with a different user_id
      foreach ($planetlist as $planet_id) {
        $planet = anomaly_get_anomaly ($planet_id);
        if ($planet['user_id'] == 0) continue;
        if ($planet['user_id'] == $user_id) continue;

        if (user_is_mutual_friend ($user_id, $planet['user_id']) and anomaly_is_planet ($planet['id']) and planet_is_minable ($planet)) {
          $sector = sector_get_sector ($planet['sector_id']);
          $race   = user_get_race ($planet['user_id']);
          echo "        <option value=".encrypt_get_vars ($planet['id']).">(".$race.") ".$sector['name']." / ".$planet['name']."</option>\n";
        }
      }
      echo "      </select>\n";
  echo "    </td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>&nbsp;Ores: &nbsp;</td>\n";
  echo "    <td>\n";
      echo "      <table border=0 cellspacing=0 colspacing=0>\n";
      echo "        <tr>\n";
      for ($i=0; $i!=ore_get_ore_count(); $i++) {
        if ($i % 3 == 0) {
          echo "      </tr>\n";
          echo "      <tr>\n";
        }
        echo "        <td><input type=checkbox name=dst_ore_".$i.">".ore_get_ore_name ($i)."</td>\n";
      }
      echo "     </tr>\n";
      echo "    </table>\n";
  echo "      </td>\n";
  echo "    </tr>\n";

  echo "    <tr><td colspan=2>&nbsp;</td></tr>\n";
  echo "    <tr><td></td><td><input type=submit name=submit value='create traderoute'></td></tr>\n";

  echo "  </table>\n";
  form_end ();
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
function trade_is_vessel_in_route ($vessel_id) {
  assert (is_numeric ($vessel_id));

  $result = sql_query ("SELECT * FROM a_trades WHERE vessel_id=".$vessel_id);
  if (sql_fetchrow ($result)) return true;
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
function trade_delete_route ($user_id) {
  assert (is_numeric ($user_id));

  $firstrow = 1;

  $result = sql_query ("SELECT * FROM a_trades");
  while ($traderoute = sql_fetchrow ($result)) {
    $src_planet = anomaly_get_anomaly ($traderoute['src_planet_id']);
    $dst_planet = anomaly_get_anomaly ($traderoute['dst_planet_id']);

    // We don't own the source or destination planet... skip it..
    if ($src_planet['user_id'] != $user_id and
    	  $dst_planet['user_id'] != $user_id) continue;

    $vessel = vessel_get_vessel ($traderoute['vessel_id']);

    if ($firstrow == 1) {
      $firstrow = 0;
      print_remark ("Delete form");
      form_start ();
      echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("delete2").">\n";
      echo "  <table align=center>\n";
      echo "    <tr>\n";
      echo "      <td>\n";
      echo "        <select name=tid>\n";
    }
    echo "          <option value=".encrypt_get_vars ($traderoute['id']).">".$vessel['name']." from ".$src_planet['name']." to ".$dst_planet['name']."</option>\n";
  }

  if ($firstrow == 0) {
    echo "        </select>\n";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "    <tr><td><input type=submit name=submit value='Delete Traderoute'></td></tr>\n";
    echo "  </table>\n";
    form_end ();
  } else {
    print_line ("There are currently no traderoutes to delete.");
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
function trade_show_routes ($user_id) {
  assert (is_numeric ($user_id));
  global $_GALAXY;

  $firstrow = 1;

  $result = sql_query ("SELECT * FROM a_trades");
  while ($traderoute = sql_fetchrow ($result)) {
    $src_planet = anomaly_get_anomaly ($traderoute['src_planet_id']);
    $dst_planet = anomaly_get_anomaly ($traderoute['dst_planet_id']);

    // We don't own the source or destination planet... skip it..
    if ($src_planet['user_id'] != $user_id and
       	$dst_planet['user_id'] != $user_id) continue;

    $vessel = vessel_get_vessel ($traderoute['vessel_id']);

    $ore1 = "";
    $ore2 = "";

    if ($traderoute['src_ore'] == ORE_NONE) {
      $ore1 = "None, ";
    } elseif ($traderoute['src_ore'] == ORE_ALL) {
      $ore1 = "All ores, ";
    } else {
      $ores = csl ($traderoute['src_ore']);
      foreach ($ores as $ore) {
        $ore1 .= ore_get_ore_name ($ore) . ", ";
      }
    }
    // Chop off last comma
    $ore1 = substr_replace ($ore1, "", -2);

    if ($traderoute['dst_ore'] == ORE_NONE) {
      $ore2 = "None, ";
    } elseif ($traderoute['dst_ore'] == ORE_ALL) {
      $ore2 = "All ores, ";
    } else {
      $ores = csl ($traderoute['dst_ore']);
      foreach ($ores as $ore) {
        $ore2 .= ore_get_ore_name ($ore) . ", ";
      }
    }
    // Chop off last comma
    $ore2 = substr_replace ($ore2, "", -2);

    if ($firstrow == 1) {
      $firstrow = 0;
      print_remark ("Vessel table");
      echo "<table align=center width=80% border=0>\n";
      echo "  <tr class=wb>";
      echo "<th>Vessel</th>";
      echo "<th>Source</th>";
      echo "<th>Ores</th>";
      echo "<th>Destination</th>";
      echo "<th>Ores</th>";
      echo "</tr>\n";
    }

    echo "  <tr class=bl>\n";
    echo "    <td>&nbsp;<img src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/ships/trade.jpg>&nbsp;<a href=vessel.php?cmd=".encrypt_get_vars("showvid")."&vid=".encrypt_get_vars ($vessel['id']).">".$vessel['name']."</a>&nbsp;</td>\n";
    echo "    <td>&nbsp;<a href=anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars ($src_planet['id']).">".$src_planet['name']."</a>&nbsp;</td>\n";
    echo "    <td>&nbsp;".$ore1."&nbsp;</td>\n";
    echo "    <td>&nbsp;<a href=anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars ($dst_planet['id']).">".$dst_planet['name']."</a>&nbsp;</td>\n";
    echo "    <td>&nbsp;".$ore2."&nbsp;</td>\n";
    echo "  </tr>\n";
  }

  if ($firstrow == 0) {
    echo "</table>\n";
    echo "<br><br>\n";
  } else {
    print_line ("There are currently no traderoutes available.");
  }
}


?>
