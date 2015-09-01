<?php
  // Include Files
  include "../includes.inc.php";

  // Session Identification
  session_identification ("admin");

  print_header ();
  print_title ("Admin Page", "Here you can control a lot of stuff for Perihelion");

  $cmd = input_check ("choose", 0,
                      "pxserver", 0,
                      "pxserver2", 0,
                      "manualwww", 0,
                      "manualwww2", 0);

  if ($cmd == "pxserver") {
    print_subtitle ("Enter your admin commands directly into the px_server.");
    if (! isset ($_REQUEST['px_cmd'])) $_REQUEST['px_cmd'] = "";
    if (! isset ($_REQUEST['px_k'])) $_REQUEST['px_k'] = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
    if (! isset ($_REQUEST['px_v'])) $_REQUEST['px_v'] = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
    show_px_table ($_REQUEST['px_cmd'], $_REQUEST['px_k'], $_REQUEST['px_v']);
  }
  if ($cmd == "pxserver2") {
    print_subtitle ("Enter your admin commands directly into the px_server.");
    show_px_table ($_REQUEST['px_cmd'], $_REQUEST['px_k'], $_REQUEST['px_v']);
    echo "<hr>\n";
    px_execute ($_REQUEST['px_cmd'], $_REQUEST['px_k'], $_REQUEST['px_v']);
  }


  if ($cmd == "manualwww") {
    print_subtitle ("Jump to encrypted page.");
    if (! isset ($_REQUEST['ww_cmd'])) $_REQUEST['ww_cmd'] = "";
    if (! isset ($_REQUEST['ww_k'])) $_REQUEST['ww_k'] = array("", "", "", "", "", "", "", "");
    if (! isset ($_REQUEST['ww_v'])) $_REQUEST['ww_v'] = array("", "", "", "", "", "", "", "");
    show_www_table ($_REQUEST['ww_cmd'], $_REQUEST['ww_k'], $_REQUEST['ww_v']);
  }
  if ($cmd == "manualwww2") {
    print_subtitle ("Jump to encrypted page.");
    show_www_table ($_REQUEST['ww_cmd'], $_REQUEST['ww_k'], $_REQUEST['ww_v']);
    echo "<hr>\n";
    www_execute ($_REQUEST['ww_cmd'], $_REQUEST['ww_k'], $_REQUEST['ww_v']);
  }


  create_submenu ( array (
                          "PX_Server" => "admin.php?cmd=".encrypt_get_vars("pxserver"),
                          "Manual WWW" => "admin.php?cmd=".encrypt_get_vars("manualwww"),
                          "New Sector" => "createnewsector.php",
                          "New User" => "createnewuser.php",
                          "ScanArea" => "scanarea.php",
                          "Ticks" => "ticks.php",
                          "zoomtables" => "zoomtables.php",
			  "test users" => "createtest.php",
                         )
                 );


  print_footer ();
  exit;







// ================================================================================
function show_px_table ($px_cmd, $px_k, $px_v) {
  form_start ();
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("pxserver2").">\n";

  echo "<table align=center>\n";
  echo "  <tr>";
  echo "<td>Command</td>";
  echo "<td><input type=text size=50 name=px_cmd value='".$px_cmd."'></td>";
  echo "</tr>\n";

  for ($i=0; $i!=16; $i++) {
    echo "  <tr>";
    echo "<td><input type=text size=15 name=px_k[".$i."] value='".$px_k[$i]."'></td>";
    echo "<td><input type=text size=50 name=px_v[".$i."] value='".$px_v[$i]."'></td>";
    echo "</tr>\n";
  }
  echo "  <tr>";
  echo "<td>&nbsp;</td>";
  echo "<td><input type=submit value=Submit name=submit></td>";
  echo "</tr>\n";

  echo "</table>\n";

  form_end ();

  echo "<br><br>\n";
}

// ================================================================================
function px_execute ($px_cmd, $px_k, $px_v) {

  for ($i=0; $i!=16; $i++) {
    $data[$px_k[$i]] = $px_v[$i];
  }
  comm_init_server ();
  comm_s2s ($px_cmd, $data);
  $pkg = comm_recv_from_server ();
  comm_fini_server ();

  echo "<table align=center>\n";
  echo "<tr><th colspan=2>PX_Server Output</th></tr>\n";
  reset ($pkg);
  while (list ($key, $val) = each ($pkg)) {
    echo "<tr class=bl><td>".$key."</td><td>".$val."</td></tr>\n";
  }
  echo "</table>\n";
  echo "<br><br>\n";
}

// ================================================================================
function show_www_table ($ww_cmd, $ww_k, $ww_v) {
  form_start ();
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("manualwww2").">";

  echo "<table align=center>\n";
  echo "  <tr>";
  echo "<td>Page</td>";
  echo "<td><input type=text size=50 name=ww_cmd value='".$ww_cmd."'></td>";
  echo "</tr>\n";

  for ($i=0; $i!=8; $i++) {
    echo "  <tr>";
    echo "<td><input type=text size=15 name=ww_k[".$i."] value='".$ww_k[$i]."'></td>";
    echo "<td><input type=text size=50 name=ww_v[".$i."] value='".$ww_v[$i]."'></td>";
    echo "</tr>\n";
  }
  echo "  <tr>";
  echo "<td>&nbsp;</td>";
  echo "<td><input type=submit value=Submit name=submit></td>";
  echo "</tr>\n";

  echo "</table>\n";

  form_end ();

  echo "<br><br>\n";
}

// ================================================================================
function www_execute ($ww_cmd, $ww_k, $ww_v) {
  $url = $_CONFIG['URL'].$ww_cmd . "?";

  for ($i=0; $i!=8; $i++) {
    if ($ww_k[$i] != "") $url .= $ww_k[$i] . '='.encrypt_get_vars ($ww_v[$i]).'&';
  }

  $url = substr ($url, 0, -1);
  echo "<center>\n";
  echo "  Click on the target below:<br>\n";
  echo "  <a target=_blank href=$url>$url</a>\n";
  echo "</center>\n";
  echo "<br><br>";
}

?>
