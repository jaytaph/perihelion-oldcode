<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Anomaly view");

  $cmd = input_check ("show", "aid", "uid", 0,                                   // Show planet
                      "claim", "!frmid", "!aid", "uid", "!ne_name", 0,           // Claim planet for the user
                      "description", "!aid", 0,                                  // Change description
                      "description2", "!frmid", "!aid", "!ne_description", 0);   // Change description (post to server)

  if ($cmd == "description") {
    edit_description ($aid);
  }

  if ($cmd == "description2") {
    $ok = "";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['anomaly_id']  = $aid;
    $data['description'] = convert_crlf_to_px_tags ($ne_description);
    comm_send_to_server ("ANOMALYDESC", $data, $ok, $errors);
    show_anomaly ($aid);
  }

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    if ($aid == "") {
      show_all_user_anomalies ($uid);
    } else {
      show_anomaly ($aid);
    }
  }

  if ($cmd == "claim") {
    if ($uid == "") $uid = user_ourself();
    $ok = "";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $errors['NAME']   = "The anomaly name already exists.\n";
    $data['anomaly_id'] = $aid;
    $data['user_id']    = $uid;
    $data['name']       = convert_crlf_to_px_tag ($ne_name);
    comm_send_to_server ("ANOMALYNAME", $data, $ok, $errors);
    show_anomaly ($aid);
  }

  print_footer ();
  exit;



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
function edit_description ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);

  print_subtitle ("Edit description for ".$anomaly['name']);

  echo "  <center>\n";
  form_start ();
  echo "  <input type=hidden name=aid value=".encrypt_get_vars ($anomaly_id).">\n";
  echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("description2").">\n";
  echo "  <textarea name=ne_description maxlength=255 rows=10 cols=80>";
  echo px2html4edit ($anomaly['description']);
  echo "</textarea>\n";
  echo "  <input name=submit type=submit value=\"Change Description\">\n";
  form_end ();
  echo "  </center>\n";


  create_submenu ( array (
                     "Back to planet view" => "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly_id),
                   )
                 );
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
function show_all_user_anomalies ($user_id) {
  assert (is_numeric ($user_id));

  $result = sql_query ("SELECT * FROM s_anomalies WHERE user_id=".$user_id);
  while ( $anomaly = sql_fetchrow ($result)) {
    show_anomaly ($anomaly['id']);
  }
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
function show_anomaly ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  if (anomaly_is_starbase ($anomaly_id))  starbase_show_starbase ($anomaly_id);
  if (anomaly_is_planet ($anomaly_id))    planet_show_planet ($anomaly_id);
  if (anomaly_is_wormhole ($anomaly_id))  wormhole_show_wormhole ($anomaly_id);
  if (anomaly_is_blackhole ($anomaly_id)) blackhole_show_blackhole ($anomaly_id);
  if (anomaly_is_nebula ($anomaly_id))    nebula_show_nebula ($anomaly_id);
}










?>
