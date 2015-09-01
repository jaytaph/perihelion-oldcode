<?php

  // Include Files
  include "../includes.inc.php";

  // Session Identification
  session_identification ("admin");

  print_header ();

  print_title ("Create a new sector");

  echo "<strong>Use this in the px_server option:</strong>";
  echo "<li>tmp     : tmp\n";
  echo "<li>CMD     : NEWSECTOR\n";

  print_footer ();
  exit;

?>