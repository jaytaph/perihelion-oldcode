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
// Returns the ID of the user who is calling this function. Eg: the user who is currently
// logged in.
function user_ourself () {
  global $_USER;
  return $_USER['id'];
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
function user_showinfo ($user_id, $extended_info=USER_SHOWINFO_NORMAL) {
  assert (is_numeric ($user_id));

  $user    = user_get_user ($user_id);
  $px_user   = user_get_perihelion_user ($user_id);

  $result  = sql_query ("SELECT * FROM perihelion.u_access WHERE user_id=".$user_id." ORDER BY login DESC LIMIT 1");
  $access  = sql_fetchrow ($result);
  sql_query ("USE ".$px_user['galaxy_db']);

  $result = sql_query ("SELECT SUM(population) FROM s_anomalies WHERE user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $people = $row['0'];

  $result = sql_query ("SELECT COUNT(*) FROM s_sectors WHERE user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $sectors_owned = $row['0'];

  $result = sql_query ("SELECT COUNT(*) FROM s_anomalies WHERE user_id=".$user_id);
  $row = sql_fetchrow ($result);
  $planets_owned = $row['0'];

  $result = sql_query ("SELECT DISTINCT sector_id, COUNT(*) FROM s_anomalies WHERE user_id=".$user_id." GROUP BY sector_id");
  $sector_count = sql_countrows ($result);

  $sector = sector_get_sector (user_get_home_sector ($user_id));
  $planet = anomaly_get_anomaly (user_get_home_planet ($user_id));
  $race   = user_get_race ($user_id);

  $result = sql_query ("SELECT * FROM g_alliance WHERE id=".$user['alliance_id']);
  $alliance = sql_fetchrow ($result);

  if ($user['alliance_id'] != 0) {
    $result = sql_query ("SELECT COUNT(*) AS count FROM g_users WHERE alliance_id = ".$alliance['id']);
    $tmp = sql_fetchrow ($result);
    $alliance_size = $tmp['count'];
  }

  $status = "Unknown";
  if (user_is_friend (user_ourself(), $user_id)) $status = "Single side friend";
  if (user_is_mutual_friend (user_ourself(), $user_id)) $status = "Mutual friend";
  if (user_is_neutral (user_ourself(), $user_id)) $status = "Neutral";
  if (user_is_enemy (user_ourself(), $user_id)) $status = "Enemy";
  if (user_ourself() == $user_id) $status = "";

  echo "<table border=0 align=center width=60%>";
  echo "<tr><th class=white colspan=2>".$px_user['name']."</th><th>&nbsp;</th></tr>";
  echo "<tr><th colspan=2><b>".$px_user['tag']."</b></th></tr>";

  echo "<tr valign=top>";
  echo "  <td width=120>";
  echo "    <img width=100 height=100 src='images/users/".$px_user['avatar']."'><br>";
  echo "    <br>";
  if ($user['alliance_id'] != 0) {
    echo "    <img width=100 height=100 src='images/users/".$alliance['avatar']."'><br>";
  }
  echo "  </td>";
  echo "  <td><table border=0 nowrap>";
  echo "        <tr class=bl><td>&nbsp;Full Name       &nbsp;</td><td>:</td><td>&nbsp;".$px_user['name']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;User ID         &nbsp;</td><td>:</td><td>&nbsp;".$px_user['id']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;City            &nbsp;</td><td>:</td><td>&nbsp;".$px_user['city']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Country         &nbsp;</td><td>:</td><td>&nbsp;".$px_user['country']."&nbsp;</td></tr>";


  echo "        <tr><td colspan=3>&nbsp;</td></tr>";
  if ($status != "") {
    echo "        <tr class=bl><td>&nbsp;Diplomatic Status &nbsp;</td><td>:</td><td>&nbsp;".$status."&nbsp;</td></tr>";
  }
  echo "        <tr class=bl><td>&nbsp;Race              &nbsp;</td><td>:</td><td>&nbsp;".$race."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Home Planet       &nbsp;</td><td>:</td><td>&nbsp;".$planet['name']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Home Sector       &nbsp;</td><td>:</td><td>&nbsp;".$sector['name']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Population        &nbsp;</td><td>:</td><td>&nbsp;".$people."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Sectors Owned     &nbsp;</td><td>:</td><td>&nbsp;".$sectors_owned."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Planets Owned     &nbsp;</td><td>:</td><td>&nbsp;".$planets_owned." planet(s) in ".$sector_count." sector(s)&nbsp;</td></tr>";

  if ($user['alliance_id'] != 0) {
    echo "        <tr class=bl><td>&nbsp;Alliance        &nbsp;</td><td>:</td><td>&nbsp;<a href=alliance.php?cmd=".encrypt_get_vars ("show")."&aid=".encrypt_get_vars ($alliance['id']).">".$alliance['name']."</a>&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;Alliance Size   &nbsp;</td><td>:</td><td>&nbsp;".$alliance_size." user(s)&nbsp;</td></tr>";
  }

  echo "        <tr><td colspan=3>&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Times logged in   &nbsp;</td><td>:</td><td>&nbsp;".$px_user['login_count']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Last login        &nbsp;</td><td>:</td><td>&nbsp;".$access['login']."&nbsp;</td></tr>";
  echo "        <tr class=bl><td>&nbsp;Last action       &nbsp;</td><td>:</td><td>&nbsp;".$access['logout']."&nbsp;</td></tr>";

  if ($extended_info == USER_SHOWINFO_EXTENDED) {
    if ($px_user['gender']=="M") $gender="Male"; else $gender="Female";
    echo "        <tr><td colspan=3>&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;Email      &nbsp;</td><td>:</td><td>&nbsp;".$px_user['email']."&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;Login Name &nbsp;</td><td>:</td><td>&nbsp;".$px_user['login_name']."&nbsp;</td></tr>";
    echo "        <tr><td colspan=3>&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;City       &nbsp;</td><td>:</td><td>&nbsp;".$px_user['city']."&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;Country    &nbsp;</td><td>:</td><td>&nbsp;".$px_user['country']."&nbsp;</td></tr>";
    echo "        <tr><td colspan=3>&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;Birthday   &nbsp;</td><td>:</td><td>&nbsp;".$px_user['birthday']."&nbsp;</td></tr>";
    echo "        <tr class=bl><td>&nbsp;Gender     &nbsp;</td><td>:</td><td>&nbsp;".$gender."&nbsp;</td></tr>";
  }


  if ($user_id != user_ourself() ) {
    echo "<tr><th colspan=3>";
    echo "[ <a href=message.php?uid=".encrypt_get_vars($user_id).">Send Message</a> ]";
    echo "</th></tr>";

    echo "<tr><th colspan=3>";
    echo "[ <a href=user.php?cmd=".encrypt_get_vars("relation")."&uid=".encrypt_get_vars($user_id)."&wid=".encrypt_get_vars(RELATION_FRIEND).">Set as friend</a>  ] - ";
    echo "[ <a href=user.php?cmd=".encrypt_get_vars("relation")."&uid=".encrypt_get_vars($user_id)."&wid=".encrypt_get_vars(RELATION_NEUTRAL).">Set as neutral</a>  ] - ";
    echo "[ <a href=user.php?cmd=".encrypt_get_vars("relation")."&uid=".encrypt_get_vars($user_id)."&wid=".encrypt_get_vars(RELATION_ENEMY).">Set as enemy</a> ]";
    echo "</th></tr>";

  }

  echo "  </table></td>";
  echo "</tr>";
  echo "</table>";
  echo "<br><br>";
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
function user_is_mutual_friend ($user1_id, $user2_id) {
  assert (is_numeric ($user2_id));
  assert (is_numeric ($user1_id));

  // Worse case scenario
  $we_are_friendly = false;
  $they_are_friendly = false;

  // If it's our own planet, we are friendly.. of course...
  if ($user1_id == $user2_id) {
    $we_are_friendly = true;
    $they_are_friendly = true;
    return true;
  }

  // Check if the planet owner is located in our friend list
  $known_users = user_get_knownspecies ($user1_id);
  $friend_ids = csl ($known_users['csl_friend_id']);
  if (in_array ($user2_id, $friend_ids)) $we_are_friendly = true;

  // Now, check if the planet owner has us in the friend list
  $known_users = user_get_knownspecies ($user1_id);
  $friend_ids = csl ($known_users['csl_friend_id']);
  if (in_array ($user1_id, $friend_ids)) $they_are_friendly = true;

  if ($we_are_friendly and $they_are_friendly) return true;
  return false;
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
// is_friend, is_neutral, is_enemy use a caching array with a row out of g_knownspecies.
// this is because we use these functions a lot sometimes, and we don't want to query
// everytime we already get the $uid. This would happen, cause we are looking each time for
// a user_id and a destination_user_id. This format is not good for our standard query
// chaching function.
$current_ffn = 0;
function load_current_ffn ($uid) {
  assert (is_numeric ($uid));

  global $current_ffn;
  $current_ffn = array();

  $tmp = user_get_knownspecies ($uid);
  $current_ffn['uid'] = $uid;
  $current_ffn['csl_neutral_id'] = csl ($tmp['csl_neutral_id']);
  $current_ffn['csl_enemy_id']   = csl ($tmp['csl_enemy_id']);
  $current_ffn['csl_friend_id']  = csl ($tmp['csl_friend_id']);
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
function user_is_friend ($uid, $dst_uid) {
  assert (is_numeric ($uid));
  assert (is_numeric ($dst_uid));

  global $current_ffn;
  if ($current_ffn == 0 or $current_ffn['uid'] != $uid) { load_current_ffn ($uid); }

  if (in_array ($dst_uid, $current_ffn['csl_friend_id'])) return true;
  return false;
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
function user_is_enemy ($uid, $dst_uid) {
  assert (is_numeric ($uid));
  assert (is_numeric ($dst_uid));

  global $current_ffn;
  if ($current_ffn == 0 or $current_ffn['uid'] != $uid) { load_current_ffn ($uid); }

  if (in_array ($dst_uid, $current_ffn['csl_enemy_id'])) return true;
  return false;
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
function user_is_neutral ($uid, $dst_uid) {
  assert (is_numeric ($uid));
  assert (is_numeric ($dst_uid));

  global $current_ffn;
  if ($current_ffn == 0 or $current_ffn['uid'] != $uid) { load_current_ffn ($uid); }

  if (in_array ($dst_uid, $current_ffn['csl_neutral_id'])) return true;
  return false;
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
function user_is_in_aliance ($uid, $dst_uid) {
  assert (is_numeric ($uid));
  assert (is_numeric ($dst_uid));


  if ($dst_uid == 0) return false;

  $user1 = user_get_user ($uid);
  $user2 = user_get_user ($dst_uid);

  if ($user1['alliance_id'] == 0 or $user2['alliance_id'] == 0) return false;
  if ($user1['alliance_id'] == $user2['alliance_id']) return true;
  return false;
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
$cache_ugpu = 0;
function user_get_perihelion_user ($user_id) {
  assert (is_numeric ($user_id));
  global $cache_ugpu;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_ugpu == 0 or $user_id != $cache_ugpu['id']) {
    $result = sql_query ("SELECT * FROM perihelion.u_users WHERE id=".$user_id);
    $tmp    = sql_fetchrow ($result);
    $cache_ugpu = array();
    $cache_ugpu['id'] = $user_id;
    $cache_ugpu['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_ugpu['query'];
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
$cache_ugu = 0;
function user_get_user ($user_id, $caching = USECACHE) {
  assert (is_numeric ($user_id));
  global $cache_ugu;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_ugu == 0 or $user_id != $cache_ugu['id'] or $caching == NOCACHE) {
    $result = sql_query ("SELECT * FROM g_users WHERE user_id=".$user_id);
    $tmp    = sql_fetchrow ($result);
    $cache_ugu = array();
    $cache_ugu['id'] = $user_id;
    $cache_ugu['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_ugu['query'];
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
$cache_ugk = 0;
function user_get_knownspecies ($user_id) {
  assert (is_numeric ($user_id));
  global $cache_ugk;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_ugk == 0 or $user_id != $cache_ugk['id']) {
    $result = sql_query ("SELECT * FROM g_knownspecies WHERE user_id=".$user_id);
    $tmp = sql_fetchrow ($result);

    $cache_ugk = array();
    $cache_ugk['id'] = $user_id;
    $cache_ugk['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_ugk['query'];
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
$cache_ugapfu = 0;
function user_get_all_anomalies_from_user ($user_id) {
  assert (is_numeric ($user_id));
  global $cache_ugapfu;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_ugapfu == 0 or $user_id != $cache_ugapfu['id']) {
    $result = sql_query ("SELECT * FROM g_anomalies WHERE user_id=".$user_id);
    $tmp    = sql_fetchrow ($result);

    $cache_ugapfu = array();
    $cache_ugapfu['id'] = $user_id;
    $cache_ugapfu['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_ugapfu['query'];
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
function user_get_home_planet ($user_id ) {
  assert (is_numeric ($user_id));
  $user = user_get_user ($user_id);

  return $user['home_planet_id'];
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
function user_get_home_sector ($user_id ) {
  assert (is_numeric ($user_id));
  $user = user_get_user ($user_id);

  return $user['home_sector_id'];
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
function user_get_race ($user_id ) {
  assert (is_numeric ($user_id));
  $user = user_get_user ($user_id);
  return $user['race'];
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
function user_get_fullname ($user_id) {
  assert (is_numeric ($user_id));

  $tmp = user_get_perihelion_user ($user_id);

  return $tmp['name'];
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
function user_has_flag ($user_id, $flag) {
  assert (is_numeric ($user_id));
  assert (is_string ($flag));  

  $user = user_get_perihelion_user ($user_id);

  $userflags = split (",", $user['flags']);
  if (in_array ($flag, $userflags)) return true;
  return false;
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
function user_is_admin ($user_id) { 
  assert (is_numeric ($user_id));
  return user_has_flag ($user_id, USERFLAG_ADMIN);
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
function user_is_invisible ($user_id) { 
  assert (is_numeric ($user_id));
  return user_has_flag ($user_id, USERFLAG_INVISIBLE);
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
function user_is_logged_in () {
	return isset ($_SESSION['logged_in']);  
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
function user_get_weakest_ship ($user_id) {
  assert (is_numeric ($user_id));

  $vessel_array = get_vessel_array ($user_id);
  $ws = $vessel_array[count ($vessel_array)-2]['id'];
  $avg = round (($vessel_array[count ($vessel_array)-2]['score']+1) / $vessel_array['avg']['score'] * 100, 2);

  return array ($ws, $avg);
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
function user_get_strongest_ship ($user_id) {
  assert (is_numeric ($user_id));

  $vessel_array = get_vessel_array ($user_id);
  $ss = $vessel_array[0]['id'];
  $avg = round (($vessel_array[1]['score']+1) / $vessel_array['avg']['score'] * 100, 2);

  return array ($ss, $avg);
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
function user_get_weakest_planet ($user_id) {
  assert (is_numeric ($user_id));

  $anomaly_array = get_anomaly_array ($user_id);
  $wp = $anomaly_array[count ($anomaly_array)-2]['id'];
  $avg = round (($anomaly_array[count ($anomaly_array)-2]['score']+1) / $anomaly_array['avg']['score'] * 100, 2);

  return array ($wp, $avg);
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
function user_get_strongest_planet ($user_id) {
  assert (is_numeric ($user_id));

  $anomaly_array = get_anomaly_array ($user_id);
  $sp = $anomaly_array[0]['id'];
  $avg = round (($anomaly_array[0]['score']+1) / $anomaly_array['avg']['score'] * 100, 2);

  return array ($sp, $avg);
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
$gaa_cached_uid = 0;
function get_anomaly_array ($user_id) {
  assert (is_numeric ($user_id));

  global $gaa_cached_uid;

  if ($gaa_cached_uid != 0 and $gaa_cached_uid['uid'] == $user_id) return $gaa_cached_uid['aa'];


  $i = 0;

  $result = sql_query ("SELECT * FROM s_anomalies WHERE user_id=".$user_id);
  while ($anomaly = sql_fetchrow ($result)) {
    $i++;
    $anomaly_array[$i]['id'] = $anomaly['id'];
    $anomaly_array[$i]['ca'] = $anomaly['cur_attack'];
    $anomaly_array[$i]['cd'] = $anomaly['cur_defense'];
    $anomaly_array[$i]['cs'] = $anomaly['cur_strength'];
    $anomaly_array[$i]['score'] = ($anomaly['cur_strength'] * 3) +
                                  ($anomaly['cur_attack']   * 2) +
                                  ($anomaly['cur_defense']  * 1);
  }


  // Get the highest score on index 1, lowest on -1
  global $user_sort_cmd_idx;
  $user_sort_cmd_idx = 'score';
  usort ($anomaly_array, "user_sort_cmp");


  // Calculate average
  $anomaly_array['avg']['id'] = 'avg';
  $anomaly_array['avg']['score'] = 0;
  $anomaly_array['avg']['ca'] = 0;
  $anomaly_array['avg']['cd'] = 0;
  $anomaly_array['avg']['cs'] = 0;

  for ($j=1; $j!=$i; $j++) {
    $anomaly_array['avg']['ca'] += $anomaly_array[$j]['ca'];
    $anomaly_array['avg']['cd'] += $anomaly_array[$j]['cd'];
    $anomaly_array['avg']['cs'] += $anomaly_array[$j]['cs'];
    $anomaly_array['avg']['score'] += $anomaly_array[$j]['score'];
  }
  $anomaly_array['avg']['ca'] /= $i;
  $anomaly_array['avg']['cd'] /= $i;
  $anomaly_array['avg']['cs'] /= $i;
  $anomaly_array['avg']['score'] /= $i;

  // Cache it before returning
  $gaa_cached_uid = array ();
  $gaa_cached_uid['uid'] = $user_id;
  $gaa_cached_uid['aa'] = $anomaly_array;

  return $anomaly_array;
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
$user_sort_cmd_idx = 'strength';
function user_sort_cmp ($a, $b) {
  global $user_sort_cmd_idx;
  if ($a[$user_sort_cmd_idx] > $b[$user_sort_cmd_idx]) return -1;
  if ($a[$user_sort_cmd_idx] < $b[$user_sort_cmd_idx]) return 1;
  return 0;
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
$gva_cached_uid = 0;
function get_vessel_array ($user_id) {
  assert (is_numeric ($user_id));

  global $gva_cached_uid;

  if ($gva_cached_uid != 0 and $gva_cached_uid['uid'] == $user_id) return $gva_cached_uid['va'];


  $i = 0;

  $result = sql_query ("SELECT * FROM g_vessels WHERE user_id=".$user_id);
  while ($vessel = sql_fetchrow ($result)) {
    $i++;
    $vessel_array[$i]['id'] = $vessel['id'];
    $vessel_array[$i]['ca'] = $vessel['cur_attack'];
    $vessel_array[$i]['cd'] = $vessel['cur_defense'];
    $vessel_array[$i]['cs'] = $vessel['cur_strength'];
    $vessel_array[$i]['score'] = ($vessel['cur_strength'] * 3) +
                                  ($vessel['cur_attack']   * 2) +
                                  ($vessel['cur_defense']  * 1);
  }


  // Get the highest score on index 1, lowest on -1
  global $user_sort_cmd_idx;
  $user_sort_cmd_idx = 'score';
  usort ($vessel_array, "user_sort_cmp");


  // Calculate average
  $vessel_array['avg']['id'] = 'avg';
  $vessel_array['avg']['score'] = 0;
  $vessel_array['avg']['ca'] = 0;
  $vessel_array['avg']['cd'] = 0;
  $vessel_array['avg']['cs'] = 0;

  for ($j=1; $j!=$i; $j++) {
    $vessel_array['avg']['ca'] += $vessel_array[$j]['ca'];
    $vessel_array['avg']['cd'] += $vessel_array[$j]['cd'];
    $vessel_array['avg']['cs'] += $vessel_array[$j]['cs'];
    $vessel_array['avg']['score'] += $vessel_array[$j]['score'];
  }
  $vessel_array['avg']['ca'] /= $i;
  $vessel_array['avg']['cd'] /= $i;
  $vessel_array['avg']['cs'] /= $i;
  $vessel_array['avg']['score'] /= $i;

  // Cache it before returning
  $gva_cached_uid = array ();
  $gva_cached_uid['uid'] = $user_id;
  $gva_cached_uid['va'] = $vessel_array;

  return $vessel_array;
}

?>
