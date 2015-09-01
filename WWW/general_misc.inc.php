<?php

// ============================================================================================
// GetMicroTime ()
//
// Description:
//   Returns the current time since epoch
//
// Parameters:
//    None
//
// Returns:
//    float   Current time in seconds
//
function getmicrotime () {
  list ($usec, $sec) = explode (" ", microtime());
  return ((float)$usec + (float)$sec);
}


// ============================================================================================
// Info_Get_Stardate ()
//
// Description:
//    Returns the stardate
//
// Parameters:
//    None
//
// Returns:
//  string    stardate from the galaxy
//
function info_get_stardate () {
  $result = sql_query ("SELECT * FROM g_stardate");
  $row    = sql_fetchrow ($result);

  $stardate = $row['stardate'];
  return $stardate;
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
// =======================================================================================
// Makes an array of a comma-separated TEXT-column. All
// rows in the result are used.
function csl_create_array ($result, $colnumber) {
  assert (isset ($result));
  assert (isset ($colnumber));

  // Make array
  $tmp = array ();

  // Explode all rows into the tmp-array
  while ($row = sql_fetchrow ($result)) {

    // If it's not an empty string, place a comma after the string if there isn't one already...
    $char = substr ($row[$colnumber], -1);
    if ($row[$colnumber] != "" and $char != ",") {
      $row[$colnumber] = $row[$colnumber] . ",";
    }
    $tmp = array_merge ($tmp, explode(",", $row[$colnumber]));
  }

  // This function makes sure we start at row 0 again. This is needed
  // so we can make a second call to explode_array without having to
  // do a whole query again...
  @mysql_data_seek ($result, 0);

  // Since last char is a comma, last entry after explode is bogus
//  echo "CSL_CREATE_ARRAY BEFORE: "; print_r($tmp); echo "<br>\n";
  if (end($tmp) == "") array_pop ($tmp);
  reset ($tmp);
//  echo "CSL_CREATE_ARRAY AFTER : "; print_r($tmp); echo "<br>\n";

  // And return
  return $tmp;
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
// =======================================================================================
// Merges a csl from $new into $org
function csl_merge_fields ($org, $new) {
  return array_merge ($org, csl ($new));
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
// =======================================================================================
// Converts a comma seperated string into an array
function csl ($str) {
  $tmp = split (",", $str);

  // If the last one is empty.. remove it..
  if ($tmp[count($tmp)-1] == "") array_pop ($tmp);

  return $tmp;
}



?>
