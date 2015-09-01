<?php

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
function scan_scan_area ($vessel_id, $range) {
  $vessel = vessel_get_vessel ($vessel_id);
  $planet = anomaly_get_anomaly ($vessel['planet_id']);

  if (vessel_is_in_orbit ($vessel_id) and anomaly_is_nebula ($planet['id'])) {
    echo "<table align=center border=0 width=75%>\n";
    echo "  <tr><th colspan=4>Cannot scan area while hiding in nebula</th></tr>";
    echo "</table>";
    echo "<br><br>";
    return;
  }

  $i = 0;
  $scans = array();
  list ($scans, $i) = scan_vessels ($vessel_id, $range, $scans, $i);
  list ($scans, $i) = scan_anomalies ($vessel_id, $range, $scans, $i);


  echo "<table align=center border=0 width=75%>\n";
  echo "  <tr class=wb><th colspan=4>Area scan of ".$range." lightyears</th></tr>\n";
  echo "  <tr class=bl><th>Type</th><th>Name</th><th>Coordinates</th><th>Range</th></tr>\n";

  // Sort the hash on range, and print it accordingly
  uasort ($scans, "scan_area_cmp");
  reset ($scans);
  foreach ($scans as $line) {
    echo $line['str'];
  }

  echo "</table>";
  echo "<br><br>";
  return;
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
function scan_vessels ($vessel_id, $range, $scans, $i) {
  list ($src_distance, $src_angle) = get_correct_da ($vessel_id);

  // Scan for vessels
  $result = sql_query ("SELECT * FROM g_vessels ORDER BY user_id");
  while ($vessel = sql_fetchrow ($result)) {
    if ($vessel['id'] == $vessel_id) continue;

    $race = user_get_race ($vessel['user_id']);

    if ($vessel['sector_id'] == 0) {
      $distance = calc_distance ($src_distance, $src_angle, $vessel['distance'], $vessel['angle']);
      if ($distance <= $range and $distance != 0) {
        $scans[$i]['str'] = "<tr class=bl><td>&nbsp;Vessel&nbsp;</td><td>&nbsp;".$vessel['name']." (".$race.")&nbsp;</td><td>&nbsp;".$vessel['distance']." / ".$vessel['angle']."&nbsp;</td><td>&nbsp;".$distance."&nbsp;</td></tr>\n";
        $scans[$i]['range'] = $distance;
        $i++;
      }
    } else {
      $vessel_sector = sector_get_sector ($vessel['sector_id']);
      $distance = calc_distance ($src_distance, $src_angle, $vessel_sector['distance'], $vessel_sector['angle']);
      if ($distance <= $range and $distance != 0) {
        // Check if it's not inside a nebula
        $planet = anomaly_get_anomaly ($vessel['planet_id']);

        if (! (vessel_is_in_orbit ($vessel) and anomaly_is_nebula ($planet['id']))) {
          $scans[$i]['str'] = "<tr class=bl><td>&nbsp;Vessel&nbsp;</td><td>&nbsp;".$vessel['name']." (".$race.")&nbsp;</td><td>&nbsp;SECTOR: ".$vessel_sector['distance']." / ".$vessel_sector['angle']."&nbsp;</td><td>&nbsp;".$distance."&nbsp;</td></tr>\n";
          $scans[$i]['range'] = $distance;
          $i++;
        }
      }
    }
  }

  return array ($scans, $i);
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
function scan_anomalies ($vessel_id, $range, $scans, $i) {
  list ($src_distance, $src_angle) = get_correct_da ($vessel_id);

  // Scan for sectors
  $result = sql_query ("SELECT * FROM s_sectors ORDER BY name");
  while ($sector = sql_fetchrow ($result)) {
    $distance = calc_distance ($src_distance, $src_angle, $sector['distance'], $sector['angle']);
    if ($distance <= $range and $distance != 0) {
      $scans[$i]['str'] = "<tr class=bl><td>&nbsp;Sector&nbsp;</td><td>&nbsp;".$sector['name']."&nbsp;</td><td>&nbsp;".$sector['distance']." / ".$sector['angle']."&nbsp;</td><td>&nbsp;".$distance."&nbsp;</td></tr>\n";
      $scans[$i]['range'] = $distance;
      $i++;
    }
  }

  // Scan for wormhole endpoints
  $result = sql_query ("SELECT * FROM w_wormhole");
  while ($worm = sql_fetchrow ($result)) {
    $tmp = anomaly_get_anomaly ($worm['id']);

    $distance = calc_distance ($src_distance, $src_angle, $worm['distance'], $worm['angle']);
    if ($distance <= $range and $distance != 0) {
      $scans[$i]['str'] = "<tr class=bl><td>&nbsp;Wormhole&nbsp;</td><td>&nbsp;".$tmp['name']."&nbsp;</td><td>&nbsp;".$worm['distance']." / ".$worm['angle']."&nbsp;</td><td>&nbsp;".$distance."&nbsp;</td></tr>\n";
      $scans[$i]['range'] = $distance;
      $i++;
    }
  }


  return array ($scans, $i);
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
// Compares tick from hash $a with hash $b
// This is needed to order the distance of the sectors
function scan_area_cmp ($a, $b) {
  if ($a['range'] < $b['range']) return -1;
  if ($a['range'] > $b['range']) return 1;
  return 0;
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
function get_correct_da ($vessel_id) {
  assert (isset ($vessel_id));

  $vessel = vessel_get_vessel ($vessel_id);

  if (vessel_is_in_orbit ($vessel_id) and $vessel['planet_id'] == 0) {
    $src_distance = $vessel['distance'];
    $src_angle = $vessel['angle'];
  } elseif (vessel_is_in_orbit ($vessel_id) and $vessel['planet_id'] != 0) {
    $planet = anomaly_get_anomaly ($vessel['planet_id']);
    $sector = sector_get_sector ($planet['sector_id']);

    $src_distance = $sector['distance'];
    $src_angle = $sector['angle'];
  } else {
    $src_distance = $vessel['distance'];
    $src_angle = $vessel['angle'];
  }

  return array ($src_distance, $src_angle);
}


?>