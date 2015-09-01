<?php /* Smarty version 2.6.2, created on 2004-06-03 23:51:05
         compiled from /home/joshua/WWW/themes/Perihelion/inventions.tpl */ ?>
<!-- Inventions -->
 
  <table class="standard" align="center" border="0"> 
  <tr><th colspan=5>Building Discoveries</th></tr>
  <?php if (isset($this->_sections['tr'])) unset($this->_sections['tr']);
$this->_sections['tr']['name'] = 'tr';
$this->_sections['tr']['loop'] = is_array($_loop=$this->_tpl_vars['building_text']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['tr']['step'] = ((int)5) == 0 ? 1 : (int)5;
$this->_sections['tr']['show'] = true;
$this->_sections['tr']['max'] = $this->_sections['tr']['loop'];
$this->_sections['tr']['start'] = $this->_sections['tr']['step'] > 0 ? 0 : $this->_sections['tr']['loop']-1;
if ($this->_sections['tr']['show']) {
    $this->_sections['tr']['total'] = min(ceil(($this->_sections['tr']['step'] > 0 ? $this->_sections['tr']['loop'] - $this->_sections['tr']['start'] : $this->_sections['tr']['start']+1)/abs($this->_sections['tr']['step'])), $this->_sections['tr']['max']);
    if ($this->_sections['tr']['total'] == 0)
        $this->_sections['tr']['show'] = false;
} else
    $this->_sections['tr']['total'] = 0;
if ($this->_sections['tr']['show']):

            for ($this->_sections['tr']['index'] = $this->_sections['tr']['start'], $this->_sections['tr']['iteration'] = 1;
                 $this->_sections['tr']['iteration'] <= $this->_sections['tr']['total'];
                 $this->_sections['tr']['index'] += $this->_sections['tr']['step'], $this->_sections['tr']['iteration']++):
$this->_sections['tr']['rownum'] = $this->_sections['tr']['iteration'];
$this->_sections['tr']['index_prev'] = $this->_sections['tr']['index'] - $this->_sections['tr']['step'];
$this->_sections['tr']['index_next'] = $this->_sections['tr']['index'] + $this->_sections['tr']['step'];
$this->_sections['tr']['first']      = ($this->_sections['tr']['iteration'] == 1);
$this->_sections['tr']['last']       = ($this->_sections['tr']['iteration'] == $this->_sections['tr']['total']);
?>
  <tr> 
    <?php if (isset($this->_sections['td'])) unset($this->_sections['td']);
$this->_sections['td']['name'] = 'td';
$this->_sections['td']['start'] = (int)$this->_sections['tr']['index'];
$this->_sections['td']['loop'] = is_array($_loop=$this->_sections['tr']['index']+5) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['td']['show'] = true;
$this->_sections['td']['max'] = $this->_sections['td']['loop'];
$this->_sections['td']['step'] = 1;
if ($this->_sections['td']['start'] < 0)
    $this->_sections['td']['start'] = max($this->_sections['td']['step'] > 0 ? 0 : -1, $this->_sections['td']['loop'] + $this->_sections['td']['start']);
else
    $this->_sections['td']['start'] = min($this->_sections['td']['start'], $this->_sections['td']['step'] > 0 ? $this->_sections['td']['loop'] : $this->_sections['td']['loop']-1);
if ($this->_sections['td']['show']) {
    $this->_sections['td']['total'] = min(ceil(($this->_sections['td']['step'] > 0 ? $this->_sections['td']['loop'] - $this->_sections['td']['start'] : $this->_sections['td']['start']+1)/abs($this->_sections['td']['step'])), $this->_sections['td']['max']);
    if ($this->_sections['td']['total'] == 0)
        $this->_sections['td']['show'] = false;
} else
    $this->_sections['td']['total'] = 0;
if ($this->_sections['td']['show']):

            for ($this->_sections['td']['index'] = $this->_sections['td']['start'], $this->_sections['td']['iteration'] = 1;
                 $this->_sections['td']['iteration'] <= $this->_sections['td']['total'];
                 $this->_sections['td']['index'] += $this->_sections['td']['step'], $this->_sections['td']['iteration']++):
$this->_sections['td']['rownum'] = $this->_sections['td']['iteration'];
$this->_sections['td']['index_prev'] = $this->_sections['td']['index'] - $this->_sections['td']['step'];
$this->_sections['td']['index_next'] = $this->_sections['td']['index'] + $this->_sections['td']['step'];
$this->_sections['td']['first']      = ($this->_sections['td']['iteration'] == 1);
$this->_sections['td']['last']       = ($this->_sections['td']['iteration'] == $this->_sections['td']['total']);
?> 
      <?php if ($this->_sections['td']['index'] < $this->_tpl_vars['building_count']): ?> 
        <td>&nbsp;<?php echo "<a href='".($this->_tpl_vars['building_href'][$this->_sections['td']['index']])."'><img alt='".($this->_tpl_vars['building_text'][$this->_sections['td']['index']])."' src='".($this->_tpl_vars['building_img'][$this->_sections['td']['index']])."' border='0' width='100' height='100'></a>"; ?>
&nbsp;</td>      
      <?php else: ?>
        <td>&nbsp;</td>      
      <?php endif; ?>
    <?php endfor; endif; ?> 
  </tr> 
  <?php endfor; endif; ?> 
  <tr><th colspan=5>Discoveries Completed: <?php echo $this->_tpl_vars['building_discovery_percentage']; ?>
%</th></tr>
  <?php if (isset ( $this->_tpl_vars['building_next_discovery_percentage'] )): ?>
  <tr><th colspan=5>Next discovery completion: <?php echo $this->_tpl_vars['building_next_discovery_percentage']; ?>
%</th></tr>
  <?php endif; ?>
  </table>
  <br>
  <br>

  
  <table class="standard" align="center" border="0"> 
  <tr><th colspan=5>Vessel Discoveries</th></tr>
  <?php if (isset($this->_sections['tr'])) unset($this->_sections['tr']);
$this->_sections['tr']['name'] = 'tr';
$this->_sections['tr']['loop'] = is_array($_loop=$this->_tpl_vars['vessel_text']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['tr']['step'] = ((int)5) == 0 ? 1 : (int)5;
$this->_sections['tr']['show'] = true;
$this->_sections['tr']['max'] = $this->_sections['tr']['loop'];
$this->_sections['tr']['start'] = $this->_sections['tr']['step'] > 0 ? 0 : $this->_sections['tr']['loop']-1;
if ($this->_sections['tr']['show']) {
    $this->_sections['tr']['total'] = min(ceil(($this->_sections['tr']['step'] > 0 ? $this->_sections['tr']['loop'] - $this->_sections['tr']['start'] : $this->_sections['tr']['start']+1)/abs($this->_sections['tr']['step'])), $this->_sections['tr']['max']);
    if ($this->_sections['tr']['total'] == 0)
        $this->_sections['tr']['show'] = false;
} else
    $this->_sections['tr']['total'] = 0;
if ($this->_sections['tr']['show']):

            for ($this->_sections['tr']['index'] = $this->_sections['tr']['start'], $this->_sections['tr']['iteration'] = 1;
                 $this->_sections['tr']['iteration'] <= $this->_sections['tr']['total'];
                 $this->_sections['tr']['index'] += $this->_sections['tr']['step'], $this->_sections['tr']['iteration']++):
$this->_sections['tr']['rownum'] = $this->_sections['tr']['iteration'];
$this->_sections['tr']['index_prev'] = $this->_sections['tr']['index'] - $this->_sections['tr']['step'];
$this->_sections['tr']['index_next'] = $this->_sections['tr']['index'] + $this->_sections['tr']['step'];
$this->_sections['tr']['first']      = ($this->_sections['tr']['iteration'] == 1);
$this->_sections['tr']['last']       = ($this->_sections['tr']['iteration'] == $this->_sections['tr']['total']);
?>
  <tr> 
    <?php if (isset($this->_sections['td'])) unset($this->_sections['td']);
$this->_sections['td']['name'] = 'td';
$this->_sections['td']['start'] = (int)$this->_sections['tr']['index'];
$this->_sections['td']['loop'] = is_array($_loop=$this->_sections['tr']['index']+5) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['td']['show'] = true;
$this->_sections['td']['max'] = $this->_sections['td']['loop'];
$this->_sections['td']['step'] = 1;
if ($this->_sections['td']['start'] < 0)
    $this->_sections['td']['start'] = max($this->_sections['td']['step'] > 0 ? 0 : -1, $this->_sections['td']['loop'] + $this->_sections['td']['start']);
else
    $this->_sections['td']['start'] = min($this->_sections['td']['start'], $this->_sections['td']['step'] > 0 ? $this->_sections['td']['loop'] : $this->_sections['td']['loop']-1);
if ($this->_sections['td']['show']) {
    $this->_sections['td']['total'] = min(ceil(($this->_sections['td']['step'] > 0 ? $this->_sections['td']['loop'] - $this->_sections['td']['start'] : $this->_sections['td']['start']+1)/abs($this->_sections['td']['step'])), $this->_sections['td']['max']);
    if ($this->_sections['td']['total'] == 0)
        $this->_sections['td']['show'] = false;
} else
    $this->_sections['td']['total'] = 0;
if ($this->_sections['td']['show']):

            for ($this->_sections['td']['index'] = $this->_sections['td']['start'], $this->_sections['td']['iteration'] = 1;
                 $this->_sections['td']['iteration'] <= $this->_sections['td']['total'];
                 $this->_sections['td']['index'] += $this->_sections['td']['step'], $this->_sections['td']['iteration']++):
$this->_sections['td']['rownum'] = $this->_sections['td']['iteration'];
$this->_sections['td']['index_prev'] = $this->_sections['td']['index'] - $this->_sections['td']['step'];
$this->_sections['td']['index_next'] = $this->_sections['td']['index'] + $this->_sections['td']['step'];
$this->_sections['td']['first']      = ($this->_sections['td']['iteration'] == 1);
$this->_sections['td']['last']       = ($this->_sections['td']['iteration'] == $this->_sections['td']['total']);
?> 
      <?php if ($this->_sections['td']['index'] < $this->_tpl_vars['vessel_count']): ?> 
        <td>&nbsp;<?php echo "<a href='".($this->_tpl_vars['vessel_href'][$this->_sections['td']['index']])."'><img alt='".($this->_tpl_vars['vessel_text'][$this->_sections['td']['index']])."' src='".($this->_tpl_vars['vessel_img'][$this->_sections['td']['index']])."' border='0' width='100' height='100'></a>"; ?>
&nbsp;</td>      
      <?php else: ?>
        <td>&nbsp;</td>      
      <?php endif; ?>
    <?php endfor; endif; ?> 
  </tr> 
  <?php endfor; endif; ?> 
  <tr><th colspan=5>Discovery Completed: <?php echo $this->_tpl_vars['vessel_discovery_percentage']; ?>
%</th></tr>
  <?php if (isset ( $this->_tpl_vars['vessel_next_discovery_percentage'] )): ?>
  <tr><th colspan=5>Next discovery completion: <?php echo $this->_tpl_vars['vessel_next_discovery_percentage']; ?>
%</th></tr>
  <?php endif; ?>
  </table>
  <br>
  <br>

  
  <table class="standard" align="center" border="0"> 
  <tr><th colspan=5>Item Discoveries</th></tr>
  <?php if (isset($this->_sections['tr'])) unset($this->_sections['tr']);
$this->_sections['tr']['name'] = 'tr';
$this->_sections['tr']['loop'] = is_array($_loop=$this->_tpl_vars['item_text']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['tr']['step'] = ((int)5) == 0 ? 1 : (int)5;
$this->_sections['tr']['show'] = true;
$this->_sections['tr']['max'] = $this->_sections['tr']['loop'];
$this->_sections['tr']['start'] = $this->_sections['tr']['step'] > 0 ? 0 : $this->_sections['tr']['loop']-1;
if ($this->_sections['tr']['show']) {
    $this->_sections['tr']['total'] = min(ceil(($this->_sections['tr']['step'] > 0 ? $this->_sections['tr']['loop'] - $this->_sections['tr']['start'] : $this->_sections['tr']['start']+1)/abs($this->_sections['tr']['step'])), $this->_sections['tr']['max']);
    if ($this->_sections['tr']['total'] == 0)
        $this->_sections['tr']['show'] = false;
} else
    $this->_sections['tr']['total'] = 0;
if ($this->_sections['tr']['show']):

            for ($this->_sections['tr']['index'] = $this->_sections['tr']['start'], $this->_sections['tr']['iteration'] = 1;
                 $this->_sections['tr']['iteration'] <= $this->_sections['tr']['total'];
                 $this->_sections['tr']['index'] += $this->_sections['tr']['step'], $this->_sections['tr']['iteration']++):
$this->_sections['tr']['rownum'] = $this->_sections['tr']['iteration'];
$this->_sections['tr']['index_prev'] = $this->_sections['tr']['index'] - $this->_sections['tr']['step'];
$this->_sections['tr']['index_next'] = $this->_sections['tr']['index'] + $this->_sections['tr']['step'];
$this->_sections['tr']['first']      = ($this->_sections['tr']['iteration'] == 1);
$this->_sections['tr']['last']       = ($this->_sections['tr']['iteration'] == $this->_sections['tr']['total']);
?>
  <tr> 
    <?php if (isset($this->_sections['td'])) unset($this->_sections['td']);
$this->_sections['td']['name'] = 'td';
$this->_sections['td']['start'] = (int)$this->_sections['tr']['index'];
$this->_sections['td']['loop'] = is_array($_loop=$this->_sections['tr']['index']+5) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['td']['show'] = true;
$this->_sections['td']['max'] = $this->_sections['td']['loop'];
$this->_sections['td']['step'] = 1;
if ($this->_sections['td']['start'] < 0)
    $this->_sections['td']['start'] = max($this->_sections['td']['step'] > 0 ? 0 : -1, $this->_sections['td']['loop'] + $this->_sections['td']['start']);
else
    $this->_sections['td']['start'] = min($this->_sections['td']['start'], $this->_sections['td']['step'] > 0 ? $this->_sections['td']['loop'] : $this->_sections['td']['loop']-1);
if ($this->_sections['td']['show']) {
    $this->_sections['td']['total'] = min(ceil(($this->_sections['td']['step'] > 0 ? $this->_sections['td']['loop'] - $this->_sections['td']['start'] : $this->_sections['td']['start']+1)/abs($this->_sections['td']['step'])), $this->_sections['td']['max']);
    if ($this->_sections['td']['total'] == 0)
        $this->_sections['td']['show'] = false;
} else
    $this->_sections['td']['total'] = 0;
if ($this->_sections['td']['show']):

            for ($this->_sections['td']['index'] = $this->_sections['td']['start'], $this->_sections['td']['iteration'] = 1;
                 $this->_sections['td']['iteration'] <= $this->_sections['td']['total'];
                 $this->_sections['td']['index'] += $this->_sections['td']['step'], $this->_sections['td']['iteration']++):
$this->_sections['td']['rownum'] = $this->_sections['td']['iteration'];
$this->_sections['td']['index_prev'] = $this->_sections['td']['index'] - $this->_sections['td']['step'];
$this->_sections['td']['index_next'] = $this->_sections['td']['index'] + $this->_sections['td']['step'];
$this->_sections['td']['first']      = ($this->_sections['td']['iteration'] == 1);
$this->_sections['td']['last']       = ($this->_sections['td']['iteration'] == $this->_sections['td']['total']);
?> 
      <?php if ($this->_sections['td']['index'] < $this->_tpl_vars['item_count']): ?> 
        <td>&nbsp;<?php echo "<a href='".($this->_tpl_vars['item_href'][$this->_sections['td']['index']])."'><img alt='".($this->_tpl_vars['item_text'][$this->_sections['td']['index']])."' src='".($this->_tpl_vars['item_img'][$this->_sections['td']['index']])."' border='0' width='100' height='100'></a>"; ?>
&nbsp;</td>
      <?php else: ?>
        <td>&nbsp;</td>      
      <?php endif; ?>
    <?php endfor; endif; ?> 
  </tr> 
  <?php endfor; endif; ?> 
  <tr><th colspan=5>Discovery Completed: <?php echo $this->_tpl_vars['item_discovery_percentage']; ?>
%</th></tr>
  <?php if (isset ( $this->_tpl_vars['item_next_discovery_percentage'] )): ?>
  <tr><th colspan=5>Next discovery completion: <?php echo $this->_tpl_vars['item_next_discovery_percentage']; ?>
%</th></tr>
  <?php endif; ?>
  </table>
  


<!-- End Inventions -->