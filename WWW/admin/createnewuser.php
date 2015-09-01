<?php

  // Include Files
  include "../includes.inc.php";

  // Session Identification
  session_identification ("admin");

  print_header ();

  print_title ("Create a new user");

  echo "<strong>Use this in the px_server option:</strong>";
  echo "<li>USER    : username\n";
  echo "<li>PASS    : password\n";
  echo "<li>SECTOR  : sector name\n";
  echo "<li>SPECIES : specie name\n";
  echo "<li>CMD     : NEWPLAYER\n";


  print_footer ();
  exit;

?>
