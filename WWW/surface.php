<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Surface");

  $cmd = input_check ("show", "aid", 0);

  if ($cmd == "show") {
    if ($aid == "") $aid = user_get_home_planet (user_ourself());
    show_surface ($aid);
  }

  create_submenu ( array (
                          "View Planet Info" => "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars ($aid),
                          "View Surface Info" => "surface.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars ($aid),
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
function show_surface ($planet_id) {
  global $_GALAXY;

  // Get global information from the user
  $planet = anomaly_get_anomaly ($planet_id);
  $user   = user_get_user ($planet['user_id']);
  $sector = sector_get_sector ($planet['sector_id']);
  $totals = calc_planet_totals ($planet_id);

  // Generate text color for the power
  if ($totals['power_out'] == 0) {
    $power_p = 0;
  } else {
    $power_p = $totals['power_in'] / $totals['power_out'] * 100;
  }
  $power_color = "white";
  if ($power_p > 50) $power_color = "yellow";
  if ($power_p > 75) $power_color = "orange";
  if ($power_p > 99) $power_color = "red";

  // Generate text color for the crew
  if ($planet['population_capacity'] == 0) {
    $crew_p = 0;
  } else {
    $crew_p = $planet['population'] / $planet['population_capacity'] * 100;
  }
  $crew_color = "white";
  if ($crew_p > 50) $crew_color = "yellow";
  if ($crew_p > 75) $crew_color = "orange";
  if ($crew_p > 99) $crew_color = "red";

  print_subtitle ("Surface View on planet ".$planet['name']);


  // ------------------------------------------------------------------
  echo "<table width=75% align=center border=1>\n";
  echo "  <tr><th colspan=2>Global Information on<br>Sector ".$sector['name']." / Planet ".$planet['name']."</th></tr>\n";
  echo "  <tr><td>\n";
  echo "    <table width=100% border=0 cellpadding=0 cellspacing=0>\n";
  echo "      <tr><td>&nbsp;Power Generated&nbsp;</td><td>&nbsp;".$totals['power_out']."&nbsp;</td></tr>\n";
  echo "      <tr><td>&nbsp;Power Needed&nbsp;</td><td>&nbsp;".$totals['power_in']."&nbsp;</td></tr>\n";
  echo "      <tr><td>&nbsp;<font color=$power_color><b>Power Left </b></font>&nbsp;</td><td>&nbsp;<font color=$power_color><b>".($totals['power_out']-$totals['power_in'])."</b></font>&nbsp;</td></tr>\n";
  echo "    </table>\n";
  echo "  </td><td>\n";
  echo "    <table width=100% border=0 cellpadding=0 cellspacing=0>\n";
  echo "      <tr><td>&nbsp;<font color=$crew_color>Inhabitants</font>&nbsp;</td><td>&nbsp;<font color=$crew_color>".$planet['population']."</font>&nbsp;</td></tr>\n";
  echo "      <tr><td>&nbsp;<font color=$crew_color>Capacity</font>&nbsp;</td><td>&nbsp;<font color=$crew_color>".$planet['population_capacity']."</font>&nbsp;</td></tr>\n";
  echo "      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
  echo "    </table>\n";
  echo "  </td></tr>\n";
  echo "</table>\n";
  echo "<br>\n";
  echo "<br>\n";


  // Get all buildings and cargo on the planet
  $surface = planet_get_surface ($planet_id);
  $current_buildings = csl ($surface['csl_building_id']);
  $current_buildings = array_count_values ($current_buildings);
  $current_cargo = csl ($surface['csl_cargo_id']);
  $current_cargo = array_count_values ($current_cargo);

  // ------------------------------------------------------------------
  // Show current buildings on the surface
//  echo "<table width=75% align=center border=0 cellpadding=0 cellspacing=0>\n";
  echo "<table width=75% align=center border=0>\n";
  echo "  <tr class=wb><th colspan=5>Buildings on the planet</th></tr>\n";
  echo "  <tr class=bl>";
  echo "<th>Qty</th>";
  echo "<th>Name</th>";
  echo "<th>Power In</th>";
  echo "<th>Power Out</th>";
  echo "<th>Description</th>";
  echo "</tr>\n";

  reset ($current_buildings);
  while (list ($building_id, $quantity) = each ($current_buildings)) {
    $building = building_get_building (building_active_or_inactive ($building_id));
    if (building_is_active ($building_id)) {
    } else {
    }


    if (building_is_active ($building_id)) {
      $active = "<img alt=Active src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/active.gif>";
      echo "  <tr class=bl>\n";
      echo "    <td>&nbsp;".$quantity."&nbsp;</td>\n";
      echo "    <td>&nbsp;".$active."&nbsp;".$building['name']."&nbsp;</td>\n";
      echo "    <td>&nbsp;".$building['power_in']."&nbsp;</td>\n";
      echo "    <td>&nbsp;".$building['power_out']."&nbsp;</td>\n";
      echo "    <td>&nbsp;".$building['rule']."&nbsp;</td>\n";
      echo " </tr>\n";
    } else {
      $active = "<img alt=Inactive src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/inactive.gif>";
      echo "  <tr class=bl>\n";
      echo "    <td><font color=red>&nbsp;".$quantity."&nbsp;</font></td>\n";
      echo "    <td><font color=red>&nbsp;".$active."&nbsp;".$building['name']."&nbsp;</font></td>\n";
      echo "    <td colspan=3><font color=red>&nbsp;Building not active&nbsp;</font></td>\n";
      echo " </tr>\n";
    }
  }
  echo "</table>\n";
  echo "<br><br>\n";

  // ------------------------------------------------------------------
  // Show current items on the surface
  echo "<table width=75% align=center border=0>\n";
  echo "  <tr class=wb><th colspan=5>Cargo and machines on planet</th></tr>\n";
  echo "  <tr class=bl>";
  echo "<th>Qty</th>";
  echo "<th>Name</th>";
  echo "<th>Description</th>";
  echo "</tr>\n";

  reset ($current_cargo);
  while (list ($item_id, $quantity) = each ($current_cargo)) {
    $invention = item_get_item ($item_id);
    if (invention_is_active_on_planet ($invention)) {
      $activeimg = "alt=Active src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/active.gif";
    } else {
      $activeimg = "alt=Inactive src=".$_CONFIG['URL'].$_GALAXY['image_dir']."/general/inactive.gif";
    }

    echo "  <tr class=bl>\n";
    echo "    <td>&nbsp;".$quantity."&nbsp;</td>\n";
    echo "    <td>&nbsp;<img ".$activeimg.">&nbsp;".$invention['name']."&nbsp;</td>\n";
    echo "    <td>&nbsp;".$invention['rule']."&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";
  echo "<br><br>\n";

}



?>
