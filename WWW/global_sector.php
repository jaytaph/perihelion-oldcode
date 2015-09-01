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
$cache_sga= 0;
function sector_get_anomalies ($sector_id) {
  assert (isset ($sector_id));
  global $cache_sga;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_sga == 0 or $sector_id != $cache_sga['id']) {
    $result = sql_query ("SELECT * FROM s_anomalies WHERE sector_id=".$sector_id." ORDER BY DISTANCE");
    $tmp    = sql_fetchrow ($result);

    $cache_sga = array();
    $cache_sga['id'] = $sector_id;
    $cache_sga['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_sga['query'];
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
$cache_sgs = 0;
function sector_get_sector ($sector_id) {
  assert (isset ($sector_id));
  global $cache_sgs;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_sgs == 0 or $sector_id != $cache_sgs['id']) {
    $result = sql_query ("SELECT * FROM s_sectors WHERE id=".$sector_id);
    $tmp    = sql_fetchrow ($result);

    $cache_sgs = array();
    $cache_sgs['id'] = $sector_id;
    $cache_sgs['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_sgs['query'];
}


?>