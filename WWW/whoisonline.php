<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Who is online?",
               "This page shows you all the users who last used the system. Note that not all users are shown since some have set their properties to invisible.");
  print_users ();
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
function print_users () {
	global $_CONFIG;
	global $_RUN;

  // Hmm, this should be a decent mysql query which filters out the last login_time and login_date per user_id actually... :(
  $user = array ();
  $result = sql_query ("SELECT * FROM perihelion.u_access ORDER BY logout DESC");
  while ($row = sql_fetchrow ($result)) {
    if (! array_key_exists ($row['user_id'], $user)) {
      $user[$row['user_id']]['user_id'] = $row['user_id'];
      $user[$row['user_id']]['login'] = $row['login'];
      $user[$row['user_id']]['logout'] = $row['logout'];
    } else {
      if ($user[$row['user_id']]['logout'] < $row['logout']) {
        $user[$row['user_id']]['login'] = $row['login'];
        $user[$row['user_id']]['logout'] = $row['logout'];
      }
    }
  }

  foreach ($user as $row) {
    $account = user_get_perihelion_user ($row['user_id']);
    list ($idle, $timestamp) = create_idle_time ($row['logout']);

    // Don't show invisible users..
    if (user_is_invisible ($row['user_id'])) continue;

    if ($timestamp < $_CONFIG['MAX_SECONDS_IDLE']) {
      $tmpvar[] = array ('href' => "user.php?cmd=".encrypt_get_vars ("showdetail")."&uid=".encrypt_get_vars ($row['user_id']),
                         'user' => $account['name'],
                         'idle' => $idle);
    }
  }

  // Output it...
  $template = new Smarty();
  $template->debugging = true;
  $template->assign ("onlineusers", $tmpvar);
  $template->display ($_RUN['theme_path']."/whoisonline.tpl");
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
function create_idle_time ($time) {
	$timestamp = strtotime ($time);
  $str = calculate_uptime (mktime() - $timestamp);
  return array ($str." ago", mktime() - $timestamp);
}

?>