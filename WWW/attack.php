<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  // Extra headers for TD..
  print_header ();
  print_title ("Attack",
               "Blahdieblahdieblah");

  // Simulate attack
  $data['attack_id'] = "V0";
  $data['defense_id'] = "V1";
  $data['battlecount'] = 25;
  comm_init_server ();
  comm_s2s ("SIMATTACK", $data);
  $pkg = comm_recv_from_server ();
  comm_fini_server ();

  $wins = $pkg['wins'];
  $draws = $pkg['draws'];
  $losses = $pkg['losses'];
  $avg_a_defense = $pkg['avg_a_defense'];
  $avg_a_strength = $pkg['avg_a_strength'];
  $avg_d_defense = $pkg['avg_d_defense'];
  $avg_d_strength = $pkg['avg_d_strength'];
  $recommendation = $pkg['recommendation'];

  // Show table
  echo "<table align=center width=75%>\n";
  echo "<tr><td><center><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/vessels/1.jpg\" width=150 height=150></center></td><td>\n";
  echo "  <table align=center>\n";
  echo "    <tr class=bl><th colspan=3>Strategic statistics for<br>Vessel Orion Battle 1 vs Planet Orion Prime</th></tr>\n";
  echo "    <tr class=bl><td align=right>".$wins."</td><th width=40%>Wins</th><td>".$losses."</td></tr>\n";
  echo "    <tr class=bl><td align=right>".$draws."</td><th width=40%>Ties</th><td>".$draws."</td></tr>\n";
  echo "    <tr class=bl><td align=right>".$losses."</td><th width=40%>Losses</th><td>".$wins."</td></tr>\n";
  echo "    <tr class=bl><td align=right>".$avg_a_defense."</td><th width=40%>Avg Defence</th><td>".$avg_d_defense."</td></tr>\n";
  echo "    <tr class=bl><td align=right>".$avg_a_strength."</td><th width=40%>Avg Defence</th><td>".$avg_d_strength."</td></tr>\n";
  echo "  </table>\n";
  echo "</td><td><center><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/planets/1.jpg\" width=150 height=150></center></td></tr>\n";
  echo "<tr class=bl><td colspan=3>Strategic recommendation:<br>".$recommendation."</td></tr>\n";
  echo "</table>\n";

  print_footer ();
  exit;

?>