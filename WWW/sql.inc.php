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
// Connects to the default MySQL Database or to the user defined database, or
// just return when we are already connected.
function sql_connect () {
  global $_CONFIG;
  global $_USER;
  global $_RUN;

  if ($_RUN["DATABASE_HANDLE"] != 0) return;


  $_RUN["DATABASE_HANDLE"] = mysql_pconnect ($_CONFIG["MYSQL_HOST"], $_CONFIG["MYSQL_USER"], $_CONFIG["MYSQL_PASS"]) or sql_error_connect ();
  if (! isset ($_USER['galaxy_db'])) {
    $ret = @mysql_select_db ($_CONFIG["MYSQL_DATABASE"], $_RUN["DATABASE_HANDLE"]) or perihelion_die ("SQL Error", "Can't set default database!");
  } else {
    $ret = @mysql_select_db ($_USER['galaxy_db'], $_RUN["DATABASE_HANDLE"]) or perihelion_die ("SQL Error", "Galaxy database ".$userdata['galaxy_db']." does not exist!");
  }

  return $_RUN["DATABASE_HANDLE"];
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
function obsolete_sql_select_db ($database) {
  if ($database == "") $database = $_RUN["DATABASE_CURRENTDB"];

  @mysql_select_db ($database, $_RUN["DATABASE_HANDLE"]);
  @mysql_query ("SELECT * FROM ".$database.".c_config", $_RUN["DATABASE_HANDLE"]);
  $_RUN["DATABASE_CURRENTDB"] = $database;
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
function sql_query ($query, $requirement = MULTIPLE_ALLOWED) {
  assert (!empty($query));
  assert (!is_int($requirement));
  global $_USER;
  global $_RUN;

  // Connect to the database, if we didn't do that already...
  if ($_RUN["DATABASE_HANDLE"] == 0) sql_connect ();


  // This block will update the logout time of the user. basicly, it records every query and places in the logout data the current time.
  // Since we don't have to do it for every query (that takes a lot of time), we only do it for the first one. This way we still
  // get a pretty accurate logout time.
  if (session_id() and $_RUN['logout_recorded']==false) {
    $_RUN['logout_recorded'] = true;
    @mysql_query ("UPDATE perihelion.u_access SET logout=NOW() WHERE php_session_id LIKE '".session_id()."'", $_RUN["DATABASE_HANDLE"]);
  }

  // TODO: Make the queries safe. Eg: no sql injections et al...
  $safe_query = sql_checksafe($query);
  if ($safe_query == "") perihelion_die ("Unsafe query detected!", "Perihelion detected an unsafe query. Possible SQL injection detected.");

  // Check if we are administrator or not, if so, we get more info when a query does not function.
  if ($_RUN['user_is_admin'] == -1 or $_RUN['user_is_admin'] == 0) {
    $_RUN['user_is_admin'] = 0;
    if (session_id()) {
      if (in_array (USERFLAG_ADMIN, split (",", $_USER['flags']))) {
        $_RUN['user_is_admin'] = 1;
      }
    }
  }

  // give more info when it fails when we are admin.
  if ($_RUN['user_is_admin']) {
    $error_str = "<br><li><b>Query: </b>$query<br><li><b>Error: </b>".mysql_error($_RUN["DATABASE_HANDLE"]);
  } else {
    $error_str = "Please notify a Perihelion administrator.";
  }

  // Do the query...
  $result = @mysql_query ($safe_query, $_RUN["DATABASE_HANDLE"]) or perihelion_die ("SQL Error", "Query did not succeed. ".$error_str);

  // Now, check the results and see if they meet the requirements...
  if ($requirement == NO_NULL_ALLOWED) {
    $rows = mysql_num_rows ($result);
    if ($rows == 0) perihelion_die ("SQL Error", "<b>A query excepted at least 1 item, but returned nothing.</b>".$error_str);
  }
  if ($requirement == JUST_ONE_ALLOWED) {
    $rows = mysql_num_rows ($result);
    if ($rows != 1) perihelion_die ("SQL Error", "<b>A query excepted only 1 item, but returned none or more than 1 (".$rows.").</b>".$error_str);
  }
  if ($requirement == NULL_OR_ONE_ALLOWED) {
    $rows = mysql_num_rows ($result);
    if ($rows != 0 or $rows != 1) perihelion_die ("SQL Error", "<b>A query excepted zero or 1 item, but returned more than 1 (".$rows.").</b>".$error_str);
  }

  if ($requirement == MULTIPLE_ALLOWED) {
    // Everything is ok for multiple allowed
  }


  return $result;
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
function sql_countrows  ($result) {
    assert (!empty($result));
    return @mysql_num_rows ($result);
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
function sql_fetchrow ($result, $override_row_check=false) {
    assert (!empty($result));
    assert (is_bool ($override_row_check));

    if ($override_row_check == true && sql_countrows ($result) == 0) {
    	perihelion_die ("Empty Record Set", "Perihelion found an empty record set.");
    }
    return @mysql_fetch_array ($result);
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
function sql_error_connect () {
    perihelion_die ("SQL Error", "The SQL server seems to be offline. Please try again...");
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
/****************************************************************************************************
 * Caching function for state_id, speeds up sector.php a little.
 */
$state_array = 0;
function sql_get_state ($sid) {
  global $state_array;

  if ($state_array == 0) {
    $state_array = array();
    $result2 = sql_query ("SELECT * FROM s_state");
    while ($state   = sql_fetchrow ($result2)) {
      $i = $state['id'];
      $state_array[$i] = $state['name'];
    }
  }

  return $state_array[$sid];
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
function sql_checksafe ($query) {
	assert (is_string ($query));

  // Quotes are automaticly	escaped.


  // Check for "OR <val>=<val>"
  if (preg_match ("/or (.+)=\\1[; #]/i", $query) == 1) return "";

  // Check for "OR 1"
  if (preg_match ("/or 1/i", $query) == 1) return "";


	return $query;
}

?>
