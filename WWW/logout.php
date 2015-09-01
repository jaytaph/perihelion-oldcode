<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification();

    comm_init_server ();
    $data['id']=user_ourself();
    $data['sess_id']=session_id();
    comm_s2s ("LOGOUT", $data);
    comm_fini_server ();

    session_destroy ();
    header ("Location: login.php");
    exit;
?>
