<?php

  // Include Files
  include "includes.inc.php";
  
  session_start ();
    
  print_header();
  print_title ("Registration",
               "With this form you can register yourself as a new player of perihelion. Please fill in all information below");

  $template = new Smarty();
  help_set_template_vars ($template, "REGISTER");
  $template->debugging = true;

  $result = sql_query ("SELECT * FROM perihelion.t_themes");
  while ($row = sql_fetchrow ($result)) {
  	$tmpvar['ids'][] = $row['id'];
  	$tmpvar['names'][] = $row['name'];  	
  }
  $template->assign ("themes_ids", $tmpvar['ids']);
  $template->assign ("themes_names", $tmpvar['names']);

    
  if (!SmartyValidate::is_init () && !isset ($_POST['submit'])) {
  	SmartyValidate::init();
    SmartyValidate::register_criteria ("validate_email");
    SmartyValidate::register_criteria ("validate_login");
    SmartyValidate::register_criteria ("validate_specie");
    SmartyValidate::register_criteria ("validate_sector");
    SmartyValidate::register_criteria ("validate_planet");

  	$template->display ($_RUN['theme_path']."/register.tpl");
  } else {
  	if (SmartyValidate::is_valid ($_POST)) {  		
  		SmartyValidate::clear();
      $ok = "";
      $errors['PARAMS'] = "Incorrect parameters specified..\n";
      $data['tag']        = $_POST['tag'];
      $data['name']       = $_POST['name'];
      $data['email']      = $_POST['email'];
      $data['inform']     = $_POST['inform'];
      $data['gender']     = $_POST['gender'];
      $data['city']       = $_POST['city'];
      $data['country']    = $_POST['country'];
      $data['species']    = $_POST['species'];
      $data['planet']     = $_POST['planet'];
      $data['sector']     = $_POST['sector'];
      $data['login_name'] = $_POST['login_name'];
      $data['login_pass'] = $_POST['login_pass'];
      if (comm_send_to_server ("REGISTER", $data, $ok, $errors) == 1) {
      	$template->display ($_RUN['theme_path']."/register-success.tpl");        
      } else {
      	$template->display ($_RUN['theme_path']."/register-failure.tpl");        
      }  		

  	} else {
  	  $template->assign ($_POST);
  	  $template->display ($_RUN['theme_path']."/register.tpl");
  	}
  }
  
  print_footer();
  exit;
  
  
  
function validate_email ($value, $empty, &$params, &$formvars) {   
  $result = sql_query ("SELECT * FROM perihelion.u_users WHERE email LIKE '$value'");
  if (sql_countrows ($result) == 0) return true;
  return false;
}

function validate_login ($value, $empty, &$params, &$formvars) { 
  $result = sql_query ("SELECT * FROM perihelion.u_users WHERE login_name LIKE '$value'");
  if (sql_countrows ($result) == 0) return true;
  return false;
}

function validate_specie ($value, $empty, &$params, &$formvars) { 
	global $_CONFIG;
  $result = sql_query ("SELECT * FROM ".$_CONFIG['default_db'].".g_users WHERE race LIKE '$value'");
  if (sql_countrows ($result) == 0) return true;
  return false;
}

function validate_sector ($value, $empty, &$params, &$formvars) { 
  global $_CONFIG;
  $result = sql_query ("SELECT * FROM ".$_CONFIG['default_db'].".s_sectors WHERE name LIKE '$value'");
  if (sql_countrows ($result) == 0) return true;
  return false;
}
 
function validate_planet ($value, $empty, &$params, &$formvars) { 
  global $_CONFIG;
  $result = sql_query ("SELECT * FROM ".$_CONFIG['default_db'].".s_anomalies WHERE name LIKE '$value'");
  if (sql_countrows ($result) == 0) return true;
  return false;
}





































  // Include Files
  include "includes.inc.php";

  $errors_txt = array ();
  $errors = array ();

  // Make sure the post-values exists, otherwise the inputboxes will complain
  if (!isset ($_POST['name']))        $_POST['name'] = "";
  if (!isset ($_POST['email']))       $_POST['email'] = "";
  if (!isset ($_POST['inform']))      $_POST['inform'] = "";
  if (!isset ($_POST['checked']))     $_POST['checked'] = "";
  if (!isset ($_POST['gender']))      $_POST['gender'] = "";
  if (!isset ($_POST['country']))     $_POST['country'] = "";
  if (!isset ($_POST['city']))        $_POST['city'] = "";
  if (!isset ($_POST['login_name']))  $_POST['login_name'] = "";
  if (!isset ($_POST['species']))     $_POST['species'] = "";
  if (!isset ($_POST['planet']))      $_POST['planet'] = "";
  if (!isset ($_POST['sector']))      $_POST['sector'] = "";
  if (!isset ($_POST['tag']))         $_POST['tag'] = "";


  // First time here? Then show the register screen
  if (!isset($submit)) {
    register ($errors_txt, $errors);
  } else {

    // We already submitted data, check for errors
    if (empty ($name)) {
      array_push ($errors_txt, "Please enter your full name");
      array_push ($errors, "name");
    }
    if (empty ($country)) {
      array_push ($errors_txt, "Please enter the country where you live");
      array_push ($errors, "country");
    }
    if (empty ($city)) {
      array_push ($errors_txt, "Please enter the city where you live");
      array_push ($errors, "city");
    }
    if (empty ($email)) {
      array_push ($errors_txt, "Please enter your email address");
      array_push ($errors, "email");
    } else {
      if (!eregi("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$", $email)) {
        array_push ($errors_txt, "Not a valid Email address!");
        array_push ($errors, "email");
      } else {
//        sql_select_db ("perihelion");
        $result = sql_query ("SELECT * FROM perihelion.u_users WHERE email LIKE '$email'");
        $row = sql_fetchrow ($result);
        if (!empty ($row)) {
          array_push ($errors_txt, "Email address already used!");
          array_push ($errors, "email");
        }
//	      sql_select_db ($config['default_db']);
      }
    }
    if (empty ($login_name)) {
      array_push ($errors_txt, "Please enter a login name");
      array_push ($errors, "login_name");
    } else {
//      sql_select_db ("perihelion");
      $result = sql_query ("SELECT * FROM perihelion.u_users WHERE login_name LIKE '$login_name'");
      $row = sql_fetchrow ($result);
      if (!empty ($row)) {
        array_push ($errors_txt, "Login name already taken!");
        array_push ($errors, "login_name");
      }
//      sql_select_db ($config['default_db']);
    }

    if (empty ($login_pass)) {
      array_push ($errors_txt, "Please enter your login password");
      array_push ($errors, "login_pass");
      array_push ($errors, "login_pass2");
    }
    if ($login_pass != $login_pass2) {
      array_push ($errors_txt, "Passwords don't match");
      array_push ($errors, "login_pass");
      array_push ($errors, "login_pass2");
    }
    if (empty ($species)) {
      array_push ($errors_txt, "Please enter your species name");
      array_push ($errors, "species");
    } else {
      $result = sql_query ("SELECT * FROM s_species WHERE name LIKE '$species'");
      $row = sql_fetchrow ($result);
      if (!empty ($row)) {
        array_push ($errors_txt, "Species name already taken!");
        array_push ($errors, "species");
      }
    }
    if (empty ($sector)) {
      array_push ($errors_txt, "Please enter your home sector name");
      array_push ($errors, "sector");
    } else {
      $result = sql_query ("SELECT * FROM s_sectors WHERE name LIKE '$sector'");
      $row = sql_fetchrow ($result);
      if (!empty ($row)) {
        array_push ($errors_txt, "Sector name already taken!");
        array_push ($errors, "sector");
      }
    }
    if (empty ($planet)) {
      array_push ($errors_txt, "Please enter your home world name");
      array_push ($errors, "planet");
    } else {
      $result = sql_query ("SELECT * FROM s_anomalies WHERE name LIKE '$planet'");
      $row = sql_fetchrow ($result);
      if (!empty ($row)) {
        array_push ($errors_txt, "Planet name already taken!");
        array_push ($errors, "planet");
      }
    }

    // We found errors on the page, try again...
    if (!empty ($errors_txt)) {
      register ($errors_txt, $errors);
    } else {

      // Whoohooo, we are to registered correctly
      print_header ();

      $ok = "";
      $errors['PARAMS'] = "Incorrect parameters specified..\n";
      $data['tag']        = $_POST['tag'];
      $data['name']       = $_POST['name'];
      $data['email']      = $_POST['email'];
      $data['inform']     = $_POST['inform'];
      $data['gender']     = $_POST['gender'];
      $data['city']       = $_POST['city'];
      $data['country']    = $_POST['country'];
      $data['species']    = $_POST['species'];
      $data['planet']     = $_POST['planet'];
      $data['sector']     = $_POST['sector'];
      $data['login_name'] = $_POST['login_name'];
      $data['login_pass'] = $_POST['login_pass'];
      if (comm_send_to_server ("REGISTER", $data, $ok, $errors) == 1) {
        echo "You have registered correctly. Now click here (Which doesn't work because i didn't finish it)...\n";
      } else {
        echo "An unknown error has occured...\n";
      }

      print_footer ();
    }
  } // if $(submit)
  exit;



/****************************************************************************************************
 */
function register ($errors_txt="", $errors="") {
  if (!isset ($errors)) $errors = array ();
  if (!isset ($errors_txt)) $errors_txt = array ();

  print_header ();
  echo "<FORM method=post action=\"register.php\">";
  echo "<TABLE align=center border=0>";
  echo "<TR><TD COLSPAN=2><b><h1><u>Register as a new user</u></h1></b></TD></TR>";
  register_show_name ($errors_txt, $errors);
  register_show_email ($errors_txt, $errors);
  register_show_inform ($errors_txt, $errors);
  echo "<TR><TD COLSPAN=2>&nbsp</TD></TR>";
  register_show_tag ($errors_txt, $errors);
  register_show_avatar ($errors_txt, $errors);
  echo "<TR><TD COLSPAN=2>&nbsp</TD></TR>";
  register_show_gender ($errors_txt, $errors);
  register_show_city ($errors_txt, $errors);
  register_show_country ($errors_txt, $errors);
  echo "<TR><TD COLSPAN=2>&nbsp</TD></TR>";
  register_show_loginname ($errors_txt, $errors);
  register_show_loginpass ($errors_txt, $errors);
  register_show_loginpass2 ($errors_txt, $errors);
  echo "<TR><TD COLSPAN=2>&nbsp</TD></TR>";
  register_show_species ($errors_txt, $errors);
  register_show_sector ($errors_txt, $errors);
  register_show_planet ($errors_txt, $errors);
  echo "<TR><TD COLSPAN=2>&nbsp</TD></TR>";
  echo "<TR><TD></TD><TD><input type=submit name=submit value=register></TD></TR>";
  echo "<TR><TD COLSPAN=2>&nbsp</TD></TR>";
  register_show_errors ($errors_txt, $errors);
  echo "</TABLE>";
  echo "</FORM>";
  print_footer ();
}

/****************************************************************************************************
 */
function register_show_errors ($errors_txt, $errors) {
  if (!empty ($errors_txt)) {
    echo "<tr><td>Please correct the following items:</td></tr>";
    while (list ($key, $val) = each ($errors_txt)) {
      echo "<TR><TD COLSPAN=2><li><b>".$val."</b></TD></TR>";
    }
  }
}

/****************************************************************************************************
 */
function register_show_name ($errors_txt, $errors) {
  if (in_array ("name", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Name:</b>         </TD><TD><input type=text size=30 maxlength=50 name=name value=\"".$_POST['name']."\"></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_email ($errors_txt, $errors) {
  if (in_array ("email", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Email:</b>        </TD><TD><input type=text size=30 maxlength=50 name=email value=\"".$_POST['email']."\"></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_inform ($errors_txt, $errors) {
  $checked = "";
  if (isset ($_POST['inform'])) $checked="checked";
  echo "<TR><TD></TD><TD><input type=checkbox ".$checked." name=inform>Spam me the latest Perihelion news!</TD></TR>";
}


/****************************************************************************************************
 */
function register_show_gender ($errors_txt, $errors) {
  if ($_POST['gender']=="F") {
    $male_checked="";
    $female_checked="checked";
  } else {
    $male_checked="checked";
    $female_checked="";
  }
  echo "<TR><TD><b>Gender:</b></TD><TD><TABLE border=0 width=\"100%\"><TR>";
  echo "<TD><input type=radio ".$male_checked." name=gender value=M>Male</TD>";
  echo "<TD><input type=radio ".$female_checked." name=gender value=F>Female</TD>";
  echo "</TR></TABLE></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_loginname ($errors_txt, $errors) {
  if (in_array ("login_name", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Login Name:</b>        </TD><TD><input type=text size=30 maxlength=30 name=login_name value=\"".$_POST['login_name']."\"></TD></TR>";
}
/****************************************************************************************************
 */
function register_show_country ($errors_txt, $errors) {
  if (in_array ("country", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Country:</b>        </TD><TD><input type=text size=30 maxlength=30 name=country value=\"".$_POST['country']."\"></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_city ($errors_txt, $errors) {
  if (in_array ("city", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">City:</b>        </TD><TD><input type=text size=30 maxlength=30 name=city value=\"".$_POST['city']."\"></TD></TR>";
}


/****************************************************************************************************
 */
function register_show_loginpass ($errors_txt, $errors) {
  if (in_array ("login_pass", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Password:</b>        </TD><TD><input type=password size=30 maxlength=30 name=login_pass></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_loginpass2 ($errors_txt, $errors) {
  if (in_array ("login_pass2", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Again:</b>        </TD><TD><input type=password size=30 maxlength=30 name=login_pass2></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_species ($errors_txt, $errors) {
  if (in_array ("species", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Species Name:</b>        </TD><TD><input type=text size=30 maxlength=30 name=species value=\"".$_POST['species']."\"></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_sector ($errors_txt, $errors) {
  if (in_array ("sector", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Home Sector Name:</b>        </TD><TD><input type=text size=30 maxlength=30 name=sector value=\"".$_POST['sector']."\"></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_planet ($errors_txt, $errors) {
  if (in_array ("planet", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Home Planet Name:</b>        </TD><TD><input type=text size=30 maxlength=30 name=planet value=\"".$_POST['planet']."\"></TD></TR>";
}


/****************************************************************************************************
 */
function register_show_tag ($errors_txt, $errors) {
  if (in_array ("tag", $errors)) { $color="red"; } else { $color="white"; }
  echo "<TR><TD><b><font color=\"".$color."\">Tag Line:</b>        </TD><TD><input type=text size=50 maxlength=50 name=tag value=\"".$_POST['tag']."\"></TD></TR>";
}

/****************************************************************************************************
 */
function register_show_avatar ($errors_txt, $errors) {
  if (in_array ("avatar", $errors)) { $color="red"; } else { $color="white"; }

  echo "<TR><TD><b><font color=\"".$color."\">Avatar Image:</b>        </TD><TD><input size=15 type=file name=avatar></TD></TR>";
  if (!isset ($_POST['avatar'])) {
    echo "<TR><td>&nbsp;</td><TD colspan=1><IMG SRC=images/users/default.gif><b><font color=\"".$color."\"></td></tr>";
  }
//  echo "Avatar Image:</b>        </TD></TR>";

}

?>
