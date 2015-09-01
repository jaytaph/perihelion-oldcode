<?php
    // Output Buffering
    ob_start ();

    // Include Files
    include "includes.inc.php";

    // Make sure we don't use cached session-frames
    header ("Cache-Control: no-cache, must-revalidate");

    session_identification ();

    $template = new Smarty ();

    if (user_is_logged_in()) {
      $template->assign ("title", "Perihelion - User: ".$_USER['login_name']." - dB: ".$_USER['galaxy_db']);
    } else {
      $template->assign ("title", "Perihelion - The Game");
    }
    $template->assign ("mainurl", "info.php?cmd=".encrypt_get_vars ("show"));

    $template->display ($_RUN['theme_path']."/index.tpl");

    ob_end_flush ();
    exit;
?>
