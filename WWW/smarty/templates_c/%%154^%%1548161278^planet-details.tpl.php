<?php /* Smarty version 2.6.2, created on 2004-06-07 07:11:01
         compiled from /home/joshua/WWW/themes/Perihelion/planet-details.tpl */ ?>
<!-- planet-details -->

  <table class=standard2 width=500 border=1 align=center>
    <tr><td align=center><b><i>Sector: <?php echo $this->_tpl_vars['sector_name']; ?>
 / Planet: <?php echo $this->_tpl_vars['planet_name']; ?>
</i></b></td></tr>
    <tr><td>
      <table border=0 cellpadding=0 cellspacing=0 width=100%>
        <tr><td width=200>
          <table border=0 cellpadding=0 cellspacing=0 width=100%>
            <tr><th>Planet's View</th></tr>
            <tr><td width=100%><center><img src='<?php echo $this->_tpl_vars['image']; ?>
' width=150 height=150></center></td></tr>
            <tr><th>&nbsp;</th></tr>
          </table>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
          <table border=0 cellpadding=0 cellspacing=0 width=100%>
            <tr><td nowrap width=40%><strong>Planet Name        </strong></td><td nowrap width=1%><b>:</b></td>
               <?php if ($this->_tpl_vars['rename_form_visible'] == 'true'): ?>
                <td nowrap>
                  <form method='post' action='<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
'>
                    <input type='hidden' name='cmd' value='<?php echo $this->_tpl_vars['cmd']; ?>
'>
                    <input type='hidden' name='frmid' value='<?php echo $this->_tpl_vars['formid']; ?>
'>
                    <input type='hidden' name='aid' value='<?php echo $this->_tpl_vars['aid']; ?>
'>    
                    <input type=text size=15 maxlength=30 name=ne_name>                     
                    <input name=submit type=submit value=\"Claim\">
                  </form>
                </td>
              <?php else: ?>
                <td nowrap><?php echo $this->_tpl_vars['planet_name']; ?>
</td>
              <?php endif; ?>
            </tr>
            <tr><td nowrap width=40%><strong>Planet Class       </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['class']; ?>
</td></tr>
            <tr><td nowrap width=40%><strong>Inhabitants        </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['race']; ?>
</td></tr>
            <tr><td nowrap width=40%><strong>Current Status     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['state']; ?>
</td></tr>
            <tr><td nowrap width=40%><strong>Happiness Rating   </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['happieness']; ?>
</td></tr>
            <tr><td nowrap width=40%><strong>Medical Rating     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['healtieness']; ?>
</td></tr>
            <tr><td nowrap width=40%><strong>Population         </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['population']; ?>
</td></tr>
            <tr><td nowrap width=40%><strong>Equator Radius     </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['radius']; ?>
 km</td></tr>
            <tr><td nowrap width=40%><strong>Distance to sun    </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['distance']; ?>
 km (10<sup>6</sup>)</td></tr>
            <tr><td nowrap width=40%><strong>Water Percentage   </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['water']; ?>
 %</td></tr>
            <tr><td nowrap width=40%><strong>Global Temperature </strong></td><td nowrap width=1%><b>:</b>&nbsp;</td><td nowrap><?php echo $this->_tpl_vars['temperature']; ?>
 K</td></tr>
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
                  <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['ores']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['row']['show'] = true;
$this->_sections['row']['max'] = $this->_sections['row']['loop'];
$this->_sections['row']['step'] = 1;
$this->_sections['row']['start'] = $this->_sections['row']['step'] > 0 ? 0 : $this->_sections['row']['loop']-1;
if ($this->_sections['row']['show']) {
    $this->_sections['row']['total'] = $this->_sections['row']['loop'];
    if ($this->_sections['row']['total'] == 0)
        $this->_sections['row']['show'] = false;
} else
    $this->_sections['row']['total'] = 0;
if ($this->_sections['row']['show']):

            for ($this->_sections['row']['index'] = $this->_sections['row']['start'], $this->_sections['row']['iteration'] = 1;
                 $this->_sections['row']['iteration'] <= $this->_sections['row']['total'];
                 $this->_sections['row']['index'] += $this->_sections['row']['step'], $this->_sections['row']['iteration']++):
$this->_sections['row']['rownum'] = $this->_sections['row']['iteration'];
$this->_sections['row']['index_prev'] = $this->_sections['row']['index'] - $this->_sections['row']['step'];
$this->_sections['row']['index_next'] = $this->_sections['row']['index'] + $this->_sections['row']['step'];
$this->_sections['row']['first']      = ($this->_sections['row']['iteration'] == 1);
$this->_sections['row']['last']       = ($this->_sections['row']['iteration'] == $this->_sections['row']['total']);
?>
                     <tr><td><strong><?php echo $this->_tpl_vars['ores'][$this->_sections['row']['index']]['name']; ?>
</strong></td> <td><strong>:</strong></td> <td><?php echo $this->_tpl_vars['ores'][$this->_sections['row']['index']]['stock']; ?>
 tons </td></tr>
                  <?php endfor; endif; ?>
                </table>
        </td><td valign=top width=50%>
                <table border=0 cellpadding=0 cellspacing=0 width=100%>
                    <tr><td colspan=3 nowrap><strong>A / D / S : </strong> <?php echo $this->_tpl_vars['attack']; ?>
<sup>(+<?php echo $this->_tpl_vars['extra_attack']; ?>
)</sup> / <?php echo $this->_tpl_vars['defense']; ?>
<sup>(+<?php echo $this->_tpl_vars['extra_defense']; ?>
)</sup> / <?php echo $this->_tpl_vars['strength']; ?>
</td></tr>
                    <tr><td colspan=3>&nbsp;</td></tr>
                    <tr><td colspan=3><strong>Vessels in orbit:</strong></td></tr>
                    <tr><td nowrap width=60%><strong>* Army           </strong></td><td nowrap width=1%><strong>:</strong></td><td><?php echo $this->_tpl_vars['orbit_battle']; ?>
</td></tr>
                    <tr><td nowrap width=60%><strong>* Commercial     </strong></td><td nowrap width=1%><strong>:</strong></td><td><?php echo $this->_tpl_vars['orbit_trade']; ?>
</td></tr>
                    <tr><td nowrap width=60%><strong>* Exploration    </strong></td><td nowrap width=1%><strong>:</strong></td><td><?php echo $this->_tpl_vars['orbit_explore']; ?>
</td></tr>
                </table>
        <td></tr>
      </table>
    </td>
  </tr>

  <?php if ($this->_tpl_vars['description'] != ""): ?> 
    <tr>
      <td>
        <?php echo $this->_tpl_vars['description']; ?>

      </td>
    </tr>
  <?php endif; ?>
  
  </table>
  <br>
  <br>





  
<!-- End planet-details -->