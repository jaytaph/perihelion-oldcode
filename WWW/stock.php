<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Stock & upkeep information",
                 "All information about your planet's stock and upkeep are located on this page. The 'Delta Upkeep' will show you wether or not your planet makes a profit. If this is a negative red number, you should increase the happieness rating of the planet, remove buildings or add more people to the planet. The 'ore upkeep per tick' should be on a white level, the more it tends to the red color, the less ore you have available for upkeep. If no ores are left for upkeep, buildings and vessels from that planet will be disabled until you replenish the ores.");

  $cmd = input_check ("show", "sid", 0);

  if ($cmd == "show") {
    if ($sid == "") {
      stock_show_all_sectors (user_ourself());
    } else {
      stock_show_sector ($sid);
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
function stock_show_all_sectors ($user_id) {
  asserT ($user_id);

  print_subtitle ("All owned sectors and their planets stocks");

  // Get all sectors that we own
  $result = sql_query ("SELECT * FROM g_sectors WHERE user_id=".$user_id);
  $tmp = sql_fetchrow ($result);
  $sectors = split (",", $tmp['csl_sector_id']);
  array_pop ($sectors);  // Last one is a , so it makes an empty field, don't use it..
  foreach ($sectors as $sector) {
    stock_show_sector ($sector, $user_id);
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
function stock_show_sector ($sector_id, $user_id) {
    assert (!empty($sector_id));
    assert (!empty($user_id));

    global $_CONFIG;

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

      $result2 = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$planet['id']);
      $ores = sql_fetchrow ($result2);
      $stock_ores = ore_csl_to_list ($ores['stock_ores']);
      $upkeep_ores =  ore_csl_to_list ($planet['upkeep_ores']);

      for ($i=0; $i!=ore_get_ore_count(); $i++) {
	      $fo = "ore".$i."c";
        $$fo = "white";

	      if ($upkeep_ores[$i] == 0) {
	        $ticks_left = 0;
	      } else {
  	      $ticks_left = $stock_ores[$i] / $upkeep_ores[$i];
          if ($ticks_left < 50) $$fo = "yellow";
          if ($ticks_left < 25) $$fo = "orange";
          if ($ticks_left < 10) $$fo = "red";
        }
      }


      $user = user_get_user ($planet['user_id']);

      if ($user['science_ratio'] == 100 or $planet['happieness'] == 0) {
        $planet_income = 0;
      } else {
        $planet_income  = $planet['population'] / (100 / (100 - $user['science_ratio']));
	      $planet_income  = $planet_income / $_CONFIG['h_credits_dividor'];
	      $planet_income  = $planet_income / (100 / $planet['happieness']);
      }
      $planet_upkeep = $planet['upkeep_costs'];
      $delta_upkeep = intval ($planet_income - $planet_upkeep);


      // Only show a table if we have rows in it...
      if ($first_row) {
        $first_row = false;
        print_remark ("Sector table");
        echo "<table align=center border=0>\n";
        echo "  <tr class=wb><td colspan=".(ore_get_ore_count()+2)."><b><i>Sector ".$sector['sector'].": ".$sector['name']."</i></b></td></tr>\n";
        echo "  <tr class=bl><th colspan=2>Name</th>";
        for ($i=0; $i!=ore_get_ore_count(); $i++) {
          echo "<th>".ore_get_ore_name($i)."</th>";
        }
        echo "</tr>\n";
      }

      echo "  <tr class=bl>\n";
      echo "    <td rowspan=2 valign=top>\n";
      echo "      &nbsp;<a href=\"anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">Planet ".$planet['name']."</a>&nbsp;<br>\n";
      if ($delta_upkeep < 0) {
        echo "      &nbsp;<font color=red>Delta Upkeep: ".$delta_upkeep."</font>&nbsp;\n";
      } else {
        echo "      &nbsp;Delta Upkeep: ".$delta_upkeep."&nbsp;\n";
      }
      echo "    </td>\n";

      echo "<td>&nbsp;Current in stock&nbsp;</td>";
      for ($i=0; $i!=ore_get_ore_count(); $i++){
        $fo = "ore".$i."c";
        echo "<td align=right><font color=".$$fo.">".$stock_ores[$i]."</font></td>";
      }
      echo "</tr>\n";

      echo "  <tr class=bl>";
      echo "<td>&nbsp;Upkeep per tick&nbsp;</td>";
      for ($i=0; $i!=ore_get_ore_count(); $i++){
        $fo = "ore".$i."c";
        echo "<td align=right><font color=".$$fo.">".$upkeep_ores[$i]."</font></td>";
      }
      echo "</tr>\n";

    } // while

    if ($first_row == false) {
      echo "</table>\n";        // Close last sector
      echo "<br><br>\n";
    }
}

?>
