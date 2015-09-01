<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Convoy view");

//  if (isset ($cid)) {
//    show_convoy_details ($_USER, decrypt_get_vars($cid));
//  } else {
    show_owned_convoys ($_USER);
    echo "<br><br>";
    show_participated_convoys ($_USER);
//  }

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
function show_owned_convoys ($_USER) {

  echo "<table border=1 align=center>";
  echo "  <tr><th colspan=4>All owned convoys</th></tr>";
  echo "  <tr>";
  echo "    <th>Convoy Name</th>";
  echo "    <th>Flag Ship</th>";
  echo "    <th>Ships</th>";
  echo "    <th>Status</th>";
  echo "  </tr>";

  $result = sql_query ("SELECT c.* FROM s_convoys c, g_vessels v WHERE c.vessel_id=v.id AND v.user_id=".$_USER['id']);
  while ($convoy = sql_fetchrow ($result)) {
    // Get the flag ship of the convoy
    $vessel = vessel_get_vessel ($convoy['vessel_id']);

    // Count the number of ships
    $result2 = sql_query ("SELECT c.* FROM s_convoys c, g_vessels v WHERE c.vessel_id=v.id AND v.user_id=".$_USER['id']);
    $tmp = csl_create_array ($result2, "csl_vessel_id");
    $shipcount = count ($tmp);

    // Get the status of the convoy
    $status = $convoy['status'];

    echo "<tr>";
    echo "<td>&nbsp;".$convoy['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$vessel['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$shipcount."&nbsp;</td>";
    echo "<td>&nbsp;".$status."&nbsp;</td>";
    echo "</tr>";
  }
  echo "</table>";
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
function show_participated_convoys ($_USER) {
  echo "<table border=1 align=center>";
  echo "  <tr><th colspan=5>All participated convoys</th></tr>";
  echo "  <tr>";
  echo "    <th>Vessel Name</th>";
  echo "    <th>Convoy Name</th>";
  echo "    <th>Flag Ship</th>";
  echo "    <th>Ships</th>";
  echo "    <th>Status</th>";
  echo "  </tr>";

  $result = sql_query ("SELECT * FROM g_vessels WHERE user_id=".$_USER['id']);
  while ($vessel = sql_fetchrow ($result)) {
    if ($vessel['convoy_id'] == 0) continue;

    $result2 = sql_query ("SELECT * FROM s_convoys WHERE id=".$vessel['convoy_id']);
    $convoy = sql_fetchrow ($result2);

    // Get the flag ship of the convoy
    $flagvessel = vessel_get_vessel ($convoy['vessel_id']);

    // Count the number of ships
    $result2 = sql_query ("SELECT c.* FROM s_convoys c, g_vessels v WHERE c.vessel_id=v.id AND v.user_id=".$_USER['id']);
    $tmp = csl_create_array ($result2, "csl_vessel_id");
    $shipcount = count ($tmp);

    // Get the status of the convoy
    $status = $convoy['status'];

    echo "<tr>";
    echo "<td>&nbsp;".$vessel['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$convoy['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$flagvessel['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$shipcount."&nbsp;</td>";
    echo "<td>&nbsp;".$status."&nbsp;</td>";
    echo "</tr>";
  }
  echo "</table>";
}


?>
