<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Convoy move");

  show_owned_convoys ($_USER['id']);

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
function show_owned_convoys ($user_id) {
  assert (is_numeric ($user_id));

  echo "<table border=1 align=center>";
  echo "  <tr>";
  echo "    <th>Convoy Name</th>";
  echo "    <th>Flag Ship</th>";
  echo "    <th>Ships</th>";
  echo "    <th>Status</th>";
  echo "  </tr>";

  $result = sql_query ("SELECT c.* FROM s_convoys c, s_vessels v WHERE c.vessel_id=v.id AND v.user_id=".$user_id);
  while ($convoy = sql_fetchrow ($result)) {
    // Get the flag ship of the convoy
    $vesseltype = vessel_get_vessel_type ($convoy['id']);

    // Count the number of ships
    $result2 = sql_query ("SELECT c.* FROM s_convoys c, s_vessels v WHERE c.vessel_id=v.id AND v.user_id=".$user_id);
    $tmp = csl_create_array ($result2, "vessel_ids");
    $shipcount = count ($tmp);

    // Get the status of the convoy
    $status = $convoy['status'];

    echo "<tr>";
    echo "<td>&nbsp;".$convoy['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$vesseltype['name']."&nbsp;</td>";
    echo "<td>&nbsp;".$shipcount."&nbsp;</td>";
    echo "<td>&nbsp;".$status."&nbsp;</td>";
    echo "</tr>";
  }
  echo "</table>";
}

?>
