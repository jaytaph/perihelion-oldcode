<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Message Office",
               "In the office you can read and send message from your empirium, your alliance and even intercept galaxy messages. The higher your exploration level is, the more messages you can intercept from inside the galaxy. Always beware that messages from and to other people can be intercepted so beware on what you write.");


  $cmd = input_check ("post", "!frmid", "target", "src_uid", "ne_msg", "ne_subject", "ne_priority", "ne_level", "aid", "dst_uid", 0,
                      "delete", "!frmid", "!mid", "!bid", "uid", 0,
                      "creategalaxy", "gid", 0,
                      "createuser", "!frmid", "uid", 0,
                      "createalliance", "aid", 0,
                      "show", "msgbox", "uid", 0);

  if ($cmd == "delete") {
    $data['mid'] = $mid;
    comm_send_to_server ("MESSAGEDEL", $data, "", "");
    if ($uid == "") $uid = user_ourself();
    message_show_all ($uid, $bid);
  }
  if ($cmd == "post") {
    $ok = "Message send succesfully.";
    $errors['PARAMS'] = "No decent params given.";
    $errors['SUBJECT'] = "Please enter a subject.";
    $errors['LEVEL'] = "Please enter a minimum level between 0 and 99999";
    $errors['MSG'] = "Cannot send an empty message.";
    $data['target']  = $target;
    $data['src_uid'] = $src_uid;
    $data['dst_uid'] = $dst_uid;
    $data['prio']    = $ne_priority;
    $data['level']   = $ne_level;
    $data['msg']     = convert_crlf_to_px_tags ($ne_msg);
    $data['subject'] = $ne_subject;
    comm_send_to_server ("MESSAGECREATE", $data, $ok, $errors);
  }
  if ($cmd == "createuser") {
    message_create ($dst_uid);
  }
  if ($cmd == "creategalaxy") {
    message_create_galaxy (user_ourself());
  }
  if ($cmd == "createalliance") {
    message_create_alliance (user_ourself());
  }

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();

    if ($msgbox == "") {
      message_show_menu ($uid);
    } elseif ($msgbox == 'Z') {
      message_show_galaxy ($uid);
    } elseif ($msgbox == 'Y') {
      message_show_alliance ($uid);
    } else {
      message_show_all ($uid, $msgbox);
    }
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
function message_show_all ($user_id, $msgbox) {
  assert (is_numeric ($user_id));
  assert (is_string ($msgbox));

  // Display all messages
  $found_messages = 0;
  $result = sql_query ("SELECT * FROM m_messages WHERE deleted=0 AND type='".$msgbox."' AND user_id=".$user_id." ORDER BY DATETIME DESC");
  while ($message = sql_fetchrow ($result)) {
    message_view ($msgbox, $message);
    $found_messages = 1;
  }

  // If there were no messages, display something else..
  if ($found_messages == 0) {
    print_line ("<center>Your messagebox is empty...</center><br>");
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
function message_show_menu ($user_id) {  
  assert (is_numeric ($user_id));
  global $_CONFIG;
  global $_RUN;

  $template = new Smarty();
  $template->debugging = true;


  foreach ( array('g' => 'global', 
                  'u' => 'alien', 
                  'p' => 'planet', 
                  'e' => 'exploration', 
                  'i' => 'invention', 
                  'v' => 'fleet') as $key => $value) {
    $result = sql_query ("SELECT COUNT(*) AS count FROM m_messages WHERE type='".strtoupper($key)."' AND priority=1 AND deleted=0 AND user_id=".$user_id);
    $tmp = sql_fetchrow ($result);
    $tmpvar['low'] = $tmp['count'];
    $result = sql_query ("SELECT COUNT(*) AS count FROM m_messages WHERE type='".strtoupper($key)."' AND priority=2 AND deleted=0 AND user_id=".$user_id);
    $tmp = sql_fetchrow ($result);
    $tmpvar['high'] = $tmp['count'];

    $result = sql_query ("SELECT * FROM m_messages WHERE deleted=0 AND type='".strtoupper($key)."' AND user_id=".$user_id." ORDER BY DATETIME DESC LIMIT 1");
    $tmp = sql_fetchrow ($result);
    if (! isset ($tmp['msg_subject'])) {
      $tmp['msg_subject'] = "";
    }
    $tmpvar['lasttopic'] = $tmp['msg_subject'];
    $tmpvar['href'] = "message.php?cmd=".encrypt_get_vars ("show")."&msgbox=".encrypt_get_vars ($key);
    $template->assign ($value, $tmpvar);
  }
  
  $result = sql_query ("SELECT * FROM g_flags WHERE user_id=".$user_id);
  $flags  = sql_fetchrow ($result);
  if ($flags['can_warp'] == 1) {  	
    $user    = user_get_user ($user_id);
    $result = sql_query ("SELECT COUNT(*) AS count FROM m_galaxy WHERE deleted=0 AND level <= ".$user['explore_level']);
    $msg    = sql_fetchrow ($result);

    $tmpvar = array();
    $tmpvar['href'] = "message.php?cmd=".encrypt_get_vars ("show")."&msgbox=".encrypt_get_vars ('Z');
    $tmpvar['count'] = $msg['count'];
    $tmpvar['hrefsend'] = "message.php?cmd=".encrypt_get_vars ("creategalaxy");
    $template->assign ("galaxy", $tmpvar);
    $template->assign ("show_galaxy", "1");  
  } else {
  	$template->assign ("show_galaxy", "0");  
  }

  $user    = user_get_user ($user_id);
  if ($user['alliance_id'] == 1) {
    $result = sql_query ("SELECT COUNT(*) AS count FROM m_alliance WHERE deleted=0 AND alliance_id = ".$user['alliance_id']);
    $msg    = sql_fetchrow ($result);

  	$tmpvar = array();
    $tmpvar['href'] = "message.php?cmd=".encrypt_get_vars ("show")."&msgbox=".encrypt_get_vars ('Y');
    $tmpvar['count'] = $msg['count'];
    $tmpvar['hrefsend'] = "message.php?cmd=".encrypt_get_vars ("createalliance");
		$template->assign ("alliance", $tmpvar);
  	$template->assign ("show_alliance", "1");
  } else {
    $template->assign ("show_alliance", "0");
  }
  
  $template->display ($_RUN['theme_path']."/messages-main.tpl");
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
function message_view ($msgbox, $message) {
  assert (is_string ($msgbox));
  assert (is_array ($message));

  global $_GALAXY;
  global $_CONFIG;
  global $_RUN;
    
  $template = new Smarty ();
  $template->assign ("priority_str", $message['priority']==1?"high":"low");
  $template->assign ("priority_img", $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/msg/".$message['priority'].".jpg");
  $template->assign ("id", $message['id']);
  $template->assign ("from", $message['msg_from']);
  $template->assign ("datetime", $message['datetime']);
  $template->assign ("delete_href", "message.php?cmd=".encrypt_get_vars("delete")."&frmid=".encrypt_get_vars ($_RUN['current_page_checksum'])."&bid=".encrypt_get_vars ($msgbox)."&mid=".encrypt_get_vars($message['id']));
  $template->assign ("subject", $message['msg_subject']);
  $template->assign ("body", convert_px_to_html_tags ($message['text']));
  $template->display ($_RUN['theme_path']."/messages-normal-msg.tpl");
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
function message_create ($dst_user_id) {
  $src_race = user_get_race (user_ourself());
  $dst_race = user_get_race ($dst_uid);

  print_remark ("Createmessage");
  form_start ();
  echo "  <table align=center border=0 width=80%>\n";
  echo "    <tr><th>Send message</th></tr>\n";
  echo "    <tr><td>From:     </td><td>".$src_race." race</td></tr>\n";
  echo "    <tr><td>To:       </td><td>".$dst_race." race</td></tr>\n";
  echo "    <tr><td>Priority: </td><td><select name=priority><option value=1>Normal</option><option value=2>High</option></select></td></tr>\n";
  echo "    <tr><td>Subject:  </td><td><input type=text name=subject size=50 maxvalue=50></td></tr>\n";
  echo "    <tr><td>Msg:      </td><td><textarea name=msg rows=5 cols=60></textarea></td></tr>\n";
  echo "    <tr><td>&nbsp;    </td><td><input type=submit name=submit value='Send Message'></td></tr>\n";
  echo "  </table>\n";
  echo "  <br><br>\n";
  echo "\n";
  echo "<input type=hidden name=src_uid value=".encrypt_get_vars ($src_user_id).">\n";
  echo "<input type=hidden name=dst_uid value=".encrypt_get_vars ($dst_user_id).">\n";
  echo "<input type=hidden name=target value=".encrypt_get_vars ("USER").">\n";
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("post").">\n";
  form_end ();
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
function message_show_galaxy ($user_id) {
	assert (is_numeric ($user_id));	
  global $_CONFIG;
  global $_RUN;
  
  $user    = user_get_user ($user_id);
  
  $result = sql_query ("SELECT * FROM m_galaxy WHERE deleted=0 AND level <= ".$user['explore_level']." ORDER BY DATETIME DESC");
  while ($message = sql_fetchrow ($result)) {
    if ($message['from_uid'] == UID_NOBODY) {
      $from_user['avatar'] = "default.gif";
    } else {
      $from_user = user_get_perihelion_user ($message['from_uid']);
    }
    
    $template = new Smarty ();
    $template->debugging = true;
    $template->assign ("from", $message['msg_from']);
    $template->assign ("datetime", $message['datetime']);
    $template->assign ("subject", $message['msg_subject']);
    $template->assign ("image", $_CONFIG['IMAGE_URL'].'/users/'.$from_user['avatar']);
    $template->assign ("body", convert_px_to_html_tags ($message['text']));
    $template->display ($_RUN['theme_path']."/messages-galaxy-msg.tpl");
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
function message_create_galaxy ($user_id) {  
  $src_race = user_get_race ($user_id);

  print_remark ("Createmessage");
  form_start ();
  echo "  <table align=center border=0 width=80%>\n";
  echo "    <tr><th>Send message</th></tr>\n";
  echo "    <tr><td>From:          </td><td>".$src_race." race</td></tr>\n";
  echo "    <tr><td>To:            </td><td>Outer Space</td></tr>\n";
  echo "    <tr><td>Minimum Level: </td><td><input type=text name=level size=5 maxvalue=5 value=0></td></tr>\n";
  echo "    <tr><td>Subject:       </td><td><input type=text name=subject size=50 maxvalue=50></td></tr>\n";
  echo "    <tr><td>Msg:           </td><td><textarea name=msg rows=5 cols=60></textarea></td></tr>\n";
  echo "    <tr><td>&nbsp;         </td><td><input type=submit name=submit value='Send Message'></td></tr>\n";
  echo "  </table>\n";
  echo "  <br><br>\n";
  echo "\n";
  echo "<input type=hidden name=src_uid value=".encrypt_get_vars ($user_id).">\n";
  echo "<input type=hidden name=target value=".encrypt_get_vars ("GALAXY").">\n";
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("post").">\n";
  form_end ();
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
function message_show_alliance ($user_id) {
	assert (is_numeric ($user_id));	
  global $_CONFIG;
  
  $user    = user_get_user ($user_id);
  
  $result = sql_query ("SELECT * FROM m_alliance WHERE deleted=0 AND alliance_id = ".$user['alliance_id']." ORDER BY DATETIME DESC");
  while ($message = sql_fetchrow ($result)) {
    if ($message['from_uid'] == UID_NOBODY) {
      $from_user['avatar'] = "default.gif";
    } else {
      $from_user = user_get_perihelion_user ($message['from_uid']);
    }
    
    $template = new Smarty ();
    $template->debugging = true;
    $template->assign ("from", $message['msg_from']);
    $template->assign ("datetime", $message['datetime']);
    $template->assign ("subject", $message['msg_subject']);
    $template->assign ("image", $_CONFIG['IMAGE_URL'].'/users/'.$from_user['avatar']);
    $template->assign ("body", convert_px_to_html_tags ($message['text']));
    $template->display ($_RUN['theme_path']."/messages-alliance-msg.tpl");
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
function message_create_alliance ($user_id) {
  $user     = user_get_user ($user_id);
  $race     = user_get_race ($user['user_id']);
  $result   = sql_query ("SELECT * FROM g_alliance WHERE id=".$user['alliance_id']);
  $alliance = sql_fetchrow ($result);

  print_remark ("Createmessage");
  form_start ();
  echo "  <table align=center border=0 width=80%>\n";
  echo "    <tr><th>Send message</th></tr>\n";
  echo "    <tr><td>From:          </td><td>".$race." race</td></tr>\n";
  echo "    <tr><td>To:            </td><td>All members of ".$alliance['name']."</td></tr>\n";
  echo "    <tr><td>Subject:       </td><td><input type=text name=ne_subject size=50 maxvalue=50></td></tr>\n";
  echo "    <tr><td>Msg:           </td><td><textarea name=ne_msg rows=5 cols=60></textarea></td></tr>\n";
  echo "    <tr><td>&nbsp;         </td><td><input type=submit name=submit value='Send Message'></td></tr>\n";
  echo "  </table>\n";
  echo "  <br><br>\n";
  echo "\n";
  echo "<input type=hidden name=src_uid value=".encrypt_get_vars ($user['user_id']).">\n";
  echo "<input type=hidden name=dst_uid value=".encrypt_get_vars ($user['alliance_id']).">\n";
  echo "<input type=hidden name=target value=".encrypt_get_vars ("ALLIANCE").">\n";
  echo "<input type=hidden name=cmd value=".encrypt_get_vars ("post").">\n";
  form_end ();
}

?>

