<?php

/*
 * Get the number of ores currently present in the galaxy (mostly 6)
 */
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
function ore_get_ore_count () {
  global $_CONFIG;
  return count ($_CONFIG['ore_names']);
}

/*
 * Get the name of the ore of index (xellium, augon etc)
 */
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
function ore_get_ore_name ($index) {
  assert ($index >= 0);
  assert ($index <= ore_get_ore_count());
  global $_CONFIG;
  return $_CONFIG['ore_names'][$index];
}

/*
 * Generates an array from a comma separated list and pads it with 0,
 */
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
function ore_csl_to_list ($csl = ",") {
  $array = array();

//  echo "CSL: $csl<br>\n";
  // If it's not an empty string, place a comma after the string if there isn't one already...
  $char = substr ($csl, -1);
  if ($csl != "" and $char != ",") {
    $csl = $csl . ",";
  }

  $array = array_merge ($array, explode(",", $csl));
  array_pop ($array);

  // Make sure it's at least 6 items big, pad it out with zero's if we must...
  while (count ($array) <= ore_get_ore_count ()) array_push ($array, 0);

  return $array;
}

/*
 * Generates an array from a comma separated list
 */
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
function ore_list_to_csl ($list) {
  assert ( is_array ($list));

  $csl = implode (",", $list);
  $csl = $csl . ",";

  return $csl;
}

/*
 * Removes an ore from the list.
 */
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
function ore_remove_from_list ($list, $index) {
  assert ( is_array ($list));
  assert ( ! empty ($index));

  $deleted_value = $list[$index];
  $list[$index] = 0;

//  Don't unset it, this will break the ore-index in the array, just set it to 0
//  unset ($list[$index]);

  return $deleted_value;
}

/*
 * Adds an ore to the list
 */
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
function ore_add_to_list ($list, $index, $ore) {
  assert ( is_array ($list));
  assert ( ! empty ($index));
  assert ( ! empty ($ore));

  $added_value = $ore;
  $list[$index] = $ore;

  return $added_value;
}

/*
 * Replace ore in list (preffered method of setting ores)
 */
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
function ore_replace_in_list ($list, $index, $ore) {
  assert ( is_array ($list));
  assert ( ! empty ($index));
  assert ( ! empty ($ore));

  $replaced_value = $list[$index];
  $list[$index] = $ore;

  return $replaced_value;
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
// Find an ore in the list (like: ore_fin_in_list ($ores, "12345")?)
function ore_find_in_list ($list, $ore) {
  assert ( is_array ($list));
  assert ( ! empty ($ore));

  $index = array_search ($ore, $list);

  if ($index === false) return -1;
  return $index;
}


?>