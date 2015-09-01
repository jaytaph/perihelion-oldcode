<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("User preferences",
               "You can modify your Perihelion preferences on this page");

  $cmd = input_check ("show", "uid", 0,
                      "post", "!frmid", "uid", 0);
                                            
  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();    
    SmartyValidate::clear();
    prefs_smarty_show ($uid);
  }
  if ($cmd == "post") {
  	if (! prefs_smarty_validate ($uid)) prefs_smarty_show ($uid);
  }
  
  print_footer ();
  exit;
  
  
function prefs_smarty_show ($user_id) {
	assert (is_numeric ($user_id));
	
	global $_RUN;
	global $_USER;
	
  $template = new Smarty();
  help_set_template_vars ($template, "PREFERENCES");
  $template->debugging = true; 

  $result = sql_query ("SELECT * FROM perihelion.t_themes");
  while ($row = sql_fetchrow ($result)) {
  	$tmpvar['ids'][] = $row['id'];
  	$tmpvar['names'][] = $row['name'];  	
  }
  $template->assign ("themes_ids", $tmpvar['ids']);
  $template->assign ("themes_names", $tmpvar['names']);

  $user = user_get_perihelion_user ($_USER['id']);
  
  if (SmartyValidate::is_init ()) {
  	$template->assign ($_POST);
  } else {
  	SmartyValidate::init();
    SmartyValidate::register_criteria ("validate_email_is_ours_or_does_not_exists");  
    SmartyValidate::register_criteria ("validate_passwd");

    $template->assign ("name", $user['name']);
    $template->assign ("email", $user['email']);
    $template->assign ("inform", $user['inform']);
    $template->assign ("gender", $user['gender']);
    $template->assign ("country", $user['country']);
    $template->assign ("city", $user['city']);
    $template->assign ("tag", $user['tag']);
    $tmp = split ('-', $user['birthday']);
    $template->assign ("dob_Day", $tmp[2]);
    $template->assign ("dob_Month", $tmp[1]);  
    $template->assign ("dob_Year", $tmp[0]);
    $template->assign ("theme", $user['theme_id']);
  }  	
      
  $template->assign ("uid", encrypt_get_vars ($user_id));
  $template->assign ("cmd", encrypt_get_vars ("post"));
  $template->assign ("frmid", encrypt_get_vars (get_form_id()));
  
	$template->display ($_RUN['theme_path']."/preferences.tpl");
}

function prefs_smarty_validate () {
	global $_RUN;
	
  if (SmartyValidate::is_init() && SmartyValidate::is_valid ($_POST)) {
  	SmartyValidate::clear();

    // And set the preferences
    $ok = "";
    $errors['PARAMS'] = "Incorrect parameters specified..\n";
    $data['name']       = $_POST['name'];
    $data['email']      = $_POST['email'];
    $data['theme_id']   = $_POST['theme'];
    $data['gender']     = $_POST['gender'];
    $data['city']       = $_POST['city'];
    $data['country']    = $_POST['country'];
    $data['tag']        = $_POST['tag'];
    $data['dob']        = $_POST['dob_Year'] . "-" . $_POST['dob_Month'] . "-" . $_POST['dob_Day'];
    if (isset ($inform)) $data['inform'] = $_POST['inform'];   
    if (isset ($login_pass)) $data['login_pass'] = $_POST['login_pass'];
    
    $user_id = decrypt_get_vars ($_POST['uid']);
    $data['user_id']    = $user_id;
    comm_send_to_server ("SETPREFS", $data, $ok, $errors);

    $result = sql_query ("SELECT * FROM perihelion.u_users WHERE id=".$user_id);
    $tmp = sql_fetchrow ($result);
    session_reinit ($tmp);

    $template = new Smarty();
   	$template->display ($_RUN['theme_path']."/preferences-success.tpl");
   	return true;
 	} 	
 	return false;
}
 
  
function validate_email_is_ours_or_does_not_exists ($value, $empty, &$params, &$formvars) {   
  $result = sql_query ("SELECT * FROM perihelion.u_users WHERE email LIKE '$value'");
  if (sql_countrows ($result) == 0) return true;
  
  // Check if it's ours, if so, then this one is allright..
  $uid = decrypt_get_vars ($formvars['uid']);
  $row = sql_fetchrow ($result);
  if ($row['id'] == $uid) return true;
  
  return false;
}  

function validate_passwd ($value, $empty, &$params, &$formvars) { 
  $result = sql_query ("SELECT PASSWORD('".$value."') AS passwd");
  $row = sql_fetchrow ($result);
  $encrypted_passwd = $row['passwd'];
  
  $uid = decrypt_get_vars ($formvars['uid']);
         
  $result = sql_query ("SELECT * FROM perihelion.u_users WHERE id=".$uid);
  if ($row = sql_fetchrow ($result)) {
  	if ($row['login_pass'] == $encrypted_passwd) return true;
  }
  return false;
}  
  




/*
  $cmd = input_check ("show", "uid", 0,
                      "post", "!frmid", "uid", "ne_name", "ne_email", "ne_inform", "ne_gender", "ne_country", "ne_city", "ne_tag", "ne_doby", "ne_dobm", "ne_dobd", "ne_login_pass", "ne_login_pass2", "ne_validate_pass", 0);

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    $user = user_get_perihelion_user ($uid);

    $prefs['name']    = $user['name'];
    $prefs['email']   = $user['email'];
    $prefs['inform']  = $user['inform'];
    $prefs['gender']  = $user['gender'];
    $prefs['country'] = $user['country'];
    $prefs['city']    = $user['city'];
    $prefs['tag']     = $user['tag'];
    $tmp = split ('-', $user['birthday']);
    $prefs['doby']    = $tmp[0];
    $prefs['dobm']    = $tmp[1];
    $prefs['dobd']    = $tmp[2];

    $errors_txt = array();
    $errors = array();
    show_preferences ($prefs, $errors_txt, $errors);
  }
  if ($cmd == "post") {
    $prefs['name']    = $ne_name;
    $prefs['email']   = $ne_email;
    $prefs['inform']  = $ne_inform;
    $prefs['gender']  = $ne_gender;
    $prefs['country'] = $ne_country;
    $prefs['city']    = $ne_city;
    $prefs['tag']     = $ne_tag;
    $prefs['doby']    = $ne_doby;
    $prefs['dobm']    = $ne_dobm;
    $prefs['dobd']    = $ne_dobd;
    $prefs['login_pass'] = $ne_login_pass;
    $prefs['login_pass2'] = $ne_login_pass2;
    $prefs['validate_pass'] = $ne_validate_pass;

    if ($uid == "") $uid = user_ourself();


    $errors_txt = array ();
    $errors = array ();
    parse_preferences ($uid, $prefs);
    if (empty ($errors_txt)) post_preferences ($uid, $prefs);
    show_preferences ($prefs, $errors_txt, $errors);
  }

  print_footer();
  exit;


/*
    $user = user_get_perihelion_user ($_USER['id']);
    $_POST['name']    = $user['name'];
    $_POST['email']   = $user['email'];
    $_POST['inform']  = $user['inform'];
    $_POST['gender']  = $user['gender'];
    $_POST['country'] = $user['country'];
    $_POST['city']    = $user['city'];
    $_POST['tag']     = $user['tag'];
    $tmp = split ('-', $user['birthday']);
    $_POST['doby']    = $tmp[0];
    $_POST['dobm']    = $tmp[1];
    $_POST['dobd']    = $tmp[2];
  }

  // Make sure the post-values exists, otherwise the inputboxes will complain
  if (!isset ($_POST['name']))        $_POST['name'] = "";
  if (!isset ($_POST['email']))       $_POST['email'] = "";
  if (!isset ($_POST['inform']))      $_POST['inform'] = "";
  if (!isset ($_POST['gender']))      $_POST['gender'] = "";
  if (!isset ($_POST['country']))     $_POST['country'] = "";
  if (!isset ($_POST['city']))        $_POST['city'] = "";
  if (!isset ($_POST['tag']))         $_POST['tag'] = "";
  if (!isset ($_POST['doby']))        $_POST['doby'] = "";
  if (!isset ($_POST['dobm']))        $_POST['dobm'] = "";
  if (!isset ($_POST['dobd']))        $_POST['dobd'] = "";
*/


// ====================================================================
function parse_preferences ($user_id, $prefs) {
  global $errors;
  global $errors_txt;
  global $_USER;
  global $config;
  global $ext;

  // We submitted data, check for errors
  if (empty ($prefs['name'])) {
    array_push ($errors_txt, "Please enter your full name");
    array_push ($errors, "name");
  }

  if (empty ($prefs['country'])) {
    array_push ($errors_txt, "Please enter the country where you live");
    array_push ($errors, "country");
  }
  if (empty ($prefs['city'])) {
    array_push ($errors_txt, "Please enter the city where you live");
    array_push ($errors, "city");
  }
  if (empty ($prefs['email'])) {
    array_push ($errors_txt, "Please enter your email address");
    array_push ($errors, "email");
  } else {
    if (!eregi("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$", $prefs['email'])) {
      array_push ($errors_txt, "Not a valid Email address!");
      array_push ($errors, "email");
    } else {
//      sql_select_db ("perihelion");
      $result = sql_query ("SELECT * FROM perihelion.u_users WHERE email LIKE '".$prefs['email']."' AND id != ".$user_id);
      $row = sql_fetchrow ($result);
      if (!empty ($row)) {
        array_push ($errors_txt, "Email address already used!");
        array_push ($errors, "email");
      }
//     sql_select_db ($config['default_db']);
    }
  }
  if (empty ($prefs['validate_pass'])) {
    array_push ($errors_txt, "Please enter your password again");
    array_push ($errors, "validate_pass");
  } else {
//    sql_select_db ("perihelion");
    $result = sql_query ("SELECT * FROM perihelion.u_users WHERE login_name='".$_USER['login_name']."' AND login_pass LIKE PASSWORD ('".$prefs['validate_pass']."')");
    $row = sql_fetchrow ($result);
    if (empty ($row)) {
      array_push ($errors_txt, "Incorrect password.");
      array_push ($errors, "validate_pass");
    }
//    sql_select_db ($config['default_db']);
  }

  if ($prefs['login_pass']!="" and $prefs['login_pass'] != $prefs['login_pass2']) {
    array_push ($errors_txt, "New password doesn't match");
    array_push ($errors, "login_pass");
    array_push ($errors, "login_pass2");
  }
  if (checkdate ($prefs['dobm'], $prefs['dobd'], $prefs['doby']) == false) {
    array_push ($errors_txt, "Incorrect birthday");
    array_push ($errors, "dob");
  }

  if ($_FILES['avatar']['error'] != 4) {
    // Is the file too big?
    if ($_FILES['avatar']['size'] > 100 * 1024) {
      array_push ($errors_txt, "Avatar is too big (>100K)");
      array_push ($errors, "avatar");
    }

    // Is it a JPG or a GIF?
    $fileinfo = getimagesize ($_FILES['avatar']['tmp_name']);
    if ($fileinfo[2] == IMG_GIF) {
      $ext = ".gif";
    } elseif ($fileinfo[2] == IMG_JPG) {
      $ext = ".jpg";
    } else {
      array_push ($errors_txt, "Avatar is not a .JPG or a .GIF file");
      array_push ($errors, "avatar");
    }
  }
}

// ===========================================================================
function post_preferences ($user_id, $prefs) {
  global $_USER;
  global $ext;

  // Now move the avatar if any was uploaded
  if ($_FILES['avatar']['error'] != 4) {
  	// TODO: Change PATH into config thingie
    move_uploaded_file ($_FILES['avatar']['tmp_name'], "c:\\perihelion\\WWW\\perihelion\\images\\users\\".$_USER['login_name'].$ext);
  }

  // And set the preferences
  $ok = "";
  $errors['PARAMS'] = "Incorrect parameters specified..\n";
  $data['name']       = $prefs['name'];
  $data['email']      = $prefs['email'];
  $data['inform']     = $prefs['inform'];
  $data['gender']     = $prefs['gender'];
  $data['city']       = $prefs['city'];
  $data['country']    = $prefs['country'];
  $data['tag']        = $prefs['tag'];

  if ($_FILES['avatar']['error'] != 4) {
    $data['avatar']     = $_USER['login_name'].$ext;
  } else {
    $data['avatar']     = '';
  }
  $data['login_pass'] = $prefs['login_pass'];
  $data['dob']        = $prefs['doby'] . "-" . $prefs['dobm'] . "-" . $prefs['dobd'];
  $data['user_id']    = $user_id;
  comm_send_to_server ("SETPREFS", $data, $ok, $errors);

  $result = sql_query ("SELECT * FROM perihelion.u_users WHERE id=".$user_id);
  $_USER = sql_fetchrow ($result);
  session_reinit ($_USER);
}




/****************************************************************************************************
 */
function show_preferences ($prefs, $errors_txt="", $errors="") {
  if (!isset ($errors)) $errors = array ();
  if (!isset ($errors_txt)) $errors_txt = array ();

  form_start ("enctype='multipart/form-data'");
  echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("post").">\n";

  echo "  <table align=center border=0>\n";
  echo "  <tr><td colspan=2>&nbsp</td></tr>\n";
  prefs_show_errors ($prefs, $errors_txt, $errors);

  echo "  <tr><td colspan=2>&nbsp</td></tr>\n";
  prefs_show_name ($prefs, $errors_txt, $errors);
  prefs_show_email ($prefs, $errors_txt, $errors);
  prefs_show_city ($prefs, $errors_txt, $errors);
  prefs_show_country ($prefs, $errors_txt, $errors);
  prefs_show_inform ($prefs, $errors_txt, $errors);
  prefs_show_gender ($prefs, $errors_txt, $errors);
  prefs_show_birthdate ($prefs, $errors_txt, $errors);

  echo "  <tr><td colspan=2>&nbsp</td></tr>\n";
  prefs_show_tag ($prefs, $errors_txt, $errors);
  prefs_show_avatar ($prefs, $errors_txt, $errors);

  echo "  <tr><td colspan=2>&nbsp</td></tr>\n";
  echo "  <tr><td colspan=2>Only fill this in if you want a new password:</td></tr>\n";
  prefs_show_loginpass ($prefs, $errors_txt, $errors);
  prefs_show_loginpass2 ($prefs, $errors_txt, $errors);
  echo "  <tr><td colspan=2>&nbsp</td></tr>\n";
  echo "  <tr><td colspan=2>&nbsp</td></tr>\n";
  echo "  <tr><td colspan=2>Enter your password for identification:</td></tr>\n";
  prefs_show_validate_pass ($prefs, $errors_txt, $errors);
  echo "  <tr><td></td><td><input type=submit name=submit value='Set Preferences'></td></tr>\n";
  echo "  </table>\n";
  form_end ();
}

/****************************************************************************************************
 */
function prefs_show_errors ($prefs, $errors_txt, $errors) {
  if (!empty ($errors_txt)) {
    echo "<tr><td>Please correct the following items:</td></tr>\n";
    while (list ($key, $val) = each ($errors_txt)) {
      echo "<tr><td colspan=2><li><b>".$val."</b></td></tr>\n";
    }
  }
}


/****************************************************************************************************
 */
function prefs_show_birthdate ($prefs, $errors_txt, $errors) {
  if (in_array ("dob", $errors)) { $color="red"; } else { $color="white"; }

  echo "<tr><td><b><font color=\"$color\">Birthday:</b>        </td><td>\n";
  echo "  <select name=ne_dobd>\n";
  for ($i=1; $i!=32; $i++) {
    if ($i == $prefs['dobd']) {
      echo "    <option selected value=$i>$i</option>\n";
    } else {
      echo "    <option value=$i>$i</option>\n";
    }
  }
  echo "  </select>&nbsp;\n";

  echo "  <select name=ne_dobm>\n";
  for ($i=1; $i!=13; $i++) {
    if ($i == $prefs['dobm']) {
      echo "    <option selected  value=$i>".date('F', mktime (0, 0, 0, $i, 1, 2000))."</option>\n";
    } else {
      echo "    <option value=$i>".date('F', mktime (0, 0, 0, $i, 1, 2000))."</option>\n";
    }
  }
  echo "</select>&nbsp;\n";

  echo "<input type=text size=4 maxlength=4 name=ne_doby value=\"".$prefs['doby']."\"></td></tr>\n";

  echo "</td></tr>\n";
}
/****************************************************************************************************
 */
function prefs_show_name ($prefs, $errors_txt, $errors) {
  if (in_array ("name", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"$color\">Name:</b>        </td><td><input type=text size=30 maxlength=50 name=ne_name value=\"".stripslashes($prefs['name'])."\"></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_email ($prefs, $errors_txt, $errors) {
  if (in_array ("email", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">Email:</b>        </td><td><input type=text size=30 maxlength=50 name=ne_email value=\"".stripslashes($prefs['email'])."\"></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_inform ($prefs, $errors_txt, $errors) {
  $checked = "";
  if (isset ($prefs['inform'])) $checked="checked";
  echo "<tr><td></td><td><input type=checkbox ".$checked." name=ne_inform>Spam me the latest Perihelion news!</td></tr>\n";
}


/****************************************************************************************************
 */
function prefs_show_gender ($prefs, $errors_txt, $errors) {
  if ($prefs['gender']=="F") {
    $male_checked="";
    $female_checked="checked";
  } else {
    $male_checked="checked";
    $female_checked="";
  }
  echo "<tr><td><b>Gender:</b></td><td><table border=0 width=\"100%\"><tr>\n";
  echo "<td><input type=radio ".$male_checked." name=ne_gender value=M>Male</td>";
  echo "<td><input type=radio ".$female_checked." name=ne_gender value=F>Female</td>";
  echo "</tr></table></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_country ($prefs, $errors_txt, $errors) {
  if (in_array ("country", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">Country:</b>        </td><td><input type=text size=30 maxlength=30 name=ne_country value=\"".stripslashes($prefs['country'])."\"></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_city ($prefs, $errors_txt, $errors) {
  if (in_array ("city", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">City:</b>        </td><td><input type=text size=30 maxlength=30 name=ne_city value=\"".stripslashes($prefs['city'])."\"></td></tr>\n";
}


/****************************************************************************************************
 */
function prefs_show_loginpass ($prefs, $errors_txt, $errors) {
  if (in_array ("login_pass", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">New Password:</b>        </td><td><input type=password size=30 maxlength=30 name=ne_login_pass></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_loginpass2 ($prefs, $errors_txt, $errors) {
  if (in_array ("login_pass2", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">Again:</b>        </td><td><input type=password size=30 maxlength=30 name=ne_login_pass2></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_validate_pass ($prefs, $errors_txt, $errors) {
  if (in_array ("validate_pass", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">Password:</b>        </td><td><input type=password size=30 maxlength=30 name=ne_validate_pass></td></tr>\n";
}


/****************************************************************************************************
 */
function prefs_show_tag ($prefs, $errors_txt, $errors) {
  if (in_array ("tag", $errors)) { $color="red"; } else { $color="white"; }
  echo "<tr><td><b><font color=\"".$color."\">Tag Line:</b>        </td><td><input type=text size=50 maxlength=50 name=ne_tag value=\"".stripslashes($prefs['tag'])."\"></td></tr>\n";
}

/****************************************************************************************************
 */
function prefs_show_avatar ($prefs, $errors_txt, $errors) {
  global $_USER;

  if (in_array ("avatar", $errors)) { $color="red"; } else { $color="white"; }

  echo "<input type=hidden name=MAX_FILE_SIZE value=".(100*1024).">\n";

  echo "<tr><td><b><font color=\"".$color."\">Avatar Image:</b>        </td><td><input size=35 type=file name=avatar></td></tr>\n";
  if (!isset ($prefs['avatar'])) {
    echo "<tr><td>&nbsp;</td><td colspan=1><img width=100 height=100 src=images/users/".$_USER['avatar']."><b><font color=\"".$color."\"></td></tr>\n";
  }
}

?>
