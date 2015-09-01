<?php
   // Include Files
   include "includes.inc.php";

   // Session Identification
   session_identification();

   print_header ();
   print_title ("Vessel creation");

   $cmd = input_check ("showaid", "aid", 0);

   if ($cmd == "showaid") {
     if ($aid == "") $aid = user_get_home_planet (user_ourself());
     show_possible_vessels_on_planet ($aid);
   }

   print_footer ();
   exit;



/*
    // can we build ships already?
    if ($user['impulse'] == 0) {
      print_line ("You cannot build ships yet");
      print_footer ();
      exit;
    }

   // Show homeworld when nothing is set...
   if (!isset ($pid)) {
     $user   = user_get_user ($_USER['id']);
     $planet = anomaly_get_anomaly ($user['planet_id']);
     if (planet_has_vesselbuilding_capability ($planet)) {
       show_vessels ($_USER, $user['planet_id']);
     }
   } else {
     $planet = anomaly_get_anomaly ($pid);
     if (planet_has_vesselbuilding_capability ($planet)) {
       show_vessels ($_USER, $pid);
     }
   }
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
function show_possible_vessels_on_planet ($anomaly_id) {
  assert (is_numeric ($anomaly_id));

  $anomaly = anomaly_get_anomaly ($anomaly_id);

  // No construction possible when we don't have the right buildings...
  if (! planet_has_vesselbuilding_capability ($anomaly_id)) {
    print_subtitle ("Planet ".$anomaly['name']." has no vessel construction capability.");
    create_submenu ( array (
                     "Planet view" => "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly_id),
                     "Surface view" => "surface.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($anomaly_id),
                     )
                   );
    return;
  }


  print_subtitle ("Vessel construction on planet ".$anomaly['name']);

  $user    = user_get_user ($anomaly['user_id']);
  $result = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$anomaly_id);
  $ores   = sql_fetchrow ($result);

  $result = sql_query ("SELECT * FROM s_vessels ORDER BY id");
  while ($vessel = sql_fetchrow ($result)) {
    $cannot_build = true;

    // Can we build it?
    if ($vessel['build_level'] <= $user['vessel_level']) $cannot_build = false;

    if ($cannot_build == false) {
      vessel_show_type_details ($vessel['id'], $anomaly_id, $anomaly['user_id'], $ores['stock_ores']);
    }
  }
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
function obsolete_show_vessels ($planet_id) {
  assert (!empty($planet_id));
  global $_USER;

  // Get global information from the user
  $user = user_get_user ($_USER['id']);
  $planet = anomaly_get_anomaly ($planet_id);

  // And get the ores from the planet
  $result = sql_query ("SELECT * FROM g_ores WHERE planet_id=".$planet_id);
  $ores = sql_fetchrow ($result);
  $stock_ores = ore_csl_to_list ($ores['stock_ores']);

  print_subtitle ("Create vessel on planet ".$planet['name']);

  // And get all buildings, compare wether or not we may build them...
  $result = sql_query ("SELECT * FROM s_vessels ORDER BY id");
  while ($vessel = sql_fetchrow ($result)) {
    // Default, we can build this
    $cannot_build = false;

// Stage 3: Show building if we can build it..
    if ($cannot_build == false) {
      show_vessel_table ($vessel, $user, $stock_ores);
    }
  }
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
function obsolete_show_vessel_table ($s_vessel, $user, $stock_ores) {
  assert (!empty ($s_vessel));
  assert (!empty ($user));
  assert (!empty ($stock_ores));

   global $_GALAXY;
   $cannot_build = false;


    echo "<table border=1 cellpadding=0 cellspacing=0 align=center width=50%>";

// Vessel name
    echo "  <tr><th colspan=2>".$s_vessel['name']."</th></tr>";

// Plaatje plus ADS + impulse + warp
    echo "  <tr>";
    echo "    <td align=center valign=top bgcolor=black>";
    echo "              <table border=0 cellpadding=0 cellspacing=0>";
    echo "                <tr>";
    echo "                   <td >";
    echo "                    <table align=left border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "                      <tr><td width=100><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/vessels/".$s_vessel['image'].".jpg\" width=150 height=150></td></tr>";
    echo "                    </table>";
    echo "                 </td>";
    echo "               </tr>";
    echo "             </table>";
    echo "    </td>";
    echo "    <td align=left valign=top bgcolor=black>";
    echo "             <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    $class = 't';
    echo "               <tr>";
    echo "                 <td class=".$class.">&nbsp;<strong>Attack</strong>&nbsp;</td>";
    echo "                 <td class=".$class."> &nbsp;<strong>:</strong>&nbsp;</td>";
    echo "                 <td class=".$class.">&nbsp;".$s_vessel['attack']."&nbsp;</td>";
    echo "               </tr>";
    echo "               <tr>";
    echo "                 <td class=".$class.">&nbsp;<strong>Defense</strong> &nbsp;</td>";
    echo "                 <td class=".$class."> &nbsp;<strong>:</strong>     &nbsp;</td>";
    echo "                 <td class=".$class.">&nbsp;".$s_vessel['defense']."    &nbsp;</td>";
    echo "               </tr>";
    echo "               <tr>";
    echo "                 <td class=".$class.">&nbsp;<strong>Strength</strong>&nbsp; </td>";
    echo "                 <td class=".$class."> &nbsp;<strong>:</strong>     &nbsp;</td>";
    echo "                 <td class=".$class.">&nbsp;".$s_vessel['strength']."   &nbsp; </td>";
    echo "               </tr>";
    echo "               <tr><td colspan=3><hr></td></tr>";
    $class = 't';
    echo "               <tr>";
    echo "                 <td class=".$class.">&nbsp;<strong>Impulse</strong> &nbsp;</td>";
    echo "                 <td class=".$class."> &nbsp;<strong>:</strong>     &nbsp;</td>";
    echo "                 <td class=".$class.">&nbsp;".$s_vessel['max_impulse']." %&nbsp;</td>";
    echo "               </tr>";
    echo "               <tr>";
    echo "                 <td class=".$class.">&nbsp;<strong>Warp</strong> &nbsp;</td>";
    echo "                 <td class=".$class."> &nbsp;<strong>:</strong>     &nbsp;</td>";
    echo "                 <td class=".$class.">&nbsp;".number_format($s_vessel['max_warp'] / 10, 1)." &nbsp;</td>";
    echo "               </tr>";
    echo "             </table>";
    echo "    </td>";
    echo "  </tr>";

// Costs + ores  (initial / upkeep)
    echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";
    echo "  <tr bgcolor=black><td>";
    $cannot_build = svt_initial_ores ($cannot_build, $s_vessel, $user, $stock_ores);
    echo "  </td><td>";
    $cannot_build = svt_upkeep_ores ($cannot_build, $s_vessel, $user, $stock_ores);
    echo "  </td></tr>";
    echo "  <tr bgcolor=black><td colspan=2>&nbsp;</td></tr>\n";

// Print description
    if ($s_vessel['description'] != "") {
      echo "<tr><td colspan=2><table border=0 cellspacing=5><tr><td>".$s_vessel['description']."</td></tr></table></td></tr>";
    }

// Print building possibility
    if ($cannot_build == false) {
        echo "<tr><th colspan=2><a href=vesselcreate.php?vid=".encrypt_get_vars ($s_vessel['id']).
                                                    ">BUILD IT</a></th></tr>";
    } else {
      echo "<tr><th colspan=2>CANNOT BUILD</th></tr>";
    }


    echo "</table>\n";
    echo "<br><br>\n";
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
function obsolete_svt_initial_ores ($cannot_build, $s_vessel, $user, $stock_ores) {
  assert (is_numeric ($cannot_build));
  assert (is_array ($s_vessel));
  assert (is_array ($user));
  assert (is_array ($stock_ores));

  $vessel_ores = ore_csl_to_list ($s_vessel['initial_ores']);

  echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "  <tr><th colspan=3>Initial costs</th></tr>";

  if ($s_vessel['initial_costs'] > $user['credits']) {
    $class="f";
    $cannot_build = true;
  } else {
    $class="t";
  }
  echo "  <tr>\n";
  echo "    <td class=".$class." width=33%> &nbsp;<strong>Credits</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=1%>  &nbsp;<strong>:</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=34%> &nbsp;".$s_vessel['initial_costs']."&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    if ($vessel_ores[$i] > $stock_ores[$i]) {
      $class="f";
      $cannot_build = true;
    } else {
      $class="t";
    }
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$vessel_ores[$i]."&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";

  return $cannot_build;
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
function obsolete_svt_upkeep_ores ($cannot_build, $s_vessel, $user, $stock_ores) {
  assert (is_numeric ($cannot_build));
  assert (is_array ($s_vessel));
  assert (is_array ($user));
  assert (is_array ($stock_ores));

  $vessel_upkeep_ores = ore_csl_to_list ($s_vessel['upkeep_ores']);

  $class="t";
  echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "  <tr><th colspan=3>Upkeep costs</th></tr>";
  echo "  <tr>\n";
  echo "    <td class=".$class." width=33%> &nbsp;<strong>Credits</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=1%>  &nbsp;<strong>:</strong>&nbsp;</td>\n";
  echo "    <td class=".$class." width=34%> &nbsp;".$s_vessel['upkeep_costs']."&nbsp;</td>\n";
  echo "  </tr>\n";

  // Do all ores
  for ($i=0; $i != ore_get_ore_count(); $i++) {
    echo "  <tr>\n";
    echo "    <td class=".$class." width=33%>&nbsp;<strong>".ore_get_ore_name($i)."</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=1%> &nbsp;<strong>:</strong>&nbsp;</td>\n";
    echo "    <td class=".$class." width=34%>&nbsp;".$vessel_upkeep_ores[$i]."&nbsp;</td>\n";
    echo "  </tr>\n";
  }
  echo "</table>\n";

  return $cannot_build;
}

?>
