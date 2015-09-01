<?php /* Smarty version 2.6.2, created on 2004-06-07 07:10:57
         compiled from /home/joshua/WWW/themes/Perihelion/sectors-item.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', '/home/joshua/WWW/themes/Perihelion/sectors-item.tpl', 33, false),)), $this); ?>
<!-- sectors-item -->       
 
  <table class='standard' align='center' border='0'>
<?php if ($this->_tpl_vars['rename_form_visible'] == 'true'): ?>
    <form method='post' action='<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
'>
    <input type='hidden' name='cmd' value='<?php echo $this->_tpl_vars['cmd']; ?>
'>
    <input type='hidden' name='frmid' value='<?php echo $this->_tpl_vars['formid']; ?>
'>
    <input type='hidden' name='sid' value='<?php echo $this->_tpl_vars['sid']; ?>
'>    
    <tr>
      <th colspan='7'>Sector <?php echo $this->_tpl_vars['sector_id']; ?>
:         
        <input type='text' size='15' maxlength='30' name='ne_name'>
        <input name='submit' type='submit' value='Claim'>
        (<?php echo $this->_tpl_vars['sector_coordinate']; ?>
)
      </th>     
    </tr>
    </form>  
<?php else: ?>
    <tr>
      <th colspan='7'>Sector <?php echo $this->_tpl_vars['sector_id']; ?>
: <?php echo $this->_tpl_vars['sector_name']; ?>
 (<?php echo $this->_tpl_vars['sector_coordinate']; ?>
)</th>
    </tr>   
<?php endif; ?>
    <tr>
      <th>Name</th>
      <th>Class</th>
      <th>Population</th>
      <th>Owned By</th>
      <th>Current Status</th>
      <th>Radius<sup><small>(km)</small></sup></th>
      <th>Distance<sup><small>(*10^6km)</small></sup></th>
    </tr>
    
      <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['anomalies']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
      <tr class='<?php echo smarty_function_cycle(array('values' => "odd, even"), $this);?>
'>
        <?php if ($this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['name'] == 'Unknown'): ?>
          <td>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['name']; ?>
&nbsp;</td>
        <?php else: ?>
          <td>&nbsp;<a href="<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['name_href']; ?>
"><?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['name']; ?>
</a>&nbsp;</td>
        <?php endif; ?>
        <td class='<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['class_class']; ?>
'>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['class']; ?>
&nbsp;</td>
        <td class='<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['population_class']; ?>
'>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['population']; ?>
&nbsp;</td>
        <td>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['owner']; ?>
&nbsp;</td>
        <td>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['status']; ?>
&nbsp;</td>
        <td>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['radius']; ?>
&nbsp;</td>
        <td>&nbsp;<?php echo $this->_tpl_vars['anomalies'][$this->_sections['row']['index']]['distance']; ?>
&nbsp;</td>
      </tr>
      <?php endfor; endif; ?>
    </table>
  <br>
  <br>
  
<!-- End sectors-item -->