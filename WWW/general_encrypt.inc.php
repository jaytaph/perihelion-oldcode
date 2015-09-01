<?php

// ============================================================================================
// Generate_Checksum ()
//
// Description:
//   Generates a alphanumeric string which is used as a simple form checksum.
//
// Parameters:
//   none
//
// Returns:
//   string   alphanumeric checksum string
//
function generate_checksum () {
  $checksum = "";
  for ($i=0; $i!=10; $i++) $checksum .= chr(rand (65,65+26));

  return $checksum;
}


// ============================================================================================
// Validate_Request_Checksum
//
// Description:
//   Checks if the checksum found in 'frmid' is already stored in the database. If not,
//   store it. If found, update the try-counter on this checksum in the database and exit.
//
// Parameters:
//   none. But checks the POST or GET variable 'frmid'.
//
// Returns:
//   Returns true is the checksum is not already stored into the database.
//   Returns false otherwise.
//
function validate_request_checksum () {
  global $_USER;

  // No formid found, means we don't have a form posted or getted
  if (! isset ($_REQUEST['frmid'])) return true;

  // Grab the form id, either by get or by post.
  $formid = decrypt_get_vars ($_REQUEST['frmid']);

  // Check if the result is already found, if so, we can't resend the form
  $result = sql_query ("SELECT * FROM perihelion.formid WHERE user_id = ".$_USER['id']." AND id LIKE '".$formid."'");
  if (sql_countrows ($result) == 1) {
    sql_query ("UPDATE perihelion.formid SET tries=tries+1 WHERE id='".$formid."'");
    return false;
  }

  // Not found, so it's the first time we send this form, save it in the database...
  sql_query ("INSERT INTO perihelion.formid (id,tries,user_id,page) VALUES ('".$formid."', 0, ".$_USER['id'].", '".$_SERVER['PHP_SELF']."')");
  return true;
}


// ============================================================================================
// Form_Start ()
//
// Description:
//   Starts a form within perihelion. We use this function so we can track multiple clicks
//   on the same form. Once should be enough.
//
// Parameters:
//   none
//
// Returns:
//   none. But outputs html code.
//
function get_form_id () {
  static $_FORMID = NULL;
  if ($_FORMID == NULL) $_FORMID = md5(uniqid(rand(), true));                // Generate Form id for checking double postings.
  
  return $_FORMID;	 
}

function form_start () {
  static $_FORMID = NULL;
  if ($_FORMID == NULL) $_FORMID = md5(uniqid(rand(), true));                // Generate Form id for checking double postings.

  echo "<form method=post action=".$_SERVER['PHP_SELF'].">\n";
  echo "<input type=hidden name=frmid value=".encrypt_get_vars ($_FORMID).">\n";
}


// ============================================================================================
// Form_End ()
//
// Description:
//   Ends a form within perihelion.
//
// Parameters:
//   none
//
// Returns:
//   none. But outputs html code.
//
function form_end () {
  echo "</form>";
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
//// Encrypt vars on the URL so it's harder to trace them... (like ID's and stuff)
//
function encrypt_get_vars ($var) {
  assert (isset ($var));
  global $_CONFIG; 
  global $_RUN; 
  
  // Generate checksum if not already done so..
  if ($_RUN['current_page_checksum'] == NULL) {
  	$_RUN['current_page_checksum'] = generate_checksum();
  }
  
  // Things get real messy when they are not strings (like ints)
  $var = (string) $var;

  // Mix string with checksum and random chars...
  $var = substr($_RUN['current_page_checksum'], 0, 5). $var . substr($_RUN['current_page_checksum'], 5, 5);
  for ($s="",$i=0; $i!=strlen($var); $i++) $s .= chr(rand (0,255)) . $var[$i];

  // Encrypt the string
  mcrypt_generic_init ($_CONFIG['MCRYPT_TD'], $_CONFIG['MCRYPT_KEY'], $_CONFIG['MCRYPT_IV']);
  $s = mcrypt_generic ($_CONFIG['MCRYPT_TD'], $s);

  // Encode it Bas64 (YUK!)
  for ($i=0; $i!=1; $i++) $s = base64_encode ($s);

  // And return it in a html-friendly way
  return rawurlencode ($s);
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
//// Decrypt vars on the URL so it's harder to trace them... (like ID's and stuff)
//
function decrypt_get_vars ($var) {
  assert (isset ($var));
  global $_CONFIG;
  global $_RUN;
   
    
  // Remove the html-friendly way (%20 stuf etc)
  $var = rawurldecode ($var);

  // Decode it...
  for ($i=0; $i!=1; $i++) $var = base64_decode ($var);

  mcrypt_generic_init ($_CONFIG['MCRYPT_TD'], $_CONFIG['MCRYPT_KEY'], $_CONFIG['MCRYPT_IV']);
  $var = mdecrypt_generic ($_CONFIG['MCRYPT_TD'], $var);

  // Strip annoying mixture
  $s = "";

  if (strlen ($var) % 2 != 0) perihelion_die ("Tampering detected (1)", "Perihelion detected a tampering in the encryption variables..");
  for ($i=0; $i!=strlen($var)/2; $i++) $s .= $var[$i*2+1];


  // Remove the checksum. If it's not a correct checksum, uh oh....
  $checksum_found = substr($s, 0, 5) . substr($s, -5);
  if ($_RUN['previous_page_checksum'] == NULL) {
    $_RUN['previous_page_checksum'] = $checksum_found;
  }
  
  if ($checksum_found != $_RUN['previous_page_checksum']) perihelion_die ("Tampering detected (2)", "Perihelion detected a tampering in the encryption checksum...");

  // Now, strip checksum from variable
  $s = substr($s, 5, -5);

  // And we've got our decoded string
  return $s;
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
//// Format:
//    input_check ( "command1", "!var1", "!var2", "var3", 0,
//                  "command2", "!var1", "var2", 0,
//                  ...
//                );
//
// This function will check if "command1" exists in either $_POST of $_GET and
// if so, it will check all vars for that command. If a var doesn't exists and
// it's mandatory (if it's got a ! in front of the var), then it will error,
// if it's not mandatory, it will make the global var empty.
// If the var is found, it wil decrypt the var and make it a global var.
// If it doesn't need to be decrypted (for instance, in a POST-form where
// people fill in data, we let the var start with NE_.
//
// So: input_check ("blaat", "!ne_bar", "foo", 0)
// means that we have need 'cmd' variable with "blaat",
// and we need a extra variable called "bar", which is
// not encrypted and we have also a "foo" variable, which
// is encrypted, but it's ok, if it's not present.
//
function input_check () {
  $numargs = func_num_args();
  $arg_list = func_get_args();

  if (! validate_request_checksum ()) {
    perihelion_die ("Refresh Error", "You can only submit this form once.");
  }
  
  if (! isset ($_REQUEST['cmd'])) {
    perihelion_die ("", "No command requested.");
    return "";
  }

  // Command is needed. If not present... whooops.
  $cmd = decrypt_get_vars ($_REQUEST['cmd']);
  
  
  // Browse through all numargs, check the command, if it is ours, decrypt all vars EXCEPT
  // the ne_* vars
  $i = 0;
  for (;;) {
    $tmp_cmd = $arg_list[$i];
    $i++;

    if ($tmp_cmd != $cmd) {
      while ($arg_list[$i] != "0") $i++;
    } else {
      while ($arg_list[$i] != "0") {
        $tmp_var = $arg_list[$i];
        $GLOBALS[$tmp_var] = "";

        // Error if we can't find a mandatory var
        if (substr ($tmp_var, 0, 1) == "!") {
          $tmp_var = substr ($tmp_var, 1, 255);
          if (!isset ($_REQUEST[$tmp_var])) perihelion_die ("Internal Error", "Mandatory var not found: ".$tmp_var);
        }

        if (isset ($_REQUEST[$tmp_var])) {
          // Check if we need decrypting or not
          if (substr ($tmp_var, 0, 3) == "ne_") {
            $GLOBALS[$tmp_var] = $_REQUEST[$tmp_var];
          } else {
            $GLOBALS[$tmp_var] = decrypt_get_vars ($_REQUEST[$tmp_var]);
          }
        }
        $i++;
      }
      return $tmp_cmd;

    }
    $i++;
    if ($i >= $numargs) break;
  }

  // No command found :(
  if (user_is_admin (user_ourself())) {
    $str = "Illegal or no command requested.<br>Command issued: '$cmd'";
  } else {
    $str = "Illegal or no command requested.";
  }
  perihelion_die ("Internal Error", $str);
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
//// Initialize the session by setting cookies
function session_init ($info) {  
  session_start ();
  $_SESSION['logged_in'] = 1;
  $_SESSION['user'] = $info;
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
//// Reread the userinfo and set in the cookie
function session_reinit ($tmp) {
  $_SESSION['user'] = $tmp;  
  
  unset ($_SESSION['theme_path']);
  unset ($_SESSION['theme_url']);
  
  session_identification ();
}

// ============================================================================================
// Session_Identification ()
//
// Description:
//   Checks if the session is registered, if not, we jump to the login-screen.
//   It will also check to see if we got enough rights to enter the page as
//   stated in the parameter $flags.
//
// Parameters:
//   string     $flags   CSL with all flags
//
// Returns:
//   Nothing if session is ok.
//   Jumps to the login screen if not logged in.
//   Jumps to the norights screen if we don't have enough rights.
//
function session_identification ($flags = "") {
  global $_GALAXY;
  global $_USER;
  global $_CONFIG;
  global $_RUN;
  
  // Start the session, and check if we are logged in, if not, go to the login page.
  session_start ();
  if (!user_is_logged_in()) {
    session_destroy ();
    header ("Location: ".$_CONFIG['URL']."/login.php");
    exit;
  }

  // Set the userinformation from the session  
  $_USER = $_SESSION['user'];
  
  // Set the users theming path and cache it into a session
  if (! isset($_SESSION['theme_path'])) {
    $result = sql_query ("SELECT * FROM perihelion.t_themes WHERE id=".$_USER['theme_id']);
    if ($row = sql_fetchrow ($result)) {
      $_RUN['theme_path'] = $_CONFIG['TEMPLATE_PATH'] . $row['path'];
      $_RUN['theme_url']  = $_CONFIG['TEMPLATE_URL']  . $row['path'];
      $_SESSION['theme_path'] = $_RUN['theme_path'];
      $_SESSION['theme_url'] = $_RUN['theme_url'];
    }
  } else {
 	
    $_RUN['theme_path'] = $_SESSION['theme_path'];
    $_RUN['theme_url']  = $_SESSION['theme_url'];
  }
  	
  

  // Get the galaxy configuration for this user, pretty shitty, should store this info in a session?
  $result  = sql_query ("SELECT * FROM ".$_USER['galaxy_db'].".c_config LIMIT 1");
  $_GALAXY  = sql_fetchrow ($result);

  // We now know which database we use by default, so make sure we do so...
  sql_query ("USE ".$_USER['galaxy_db']);

  if ($flags == "") return;


  // Check for the flags we need to enter, if we don't have the flags
  // it means we can't enter the page.
  $userflags = split (",", $_USER['flags']);
  $wantflags = split (",", $flags);
  foreach ($wantflags as $flags) {
    if (! in_array ($flags, $userflags)) {
      header ("Location: ".$_CONFIG['URL']."/norights.php");
      exit;
    }
  }
}


?>
