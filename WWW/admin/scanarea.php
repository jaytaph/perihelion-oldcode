<?php

  // Include Files
  include "../includes.inc.php";

  // Session Identification
  session_identification ("admin");

  print_header ();
  print_title ("Scan Area");

  scan_area (4, 5000);
  scan_area (4, 200000);

  print_footer ();
  exit;



function scan_area ($sector, $range) {
  $result = sql_query ("SELECT * FROM s_sectors WHERE id=$sector");
  $sector = sql_fetchrow ($result);


  echo "<table align=center border=1>\n";
  echo "  <tr><th colspan=4>Scanning from origin ".$sector['name']." ( ".$sector['distance']." / ".$sector['angle']." ) @ ".$range." lightyears</th></tr>\n";
  echo "  <tr><td>Name</td><td>Race</td><td>D / A</td><td>Range</td></tr>\n";

  $result = sql_query ("SELECT * FROM g_vessels ORDER BY user_id");
  while ($vessel = sql_fetchrow ($result)) {
    $result2 = sql_query ("SELECT * FROM s_species WHERE user_id = ".$vessel['user_id']);
    $race = sql_fetchrow ($result2);

    if ($vessel['sector_id'] == 0) {
      $distance = calc_distance ($sector['distance'], $sector['angle'], $vessel['distance'], $vessel['angle']);

      if ($distance <= $range) {
        echo "<tr><td>".$vessel['name']." (".$vessel['sector_id'].")</td><td>".$race['name']."</td><td>".$vessel['distance']." / ".$vessel['angle']."</td><td>".$distance."</td></tr>\n";
      }
    } else {
      $result2 = sql_query ("SELECT * FROM s_sectors WHERE id=".$vessel['sector_id']);
      $vessel_sector = sql_fetchrow ($result2);
      $distance = calc_distance ($sector['distance'], $sector['angle'], $vessel_sector['distance'], $vessel_sector['angle']);
      if ($distance <= $range) {
        echo "<tr><td>".$vessel['name']." (".$vessel['sector_id'].")</td><td>".$race['name']."</td><td>SECTOR: ".$vessel_sector['distance']." / ".$vessel_sector['angle']."</td><td>".$distance."</td></tr>\n";
      }
    }

  }

  print "</table>";
  print "<br><br>";
  return;
}

?>
