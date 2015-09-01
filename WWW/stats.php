<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  // Extra headers for TD..
  $extra_headers =
      "<STYLE TYPE=\"text/css\" >      " .
      "  TH.red    { background-color: red }    " .
      "  TH.orange { background-color: orange } " .
      "  TH.white { background-color: white; color: black} " .
      "</STYLE>";
  print_header ($extra_headers);
  print_title ("Statistics for ".$_USER['name']);

  $cmd = input_check ("show", "uid", 0);

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();

    user_showinfo ($uid, USER_SHOWINFO_EXTENDED);
    score_showuser ($uid);
  }

  print_footer ();
  exit;
?>
