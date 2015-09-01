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
function anomaly_am_i_owner ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);

  if (user_ourself() == $anomaly['user_id']) return true;
  return false;
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
function anomaly_is_discovered_by_user ($anomaly_id, $user_id) {
  assert (is_numeric ($anomaly_id));
  assert (is_numeric ($user_id));

  $tmp = user_get_all_anomalies_from_user ($user_id);
  $undiscovered_anomalies = csl ($tmp['csl_undiscovered_id']);
  $discovered_anomalies = csl ($tmp['csl_discovered_id']);

  if (in_array ($anomaly_id, $discovered_anomalies)) return true;
  return false;
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
$cache_aga = 0;
function anomaly_get_anomaly ($anomaly_id) {
  assert (is_numeric ($anomaly_id));
  global $cache_aga;

  // Check if we want info for the last userid (most of the time this is true)
  if ($cache_aga == 0 or $anomaly_id != $cache_aga['id']) {
    $result = sql_query ("SELECT * FROM s_anomalies WHERE id=".$anomaly_id);
    $tmp    = sql_fetchrow ($result);

    $cache_aga = array();
    $cache_aga['id'] = $anomaly_id;
    $cache_aga['query'] = $tmp;
    return $tmp;
  }

  // Return cached information
  return $cache_aga['query'];
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
function anomaly_is_planet ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);
  if ($anomaly['type'] == ANOMALY_PLANET) return true;
  return false;
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
function anomaly_is_nebula ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);
  if ($anomaly['type'] == ANOMALY_NEBULA) return true;
  return false;
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
function anomaly_is_wormhole ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);
  if ($anomaly['type'] == ANOMALY_WORMHOLE) return true;
  return false;
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
function anomaly_is_blackhole ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);
  if ($anomaly['type'] == ANOMALY_BLACKHOLE) return true;
  return false;
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
function anomaly_is_starbase ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);
  if ($anomaly['type'] == ANOMALY_STARBASE) return true;
  return false;
}

?>