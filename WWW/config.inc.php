<?php
//    // ================================================================================
//    // Sneaky defines. This way we can threat $_CONFIG as a superglobal.
//    define ("_CONFIG",   "GLOBALS['CONFIG']");
//    define ("_USERINFO", "GLOBALS['userinfo']");
//    define ("_GALAXY",   "GLOBALS['galaxy_config']");

    // ================================================================================
    // MySQL options
    $_CONFIG['MYSQL_HOST']       =   "localhost";
    $_CONFIG['MYSQL_USER']       =   "px185251";
    $_CONFIG['MYSQL_PASS']       =   "Cid%yld0+Vok5";
    $_CONFIG['MYSQL_DATABASE']   =   "perihelion";

    $_CONFIG['COMM_HOST']        =   "127.0.0.1";
    $_CONFIG['COMM_PORT']        =   10080;
    $_CONFIG['COMM_USER']        =   "project_x";
    $_CONFIG['COMM_PASS']        =   "P190401X";
       
    $_CONFIG['PATH']						 =   "/home/joshua/WWW";
    $_CONFIG['URL']              =   "http://62.195.19.164/perihelion";
    
    $_CONFIG['TEMPLATE_PATH']    =   $_CONFIG['PATH']."/themes";
    $_CONFIG['TEMPLATE_URL']     =   $_CONFIG['URL']."/themes";    
    $_CONFIG['IMAGE_URL']        =   $_CONFIG['URL']."/images";
    $_CONFIG['HELP_URL']         =   $_CONFIG['URL']."/help";
    
    // Should we parse our dynamic pages through w3c.org's validation engine?
    $_CONFIG['validate_pages']	 = false;
    
		// ================================================================================
		$_CONFIG['DEFAULT_THEME']    = '/Perihelion';
    
    // ================================================================================
    // Parser Routine
    //$_CONFIG['OB_PARSER'] = "ob_nonewline";
    //$_CONFIG['OB_PARSER'] = "ob_tidyhandler";
    $_CONFIG['OB_PARSER'] = "";

    // ================================================================================
    // This value decides how many sql querie results are cached by perihelion.
    // $_CONFIG['SQL_QUERY_CACHE']  =  5;

    // ================================================================================
    // Key for encrypting the get-data
    $_CONFIG['MCRYPT_KEY'] = "8a1c6a92d53f2a219390fac4761b93b1";
    $_CONFIG['MCRYPT_IV']  = md5("8a1c6a92d53f2a219390fac4761b93b1");
    $_CONFIG['MCRYPT_TD']  = mcrypt_module_open ('rijndael-256', '', 'ofb', '');
    
    // ================================================================================
    // How many scores can we view at 1 page in score.php
    $_CONFIG['SCORE_VIEWSIZE'] = 50;
    
    
    // Maximum number of seconds a user can be idle before it's not visible in the 
    // who is online list.
    $_CONFIG['MAX_SECONDS_IDLE'] = 300000000;
    
    

// ================================================================================
//           *** NO MORE EDITABLE DEFINES BEHIND THIS LINE ***
// ================================================================================

    // ========================================================================================
    // The _RUN array is an array which holds all kind of variables used in 1 run. After the
    // run it is cleared. This is basicly the same as the _CONFIG array, only data in here
    // will be changed or generated during the run, not on forehand.  
    $_RUN = array();
    $_RUN['current_page_checksum']   = NULL;
    $_RUN['previous_page_checksum']  = NULL;
    
    // Set default theming
    $_RUN['theme_path'] = $_CONFIG['TEMPLATE_PATH'] . $_CONFIG['DEFAULT_THEME'];
    $_RUN['theme_url']  = $_CONFIG['TEMPLATE_URL']  . $_CONFIG['DEFAULT_THEME'];
   
    $_RUN['DATABASE_HANDLE'] = 0;               // 0 means we don't have a handle
    //$_RUN['DATABASE_CURRENTDB'] = "";         // Name of the current database (galaxy)

    $_RUN['logout_recorded'] = false;           // When true, this connection has set the logout time in the
                                                // database to the current time. This only need to be done
                                                // once each 'run'.
    $_RUN['user_is_admin'] = -1;                // If 0, we are no admin, if 1 we are admin. If -1, we still
                                                // don't know what the user is.


    // ================================================================================
    // Assertion options
    assert_options (ASSERT_ACTIVE, 1);
    assert_options (ASSERT_BAIL, 1);
    assert_options (ASSERT_CALLBACK, "my_assert_handler");

    // We like to show all errors please
    error_reporting (E_ALL);

    // Other stuff we can control... sort of..
    ob_start ($_CONFIG['OB_PARSER']);   // Output everything through our parser.
    ob_implicit_flush("off");           // This saves AAAAAAALLLLLOOOOOTTTTTTTTT of time....
    set_time_limit(10);                 // 10 seconds is the maximum for a page to generate.


  // ========================================================================================
  function my_assert_handler($file, $line, $code) {
    if (! function_exists ("debug_backtrace")) {
    	echo "<b>Warning: Backtracing is not supported by this PHP version.</b><br>\n";
    	return;
    }
    
    $backtrace = debug_backtrace();
    $backtrace = array_reverse ($backtrace);
    array_pop ($backtrace);
    array_pop ($backtrace);

    echo "<br><br><br>\n";
    echo "<table border=0 width=80%>\n";
    echo "<tr class=wb><th colspan=3>A S S E R T &nbsp;&nbsp;&nbsp; I N F O R M A T I O N</th></tr>";
    echo "<tr class=bl>";
    echo "<th>Source</th>";
    echo "<th>Func</th>";
    echo "<th>Params</th>";
    echo "</tr>";

    foreach ($backtrace as $func) {
      echo "<tr class=bl>";
      if (array_key_exists ('file', $func)) {
        echo "<td>&nbsp;".basename($func['file'])." (".$func['line'].")&nbsp;</td>";
      } else {
        echo "<td>&nbsp;&nbsp;</td>";
      }
      echo "<td>&nbsp;".$func['function']."&nbsp;</td>";
      echo "<td>&nbsp;";
      foreach ($func['args'] as $arg) echo "$arg, ";
      echo "&nbsp;</td>";
      echo "</tr>";
    }
    echo "</table>\n";
  }

?>
