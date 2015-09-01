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
function nebula_show_nebula ($nebula_id) {
  assert (!empty($nebula_id));
  global $_GALAXY;

  if (! anomaly_is_nebula ($nebula_id)) return ;

  $nebula  = anomaly_get_anomaly ($nebula_id);
  $sector  = sector_get_sector ($nebula['sector_id']);
  $race    = user_get_race ($nebula['user_id']);
  if ($race == "") $race = "-";


  echo "<table border=1 width=500 align=center>";
  echo "  <tr><td align=center><b><i>Sector: ".$sector['name']." / Anomaly: ".$nebula['name']."</i></b></td></tr>";
  echo "  <tr><td>";
  echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
  echo "      <tr><td width=200>";
  echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>";
  echo "            <tr><th>Anomaly View</th></tr>";
  echo "            <tr><td width=100%><center><img src=\"".$_CONFIG['URL'].$_GALAXY['image_dir']."/nebulas/".$nebula['image'].".jpg\" width=150 height=150></center></td></tr>";
  echo "            <tr><th>&nbsp;</th></tr>";
  echo "          </table>";
  echo "        </td>";
  echo "        <td>&nbsp;</td>";
  echo "        <td nowrap valign=top>";
  echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>";
  echo "            <tr><td nowrap width=40%><strong>Nebula Name        </strong></td><td nowrap width=1%><b>:</b></td>";
    if ($nebula['unknown'] == 1) {
        form_start();
        echo "<td nowrap>";
        echo "  <input type=hidden name=aid value=".encrypt_get_vars ($nebula_id).">";
        echo "  <input type=hidden name=cmd value=".encrypt_get_vars ("claim").">";
        echo "  <input type=text size=15 maxlength=30 name=ne_name> ";
        echo "  <input name=submit type=submit value=\"Claim\">";
        echo "</td>";
        form_end ();
    } else {
        echo "<td nowrap>".$nebula['name']."</td>";
    }
  echo "             </tr>";
  echo "            <tr><td colspan=3>&nbsp;</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Caretaker          </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$race."</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Cloud Radius     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$nebula['radius']." km</td></tr>";
  echo "            <tr><td nowrap width=40%><strong>Distance to sun    </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>".$nebula['distance']." km (10<sup>6</sup>)</td></tr>";

  echo "          </table>";
  echo "        </td>";
  echo "      </tr>";
  echo "    </table>";
  echo "    </td>";
  echo "  </tr>";
  echo "</table>";
  echo "<br><br>";
}

?>