<?php

$score_generated = false;
$score = array ();

// ============================================================================================
function score_generate_scoring_table () {
  global $score_generated;
  global $score;
  
  $tables = array ("resource", "exploration", "strategic", "overall");
  foreach ($tables  as $table) {
    $result = sql_query ("SELECT u.id AS user_id, u.name, s.$table AS points FROM perihelion.u_users AS u, perihelion.u_score AS s WHERE u.id = s.user_id ORDER  BY s.$table DESC");
    $i = 0;
    while ($row = sql_fetchrow ($result)) {
      $i++;
      $score[$table][$i] = $row;
    }
  }
  
  $score_generated = true;
}

// ============================================================================================
function score_get_user_rank ($table, $user_id) {
  assert (is_numeric ($user_id));
  assert (is_string ($table));
  global $score_generated;
  global $score;
  
  if ($score_generated == false) score_generate_scoring_table();

  $rank = 0;
  $rowid = 1;
  foreach ($score[$table] as $row) {
    if ($row['user_id'] == $user_id) {
      $rank = $rowid;
      break;
    }
    $rowid++;
  }

  return $rank;
}

// ============================================================================================
function score_get_rank ($table, $rank) {
  assert (is_string ($table));
  assert (is_numeric ($rank));
  global $score_generated;
  global $score;
  if ($score_generated == false) score_generate_scoring_table();

  if (! array_key_exists ($rank, $score[$table])) return array(0, "", 0);


  $user_id = $score[$table][$rank]['user_id'];
  $name    = $score[$table][$rank]['name'];
  $points  = $score[$table][$rank]['points'];

  return array ($user_id, $name, $points);
}

// ============================================================================================
function score_get_last_rank () {
  global $score_generated;
  global $score;
  if ($score_generated == false) score_generate_scoring_table();

  return count($score['resource']);
}

// ============================================================================================
function score_get_first_rank () {
  global $score_generated;
  global $score;
  if ($score_generated == false) score_generate_scoring_table();

  return 1;
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
function score_showuser ($user_id) {
  assert (is_numeric ($user_id));

  $tables = array ("resource", "exploration", "strategic", "overall");
  foreach ($tables  as $table) {
    // Get the current ranking of the user
    $rank = score_get_user_rank ($table, $user_id);

    // Get the first rank
    list ($uid, $name, $points) = score_get_rank ($table, 1);
    $ranktable['1'][$table] = "<td>&nbsp;1.&nbsp;</td><td>&nbsp;".$name."&nbsp;</td><td>&nbsp;(".$points." points)&nbsp</td>";

    if ($rank == score_get_first_rank()) {
      $ranktable['2'][$table] = "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
    } else {
      list ($uid, $name, $points) = score_get_rank ($table, $rank-1);
      $ranktable['2'][$table] = "<td>&nbsp;".($rank-1).".&nbsp;</td><td>&nbsp;".$name."&nbsp;</td><td>&nbsp;(".$points." points)&nbsp</td>";
    }

    list ($uid, $name, $points) = score_get_rank ($table, $rank);
    $ranktable['3'][$table] = "<td>&nbsp;<b>".$rank.".</b>&nbsp;</td><td>&nbsp;<b>".$name."</b>&nbsp;</td><td>&nbsp;<b>(".$points." points)</b>&nbsp</td>";

    if ($rank == score_get_last_rank ()) {
      $ranktable['4'][$table] = "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
    } else {
      list ($uid, $name, $points) = score_get_rank ($table, $rank+1);
      $ranktable['4'][$table] = "<td>&nbsp;".($rank+1).".&nbsp;</td><td>&nbsp;".$name."&nbsp;</td><td>&nbsp;(".$points." points)&nbsp</td>";
    }
  }


    echo "<table border=0 width=75% align=center>";
    echo "  <tr class=wb><th colspan=2>Rankings</th></tr>";
    echo "  <tr><td>";
        echo "<table width=100% border=0>";
        echo "  <tr class=bl><th colspan=4><a href=score.php?cmd=".encrypt_get_vars ("show")."&tbl=".encrypt_get_vars ("resource").">Resource Ranking:</a></th></tr>";
        echo "  <tr class=bl>".$ranktable['1']['resource']."</tr>";
        echo "  <tr class=lbl>".$ranktable['2']['resource']."</tr>";
        echo "  <tr class=bl>".$ranktable['3']['resource']."</tr>";
        echo "  <tr class=lbl>".$ranktable['4']['resource']."</tr>";
        echo "</table>";
    echo "</td><td>";
        echo "<table width=100% border=0>";
        echo "  <tr class=bl><th colspan=4><a href=score.php?cmd=".encrypt_get_vars ("show")."&tbl=".encrypt_get_vars ("exploration").">Exploration Ranking:</a></th></tr>";
        echo "  <tr class=bl>".$ranktable['1']['exploration']."</tr>";
        echo "  <tr class=lbl>".$ranktable['2']['exploration']."</tr>";
        echo "  <tr class=bl>".$ranktable['3']['exploration']."</tr>";
        echo "  <tr class=lbl>".$ranktable['4']['exploration']."</tr>";
        echo "</table>";
    echo "</td></tr>";
    echo "<tr><td>";
        echo "<table width=100% border=0>";
        echo "  <tr class=bl><th colspan=4><a href=score.php?cmd=".encrypt_get_vars ("show")."&tbl=".encrypt_get_vars ("strategic").">Strategic Ranking:</a></th></tr>";
        echo "  <tr class=bl>".$ranktable['1']['strategic']."</tr>";
        echo "  <tr class=lbl>".$ranktable['2']['strategic']."</tr>";
        echo "  <tr class=bl>".$ranktable['3']['strategic']."</tr>";
        echo "  <tr class=lbl>".$ranktable['4']['strategic']."</tr>";
        echo "</table>";
    echo "</td><td>";
        echo "<table width=100% border=0>";
        echo "  <tr class=bl><th colspan=4><a href=score.php?cmd=".encrypt_get_vars ("show")."&tbl=".encrypt_get_vars ("overall").">Overall Ranking:</a></th></tr>";
        echo "  <tr class=bl>".$ranktable['1']['overall']."</tr>";
        echo "  <tr class=lbl>".$ranktable['2']['overall']."</tr>";
        echo "  <tr class=bl>".$ranktable['3']['overall']."</tr>";
        echo "  <tr class=lbl>".$ranktable['4']['overall']."</tr>";
        echo "</table>";
    echo "</td></tr>";
    echo "</table>";
    echo "<br><br>";
}

?>
