<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Planet stuff");

    $cmd = input_check ("show", "sid", "uid", 0);

    if ($cmd == "show") {
      if ($sid == "") {
        if ($uid == "") $uid = user_ourself();
        conview_show_all_sectors ($uid);
      } else {
        conview_show_sector ($sid);
      }
    }

    print_footer ();
    exit;

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
function conview_show_all_sectors ($user_id) {
  assert (is_numeric ($user_id));

  // Get all sectors that we can see
  $result = sql_query ("SELECT * FROM g_sectors WHERE user_id=".$user_id);
  $sectors = csl_create_array ($result, "csl_sector_id");

  $user      = user_get_user ($user_id);
  $tmp       = user_get_all_anomalies_from_user ($user_id);
  $anomalies = csl ($tmp['csl_discovered_id']);

  foreach ($sectors as $sector_id) {
    conview_show_sector ($user_id, $sector_id, $anomalies);
    exit;
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
function obsolete_conview_show_sector ($user_id, $sector_id, $planets) {
  assert (is_numeric ($user_id));
  assert (is_numeric ($sector_id));
  assert (is_array ($planets));

  // Check how many planets we own in this sector. If none, don't show anything...
  $result = sql_query ("SELECT COUNT(*) AS count FROM s_anomalies WHERE sector_id = ".$sector_id." AND user_id=".$user_id);
  $tmp= sql_fetchrow ($result);
  if ($tmp['count'] == 0) return;

  // Get sector information
  $sector = sector_get_sector ($sector_id);

  // Only show a table when we have rows, which means: create table on printing the first row
  $first_row = true;

  // Get planet information for all planets in the sector
  $result = sql_query ("SELECT * FROM s_anomalies WHERE sector_id=".$sector_id." AND user_id=".$user_id." ORDER BY distance");
  while ($planet = sql_fetchrow ($result)) {

    // If we can't view the planet, then don't show it...
    if (!in_array ($planet['id'], $planets)) continue;

    if ($first_row) {
      $first_row = false;
      print_remark ("Sector ".$sector['sector']);
      echo "<table align=center border=0>\n";
      echo "  <tr class=wb><th colspan=6>Sector ".$sector['sector'].": ".$sector['name']."</th></tr>\n";
    }

    // Can we show this planet (eg, is it in our $planets-array)
    if (!empty ($visible_planets) && !in_array ($planet['id'], $visible_planets)) continue;

    // Show entry
    echo "  <tr class=bl>\n";

    if (anomaly_is_planet ($planet['id'])) {
      echo "    <td>&nbsp;Planet ".$planet['name']."&nbsp;</td>\n";
      echo "    <td><a href=\"anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">&nbsp;View Planet Info&nbsp;</a></td>\n";
    } else {
      echo "    <td>&nbsp;Anomaly ".$planet['name']."&nbsp;</td>\n";
      echo "    <td><a href=\"anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">&nbsp;View Anomaly Info&nbsp;</a></td>\n";
    }

    if (planet_is_habitable ($planet['id']) or planet_is_minable ($planet['id'])) {
      echo "    <td><a href=\"surface.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">&nbsp;Surface View&nbsp;</a></td>\n";
      echo "    <td><a href=\"construct.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">&nbsp;Construction&nbsp;</a></td>\n";
      echo "    <td><a href=\"manufacture.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id'])."\">&nbsp;Manufacture&nbsp;</a></td>\n";
      if (planet_has_vesselbuilding_capability ($planet['id'])) {
        echo "    <td><a href=\"vesselcreate.php?cmd=".encrypt_get_vars("showaid")."&aid=".encrypt_get_vars($planet['id'])."\">&nbsp;Create Vessel&nbsp;</a></td>\n";
      } else {
        echo "    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
      }
    } else {
      echo "    <th colspan=4>No Construction Possible</th>\n";
    }
    echo "  </tr>\n";
  } // while

  if ($first_row == false) {
    echo "</table>\n";        // Close last sector
    echo "<br><br>\n";
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
function conview_show_sector ($user_id, $sector_id, $planets) {
  assert (is_numeric ($user_id));
  assert (is_numeric ($sector_id));
  assert (is_array ($planets));

  global $_RUN;

  $tmpvar3 = array();

  // Check how many planets we own in this sector. If none, don't show anything...
  $result = sql_query ("SELECT COUNT(*) AS count FROM s_anomalies WHERE sector_id = ".$sector_id." AND user_id=".$user_id, JUST_ONE_ALLOWED);
  $tmp= sql_fetchrow ($result);
  if ($tmp['count'] == 0) return;

  // Get sector information
  $sector = sector_get_sector ($sector_id);


  // Get planet information for all planets in the sector
  $result = sql_query ("SELECT * FROM s_anomalies WHERE sector_id=".$sector_id." AND user_id=".$user_id." ORDER BY distance", MULTIPLE_ALLOWED);
  while ($planet = sql_fetchrow ($result)) {

    // If we can't view the planet, then don't show it...
    if (!in_array ($planet['id'], $planets)) continue;

    // Can we show this planet (eg, is it in our $planets-array)
    if (!empty ($visible_planets) && !in_array ($planet['id'], $visible_planets)) continue;

    $tmpvar = array ();

    $tmpvar['name'] = $planet['name'];
    $tmpvar['href'] = "anomaly.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id']);
    if (anomaly_is_planet ($planet['id'])) {
      $tmpvar['viewstring'] = "View Planet";
    } else {
      $tmpvar['viewstring'] = "View Anomaly";
    }

    $tmpvar2 = array ();

    if (planet_is_habitable ($planet['id']) or planet_is_minable ($planet['id'])) {
      $tmpvar2['href'] = "surface.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id']);
      $tmpvar2['str']  = "Surface View";
      $tmpvar['href_array'][] = $tmpvar2;

      $tmpvar2['href'] = "construct.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id']);
      $tmpvar2['str']  = "Construction";
      $tmpvar['href_array'][] = $tmpvar2;

      $tmpvar2['href'] = "manufacture.php?cmd=".encrypt_get_vars("show")."&aid=".encrypt_get_vars($planet['id']);
      $tmpvar2['str']  = "Manufacturing";
      $tmpvar['href_array'][] = $tmpvar2;

      if (planet_has_vesselbuilding_capability ($planet['id'])) {
        $tmpvar2['href'] = "vesselcreate.php?cmd=".encrypt_get_vars("showaid")."&aid=".encrypt_get_vars($planet['id']);
        $tmpvar2['str']  = "Create Vessel";
        $tmpvar['href_array'][] = $tmpvar2;
      } else {
        $tmpvar2['href'] = "";
        $tmpvar2['str'] = "";
        $tmpvar['href_array'][] = $tmpvar2;
      }
    } else {
      $tmpvar2['href'] = "";
      $tmpvar2['str'] = "";
      $tmpvar['href_array'][] = $tmpvar2;
      $tmpvar['href_array'][] = $tmpvar2;
      $tmpvar['href_array'][] = $tmpvar2;
      $tmpvar['href_array'][] = $tmpvar2;
    }

    $tmpvar3[] = $tmpvar;
  } // while


  $template = new Smarty ();
  $template->debugging = true;
  $template->assign ("sector_id", $sector['sector']);
  $template->assign ("sector_name", $sector['name']);
  $template->assign ("planets", $tmpvar3);
  $template->display ($_RUN['theme_path']."/conview.tpl");
}

/*
<!-- conview.tpl -->

   <table class='standard' align='center' border='0'>
     <tr><th colspan=6>Sector {$sector_id}: {$sector_name}</th></tr>

   {section loop='row' name='$planets'}
     <tr class={cycle values="odd,even"}>
       <td>&nbsp;{$planets[row].name}&nbsp;</td>
       <td><a href='{$planets[row].href}'>&nbsp;{$planets[row].viewstring}&nbsp;</a></td>

       planets[row].href_array

     {assign var="cnt" value=planets[row].href_array|@count}
     {if $cnt > 0}
       {section loop='row2' name='planets[row].href_array'}
          {if $planets[row].href_array[row2].href == ""}
            <td>&nbsp;</td>
          {else}
            <td><a href='{$planets[row].href_array[row2].href}'>&nbsp;{$planets[row].href_array[row2].str}&nbsp;</a></td>
          {/if}
       {/section}
     {else}
       <th colspan=4>No Construction Possible</th>
     {/if}
     </tr>
   {/section}


  </table>
  <br>
  <br>

<!-- End conview.tpl -->

*/

?>


