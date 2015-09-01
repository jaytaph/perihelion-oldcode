<?php


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
function alliance_showinfo ($alliance_id, $user_id, $extended_info=USER_SHOWINFO_NORMAL) {
  assert (is_numeric ($alliance_id));
  assert (is_numeric ($user_id));
  assert (is_numeric ($extended_info));

  $result   = sql_query ("SELECT * FROM g_alliance WHERE id=$alliance_id");
  $alliance = sql_fetchrow ($result);
  $race     = user_get_race ($alliance['owner_id']);

  $result = sql_query ("SELECT COUNT(*) AS count FROM g_users WHERE alliance_id = ".$alliance['id']);
  $tmp = sql_fetchrow ($result);
  $alliance_size = $tmp['count'];

  $user    = user_get_user ($user_id);

  $result = sql_query ("SELECT COUNT(*) FROM s_sectors, g_users WHERE s_sectors.user_id = g_users.user_id AND g_users.alliance_id = ".$alliance_id);
  $row = sql_fetchrow ($result);
  $sectors_owned = $row['0'];

  $result = sql_query ("SELECT COUNT(*) FROM s_anomalies, g_users WHERE s_anomalies.user_id = g_users.user_id AND g_users.alliance_id = ".$alliance_id);
  $row = sql_fetchrow ($result);
  $planets_owned = $row['0'];


  echo "<table border=0 align=center width=60%>";
  echo "<tr><th class=white colspan=2>".$alliance['name']."</th></tr>";
  echo "<tr><th colspan=2><b>".$alliance['tag']."</b></th></tr>";

  echo "<tr valign=top>";
  echo "  <td width=120>";
  echo "    <img width=100 height=100 src='images/users/".$alliance['avatar']."'><br>";
  echo "  </td>";
  echo "  <td><table border=0 nowrap width=100%>";
  echo "        <tr class=bl><td colspan=3>".$alliance['description']."</td></tr>";
  echo "        <tr><td colspan=3>&nbsp;</td></tr>";

  echo "        <tr class=bl><td>&nbsp;Owner           &nbsp;</td><td>:</td><td>&nbsp;<a href=stats.php?cmd=".encrypt_get_vars("show")."&uid=".encrypt_get_vars($alliance['owner_id']).">".$race." race</a>&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Size            &nbsp;</td><td>:</td><td>&nbsp;".$alliance_size." user(s)</a>&nbsp;</td></tr>";
  echo "        <tr><td colspan=3>&nbsp;</td></tr>";

  echo "        <tr class=bl><td>&nbsp;Sectors Owned     &nbsp;</td><td>:</td><td>&nbsp;".$sectors_owned." sector(s)&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Planets Owned     &nbsp;</td><td>:</td><td>&nbsp;".$planets_owned." planet(s)</td></tr>";

  echo "        <tr class=bl><td colspan=3>\n";

  // Only show the users in the alliance when we are part of that alliance...
  if ($user['alliance_id'] == $alliance_id) {
    $result   = sql_query ("SELECT * FROM g_users WHERE alliance_id=".$alliance['id']);
    while ($tmp_user = sql_fetchrow ($result)) {
      $tmp_race  = user_get_race ($tmp_user['user_id']);
      echo "<a href=stats.php?cmd=".encrypt_get_vars("show")."&uid=".encrypt_get_vars($tmp_user['user_id']).">".$tmp_race." race</a>, \n";
    }
    echo "        </td></tr>\n";

    // We can only part an alliance if we're not the owner of it, if we are owner, we can disband the alliance (?)
    if ($user['user_id'] == $alliance['owner_id']) {
      echo "<tr><th colspan=3>";
      echo "[ Disband this alliance ] ";
      echo "</th></tr>";
    } else {
      echo "<tr><th colspan=3>";
      echo "[ <a href=alliance.php?aid=".encrypt_get_vars(0 - $alliance_id)."&uid=".encrypt_get_vars($user['user_id']).">Part this alliance</a>  ] ";
      echo "</th></tr>";
    }
  }

  // We're not part of an alliance.. We can join if we want...
  if ($user['alliance_id'] == 0) {

    $result2 = sql_query ("SELECT * FROM g_alliance_pending WHERE user_id=".$user['user_id']);
    $pending = sql_fetchrow ($result2);

    // If we already are pending for joining this alliance, we cannot click it anymore..
    if ($pending['alliance_id'] == $alliance_id) {
      echo "<tr><th colspan=3>";
      echo "[ Request pending ] ";
      echo "</th></tr>";
    } else {
      echo "<tr><th colspan=3>";
      echo "[ <a href=alliance.php?aid=".encrypt_get_vars($alliance_id)."&uid=".encrypt_get_vars($user['user_id']).">Join this alliance</a>  ] ";
      echo "</th></tr>";
    }
  }

  echo "  </table></td>";
  echo "</tr>";
  echo "</table>";
  echo "<br><br>";
}


?>