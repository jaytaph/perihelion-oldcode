<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Mining",
                 "Here you will find all mining information. The more ore is mined from a planet, the more it's level will tend to red. Once all ores are red on a planet, the planet is exhausted and no more mining can be done.");

    $cmd = input_check ("show", "sid", "uid", 0);

    if ($cmd == "show") {
      if ($uid == "") $uid = user_ourself();
      if ($sid == "") {
        mining_show_all_sectors ($uid);
      } else {
        mining_show_sector ($sid, $uid);
      }
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
function mining_show_all_sectors ($user_id) {

    print_subtitle ("All owned sectors and their planets with mining stations");

    // Get all sectors that we own
    $result = sql_query ("SELECT * FROM g_sectors WHERE user_id=".$user_id);
    $tmp = sql_fetchrow ($result);
    $sectors = split (",", $tmp['csl_sector_id']);
    array_pop ($sectors);  // Last one is a , so it makes an empty field, don't use it..
    foreach ($sectors as $sector) {
      mining_show_sector ($sector, $user_id);
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
function mining_show_sector ($sector_id, $user_id) {
  assert (!empty ($sector_id));
  assert (!empty ($user_id));

  // Check how many planets we own in this sector. If none, don't show anything...
  $result = sql_query ("SELECT COUNT(*) AS count FROM s_anomalies WHERE sector_id = ".$sector_id." AND user_id=".$user_id);
  $tmp= sql_fetchrow ($result);
  if ($tmp['count'] == 0) return;

  // Get sector information
  $sector = sector_get_sector ($sector_id);

  // Only show a table if we got rows in it...
  $first_row = true;

  // Get planet information for all planets in the sector
  $result = sql_query ("SELECT * from s_anomalies WHERE user_id=".$user_id." AND type='P' AND sector_id=".$sector['id']);
    while ($planet = sql_fetchrow ($result)) {
#      if (!in_array ($planet['id'], $planets)) continue;

      $result2 = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$planet['id']);
      $ores = sql_fetchrow ($result2);
      $cur_ores = ore_csl_to_list ($ores['cur_ores']);
      $max_ores = ore_csl_to_list ($ores['max_ores']);

      for ($i=0; $i!=ore_get_ore_count(); $i++) {
	      $fo = "ore".$i."c";
        if ($max_ores[$i] == 0) {
	        $tmp = 0;
        } else {
          $tmp = $cur_ores[$i] / $max_ores[$i] * 100;
        }
        $$fo = "white";
        if ($tmp > 50) $$fo = "yellow";
        if ($tmp > 75) $$fo = "orange";
        if ($tmp > 99) $$fo = "red";
      }

      # Only show a table if we have rows in it...
      if ($first_row) {
        $first_row = false;
        print_remark ("Sector table");
        echo "<table align=center border=0>\n";
        echo "  <tr class=wb><td colspan=".(ore_get_ore_count()+2)."><b><i>Sector ".$sector['sector'].": ".$sector['name']."</i></b></td></tr>\n";
        echo "  <tr class=bl><th>Name</th>";
        for ($i=0; $i!=ore_get_ore_count(); $i++) {
          echo "<th>".ore_get_ore_name($i)."</th>";
        }
        echo "</tr>\n";
      }

      if (! planet_is_minable ($planet['id'])) {
        echo "  <tr class=bl>";
        echo "<td>&nbsp;Planet ".$planet['name']."&nbsp;</td>";
        echo "<th colspan=".ore_get_ore_count().">No mining possible</th>";
        echo "</tr>\n";
      } else {
        echo "  <tr class=bl>";
        echo "<td>&nbsp;<a href=\"anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">Planet ".$planet['name']."</a>&nbsp;</td>";
        for ($i=0; $i!=ore_get_ore_count(); $i++) {
          $fo = "ore".$i."c";
          echo "<td><font color=".$$fo.">".$cur_ores[$i]."</font></td>";
        }
        echo "</tr>\n";
      }
    } // while

    if ($first_row == false) {
      echo "</table>\n";        // Close last sector
      echo "<br><br>\n";
    }
}

?>
