<!-- planet-details -->

  <table class=standard2 width=500 border=1 align=center>
    <tr><td align=center><b><i>Sector: {$sector_name} / Planet: {$planet_name}</i></b></td></tr>
    <tr><td>
      <table border=0 cellpadding=0 cellspacing=0 width=100%>
        <tr><td width=200>
          <table border=0 cellpadding=0 cellspacing=0 width=100%>
            <tr><th>Planet's View</th></tr>
            <tr><td width=100%><center><img src='{$image}' width=150 height=150></center></td></tr>
            <tr><th>&nbsp;</th></tr>
          </table>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
          <table border=0 cellpadding=0 cellspacing=0 width=100%>
            <tr><td nowrap width=40%><strong>Planet Name        </strong></td><td nowrap width=1%><b>:</b></td>
               {if $rename_form_visible == "true"}
                <td nowrap>
                  <form method='post' action='{$SCRIPT_NAME}'>
                    <input type='hidden' name='cmd' value='{$cmd}'>
                    <input type='hidden' name='frmid' value='{$formid}'>
                    <input type='hidden' name='aid' value='{$aid}'>    
                    <input type=text size=15 maxlength=30 name=ne_name>                     
                    <input name=submit type=submit value=\"Claim\">
                  </form>
                </td>
              {else}
                <td nowrap>{$planet_name}</td>
              {/if}
            </tr>
            <tr><td nowrap width=40%><strong>Planet Class       </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$class}</td></tr>
            <tr><td nowrap width=40%><strong>Inhabitants        </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$race}</td></tr>
            <tr><td nowrap width=40%><strong>Current Status     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$state}</td></tr>
            <tr><td nowrap width=40%><strong>Happiness Rating   </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$happieness}</td></tr>
            <tr><td nowrap width=40%><strong>Medical Rating     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$healtieness}</td></tr>
            <tr><td nowrap width=40%><strong>Population         </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$population}</td></tr>
            <tr><td nowrap width=40%><strong>Equator Radius     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$radius} km</td></tr>
            <tr><td nowrap width=40%><strong>Distance to sun    </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$distance} km (10<sup>6</sup>)</td></tr>
            <tr><td nowrap width=40%><strong>Water Percentage   </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$water} %</td></tr>
            <tr><td nowrap width=40%><strong>Global Temperature </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap>{$temperature} K</td></tr>
          </table>
        </td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
      <td align=center>
      <table border=0 cellpadding=0 cellspacing=0 width=100%>
        <tr><td width=50%>
                <table border=0 cellpadding=0 cellspacing=0 width=100%>
                  {section name='row' loop=$ores}
                     <tr><td><strong>{$ores[row].name}</strong></td> <td><strong>:</strong></td> <td>{$ores[row].stock} tons </td></tr>
                  {/section}
                </table>
        </td><td valign=top width=50%>
                <table border=0 cellpadding=0 cellspacing=0 width=100%>
                    <tr><td colspan=3 nowrap><strong>A / D / S : </strong> {$attack}<sup>(+{$extra_attack})</sup> / {$defense}<sup>(+{$extra_defense})</sup> / {$strength}</td></tr>
                    <tr><td colspan=3>&nbsp;</td></tr>
                    <tr><td colspan=3><strong>Vessels in orbit:</strong></td></tr>
                    <tr><td nowrap width=60%><strong>* Army           </strong></td><td nowrap width=1%><strong>:</strong></td><td>{$orbit_battle}</td></tr>
                    <tr><td nowrap width=60%><strong>* Commercial     </strong></td><td nowrap width=1%><strong>:</strong></td><td>{$orbit_trade}</td></tr>
                    <tr><td nowrap width=60%><strong>* Exploration    </strong></td><td nowrap width=1%><strong>:</strong></td><td>{$orbit_explore}</td></tr>
                </table>
        <td></tr>
      </table>
    </td>
  </tr>

  {if $description != ""} 
    <tr>
      <td>
        {$description}
      </td>
    </tr>
  {/if}
  
  </table>
  <br>
  <br>





  
<!-- End planet-details -->
