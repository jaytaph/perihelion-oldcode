<?php


// ============================================================================================
//
//
// Description:
//    Calculates the planet output for power in, power out en crew needed.
//
//
// Parameters:
//
//
// Returns:
//
function calc_planet_totals ($planet_id) {
  assert (is_numeric ($planet_id));

  $totals['power_in']   = 0;
  $totals['power_out']  = 0;

  // Get all buildings that are currently build on the planet
  $surface = planet_get_surface ($planet_id);
  $buildings = csl ($surface['csl_building_id']);
  reset ($buildings);
  while (list ($key, $building_id) = each ($buildings)) {
    if (! building_is_active ($building_id)) continue;
    $building = building_get_building ($building_id);
    $totals['power_in']   = $totals['power_in'] + $building['power_in'];
    $totals['power_out']  = $totals['power_out'] + $building['power_out'];
  }

  return $totals;
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
/*************************************************************************
 * Calculates the distance between 2 points which are marked by radius/angle.
 * This function can be used to calculate the distance between the galactic
 * core and a starsystem, between to star systems or between 2 ships.
 */
function calc_distance ($r1, $a1, $r2, $a2) {
  assert (($r1 >= 0));
  assert (($a1 >= 0) && ($a1 <= 360000) );
  assert (($r2 >= 0));
  assert (($a2 >= 0) && ($a2 <= 360000));

  // First, calculate X1,Y1 and X2,Y2, the coordinates inside the
  // (virtual) galaxy grid
  $Y1 = $r1 * cos (deg2rad($a1/1000));       // RADS! Which in fact, doesn't mind
                                        // since all points are virtual
  $X1 = $r1 * sin (deg2rad($a1/1000));
  $Y2 = $r2 * cos (deg2rad($a2/1000));
  $X2 = $r2 * sin (deg2rad($a2/1000));

  // Get the (absolute) delta of the 2 points
  $DX = abs ($X1-$X2);
  $DY = abs ($Y1-$Y2);

  // Pythagoras says: c^2 = a^2 + b^2. At least, he used to say, he's dead now...
  $c = sqrt (($DX*$DX) + ($DY*$DY));

  // round ($c, 4);   // PHP >= 4.0.4??
  $c = $c * 1000;       // Keep 3 digits after the comma
  $c = round ($c);      // Round it
  $c = $c / 1000;       // And back with those digits...

  return $c;        // Return schuine zijde
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
/******************************************************************************
 * Calculate the number of ticks needed to travel betweet src and dst with
 * $imp impulse speed.
 */
function calc_planet_ticks ($src_d, $src_a, $dst_d, $dst_a, $imp, $warp) {
  assert (($src_d >= 0));
  assert (($src_a >= 0) && ($src_a <= 360000) );
  assert (($dst_d >= 0));
  assert (($dst_a >= 0) && ($dst_a <= 360000));

  $ticks = calc_distance ($src_d, $src_a, $dst_d, $dst_a);
  if ($ticks == 0) return 0;

  $ticks = ( $ticks / 1000 ) / $imp / 2;
  $ticks = round ($ticks);
  if ($ticks == 0) $ticks += 1;

//  echo "TICKS 1: $ticks<br>\n";
//  $ticks = $ticks * (((100-$imp) / 100)/5);
//  echo "TICKS 2: $ticks   IMP: $imp<br>\n";
//  $ticks = round ($ticks / $_CONFIG['impulse_dividor']);
//  echo "TICKS 3: $ticks   CNF: ".$_CONFIG['impulse_dividor']."<br>\n";
  return $ticks;
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
/******************************************************************************
 * Calculate the number of ticks needed to travel between src and dst
 * with $warp warp speed.
 */
function calc_sector_ticks ($src_d, $src_a, $dst_d, $dst_a, $warp) {
  assert (($src_d >= 0));
  assert (($src_a >= 0) && ($src_a <= 360000) );
  assert (($dst_d >= 0));
  assert (($dst_a >= 0) && ($dst_a <= 360000));
  assert (($warp >= 0) && ($warp <= 100));
  global $_CONFIG;

  if ($warp == 0) return 0;
  $ticks = calc_distance ($src_d, $src_a, $dst_d, $dst_a);
  $ticks = $ticks * ((100 / ($warp/10))/10);
  $ticks = round ($ticks / $_CONFIG['warp_dividor']);
  return $ticks;
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
function calculate_uptime ($seconds) {
  assert (is_numeric ($seconds));

  $days = 0;
  $hours = 0;
  $minuts = 0;

  $days = floor($seconds / 86400);
  if ($days > 0) $seconds -= $days * 86400;

  $hours = floor($seconds / 3600);
  if ($days > 0 || $hours > 0) $seconds -= $hours * 3600;

  $minutes = floor($seconds / 60);
  if ($days > 0 || $hours > 0 || $minutes > 0) $seconds -= $minutes * 60;

  $uptime = sprintf("%s days, %s hours, %s minutes and %s seconds", (string)$days, (string)$hours, (string)$minutes, (string)$seconds);
  return $uptime;
}




?>
