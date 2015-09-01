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
        "  TH.white  { background-color: white; color: black} " .
        "</STYLE>";
  print_header ($extra_headers);
  print_title ("User information");

  $cmd = input_check ("show", "uid", 0,
                      "showdetail", "uid", 0,
                      "relation", "!frmid", "!uid", "!wid", 0);

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    show_users ($uid);
  }
  if ($cmd == "showdetail") {
    if ($uid == "") $uid = user_ourself();
    user_showinfo ($uid, USER_SHOWINFO_NORMAL);
  }
  if ($cmd == "relation") {
    set_relation (user_ourself(), $uid, $wid);
    show_users (user_ourself());
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
function set_relation ($src_user_id, $dst_user_id, $relation) {
  assert (is_numeric ($serc_user_id));
  assert (is_numeric ($dst_user_id));
  assert (is_numeric ($relation));

  $errors['PARAMS'] = "No decent params given.";
  $data['uid']     = $src_user_id;
  $data['dst_uid'] = $dst_user_id;
  $data['wid']     = $relation;
  comm_send_to_server ("RELATION", $data, "", $errors);
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
function show_users ($user_id) {
  global $_GALAXY;

  echo "<table border=0 align=center width=60%>\n";

  // Show friends
  echo "<tr><th class=white colspan=2>Friendly Races</th></tr>\n";
  $result = sql_query ("SELECT gu.* FROM g_users AS gu, g_knownspecies AS gks WHERE FIND_IN_SET( gu.user_id, gks.csl_friend_id ) and gks.user_id=".$user_id);
  while ($user = sql_fetchrow ($result)) {
    echo "<tr class=bl><td><a href=user.php?cmd=".encrypt_get_vars ("showdetail")."&uid=".encrypt_get_vars ($user['user_id']).">".$user['race']." Race</a></td><td>&nbsp;</td></tr>\n";
  }
  echo "<tr><td colspan=2>&nbsp;</td></tr>\n";

  // Show neutral
  echo "<tr><th class=white colspan=2>Neutral Races</th></tr>\n";
  $result = sql_query ("SELECT gu.* FROM g_users AS gu, g_knownspecies AS gks WHERE FIND_IN_SET( gu.user_id, gks.csl_neutral_id ) and gks.user_id=".$user_id);
  while ($user = sql_fetchrow ($result)) {
    echo "<tr class=bl><td><a href=user.php?cmd=".encrypt_get_vars ("showdetail")."&uid=".encrypt_get_vars ($user['user_id']).">".$user['race']." Race</a></td><td>&nbsp;</td></tr>\n";
  }
  echo "<tr><td colspan=2>&nbsp;</td></tr>\n";

  // Show enemies
  echo "<tr><th class=white colspan=2>Enemy Races</th></tr>\n";
  $result = sql_query ("SELECT gu.* FROM g_users AS gu, g_knownspecies AS gks WHERE FIND_IN_SET( gu.user_id, gks.csl_enemy_id ) and gks.user_id=".$user_id);
  while ($user = sql_fetchrow ($result)) {
    echo "<tr class=bl><td><a href=user.php?cmd=".encrypt_get_vars ("showdetail")."&uid=".encrypt_get_vars ($user['user_id']).">".$user['race']." Race</a></td><td>&nbsp;</td></tr>\n";
  }
  echo "<tr><td colspan=2>&nbsp;</td></tr>\n";

  echo "</table>";
}


?>