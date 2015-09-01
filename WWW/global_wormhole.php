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
function wormhole_show_wormhole ($wormhole_id) {
  assert (!empty($wormhole_id));
  global $_GALAXY;

  if (! anomaly_is_wormhole ($wormhole_id)) return ;

  $wormhole = anomaly_get_anomaly ($wormhole_id);
  $sector   = sector_get_sector ($wormhole['sector_id']);
  $race     = user_get_race ($wormhole['user_id']);
  $result       = sql_query ("SELECT * FROM w_wormhole WHERE id=".$wormhole['id']);
  $dst_wormhole = sql_fetchrow ($result);

  if ($race == "") $race = "-";


  echo "<table border=1 width=500 align=center>\n";
  echo "  <tr><td align=center><b><i>Sector: ".$sector['name']." / Anomaly: ".$wormhole['name']."</i></b></td></tr>\n";
  echo "  <tr><td>\n";
  echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "      <tr><td width=200>\n";
  echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "            <tr><th>Anomaly View</th></tr>\n";
  echo "            <tr><td width=100%><center><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/wormholes/".$wormhole['image'].".jpg\" width=150 height=150></center></td></tr>\n";
  echo "            <tr><th>&nbsp;</th></tr>\n";
  echo "          </table>\n";
  echo "        </td>\n";
  echo "        <td>&nbsp;</td>\n";
  echo "        <td nowrap valign=top>\n";
  echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
  echo "            <tr><td nowrap width=40%><strong>Wormhole Name        </strong></td><td nowrap width=1%><b>:</b></td>\n";
    if ($wormhole['unknown'] == 1) {
        form_start();
        echo "<td nowrap>\n";
        echo "  <input type=hidden name=aid value=".encrypt_get_vars ($wormhole_id).">\n";
        echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("claim").">\n";
        echo "  <input type=text size=15 maxlength=30 name=ne_name>\n";
        echo "  <input name=submit type=submit value=\"Claim\">\n";
        echo "</td>\n";
        form_end ();
    } else {
        echo "<td nowrap>".$wormhole['name']."</td>\n";
    }
  echo "             </tr>\n";
  echo "            <tr><td colspan=3>&nbsp;</td></tr>\n";
  echo "            <tr><td nowrap width=40%><strong>Caretaker          </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$race."</td></tr>\n";
  echo "            <tr><td nowrap width=40%><strong>Radius             </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$wormhole['radius']." km</td></tr>\n";
  echo "            <tr><td nowrap width=40%><strong>Distance to sun    </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$wormhole['distance']." km (10<sup>6</sup>)</td></tr>\n";
  echo "            <tr><td colspan=3>&nbsp;</td></tr>\n";
  echo "            <tr><td nowrap width=40%><strong>Destination        </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$dst_wormhole['distance']." / ".$dst_wormhole['angle']."</td></tr>\n";
  echo "            <tr><td nowrap width=40%><strong>Stability          </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".wormhole_get_wormhole_stability($dst_wormhole['next_jump'])." </td></tr>\n";

  echo "          </table>\n";
  echo "        </td>\n";
  echo "      </tr>\n";
  echo "    </table>\n";
  echo "    </td>\n";
  echo "  </tr>\n";
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
function wormhole_get_wormhole_stability ($ticks) {
  if ($ticks == -1) return "Artificial stabilized";
  if ($ticks < 5)  return "Very unstable";
  if ($ticks < 10) return "Unstable";
  if ($ticks < 20) return "Becoming Unstable";
  if ($ticks < 40) return "Minor fluxuations";
  return "Stable";
}

?>