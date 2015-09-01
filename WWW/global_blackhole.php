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
function blackhole_show_blackhole ($blackhole_id) {
  assert (is_numeric ($blackhole_id));
  global $_GALAXY;

  if (! anomaly_is_blackhole ($blackhole_id)) return ;

  $blackhole = anomaly_get_anomaly ($blackhole_id);
  $race      = user_get_race ($blackhole['user_id']);
  $sector    = sector_get_sector ($blackhole['sector_id']);
  if ($race == "") $race = "-";

  echo "<table border=1 width=500 align=center>";
  echo "  <tr><td align=center><b><i>Sector: ".$sector['name']." / Anomaly: ".$blackhole['name']."</i></b></td></tr>";
  echo "  <tr><td>";
  echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
  echo "      <tr><td width=200>";
  echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>";
  echo "            <tr><th>Anomaly View</th></tr>";
  echo "            <tr><td width=100%><center><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/blackholes/".$blackhole['image'].".jpg\" width=150 height=150></center></td></tr>";
  echo "            <tr><th>&nbsp;</th></tr>";
  echo "          </table>";
  echo "        </td>";
  echo "        <td>&nbsp;</td>";
  echo "        <td nowrap valign=top>";
  echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>";
  echo "            <tr><td nowrap width=40%><strong>Blackhole Name        </strong></td><td nowrap width=1%><b>:</b></td>";
    if ($blackhole['unknown'] == 1) {
        form_start();
        echo "<td nowrap>";
        echo "  <input type=hidden name=aid value=".encrypt_get_vars ($blackhole_id).">";
        echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("claim").">";
        echo "  <input type=text size=15 maxlength=30 name=ne_name> ";
        echo "  <input name=submit type=submit value=\"Claim\">";
        echo "</td>";
        form_end ();
    } else {
        echo "<td nowrap>".$blackhole['name']."</td>";
    }
  echo "             </tr>";
  echo "            <tr><td colspan=3>&nbsp;</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Caretaker          </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$race."</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Radius             </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$blackhole['radius']." km</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Distance to sun    </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$blackhole['distance']." km (10<sup>6</sup>)</td></tr>";
  echo "            <tr><td colspan=3>&nbsp;</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Fatalities         </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".blackhole_get_fatalities ($blackhole_id)." ship(s)</td></tr>";

  echo "          </table>";
  echo "        </td>";
  echo "      </tr>";
  echo "    </table>";
  echo "    </td>";
  echo "  </tr>";
  echo "</table>";
  echo "<br><br>";
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
function blackhole_get_fatalities ($blackhole_id) {
  assert (is_numeric ($blackhole_id));

  $blackhole = anomaly_get_anomaly ($blackhole_id);
  return $blackhole['population'];
}
?>