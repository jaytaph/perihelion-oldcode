<?php

  // Include Files
  include "../includes.inc.php";

  // Session Identification
  session_identification ("admin");

  print_header ();
  print_title ("Tick Scheme");

  $ticks = calc_sector_ticks ($_GALAXY['galaxy_size'], 0, $_GALAXY['galaxy_size'], 180, 99);
  print_line ("Crossing ticks at warp 9.9: $ticks");
  $ticks = calc_sector_ticks ($_GALAXY['galaxy_size'], 0, $_GALAXY['galaxy_size'], 180, 50);
  print_line ("Crossing ticks at warp 5.0: $ticks");
  $ticks = calc_sector_ticks ($_GALAXY['galaxy_size'], 0, $_GALAXY['galaxy_size'], 180, 10);
  print_line ("Crossing ticks at warp 1.0: $ticks");

  warp_scheme (99);
  warp_scheme (50);
  warp_scheme (10);

  print_footer ();
  exit;



function warp_scheme ($warp) {
  $result = sql_query ("SELECT COUNT(*) AS nr FROM s_sectors");
  $tmp = sql_fetchrow ($result);
  $count = $tmp['nr'];
  if ($count > 30) {
    $count = 30;
    echo "WARNING: Only the first 30 sectors are viewed now!!!<br>\n";
  }
  $result = sql_query ("SELECT * FROM s_sectors");

  print "<table align=center border=1>";
  print "  <tr><th colspan=".($count+1).">Warp Factor ".$warp."</th></tr>";
  print "  <tr><th>&nbsp;</th>";

  $sector_arr = array ();
  $result = sql_query ("SELECT * FROM s_sectors");
  while ($s = sql_fetchrow ($result)) {
    array_push ($sector_arr, $s);
  }

  // Create top columns
  reset ($sector_arr);
  foreach ($sector_arr as $sector) {
    echo "<th>&nbsp;".$sector['name']."&nbsp;</th>";
  }
  echo  "</tr>\n";

  // And create rows
  reset ($sector_arr);
  foreach ($sector_arr as $sector) {
    echo "<tr><th>&nbsp;".$sector['name']."&nbsp;</th>";
    for ($i = 1; $i <= $count; $i++) {
      if ($sector['name'] == $sector_arr[$i-1]['name']) {
        echo "<td align=center>&nbsp;</td>";
      } else {
        $sector_ticks = calc_sector_ticks ($sector['distance'], $sector['angle'],$sector_arr[$i-1]['distance'], $sector_arr[$i-1]['angle'], $warp);
        echo "<td align=center>&nbsp;".$sector_ticks."&nbsp;</td>";
      }
    }
    echo "</tr>\n";
  }

  print "</table>";
  print "<br><br>";
}

?>
