<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Security Breach Detected");

    echo "<br>\n";
    echo "<center>You have no rights to enter this page...</center><br>\n";
    echo "<br><br>\n";
    echo "<center>\n";
    echo "  <img src=images/norights/nono003.gif><br>\n";
    echo "  <b>You are now cursed by DaemonJay <sup>tm</sup>.</b><br>\n";
    echo "</center>\n";

    print_footer ();
    exit;
?>
