<?php /* Smarty version 2.6.2, created on 2004-06-07 08:35:51
         compiled from /home/joshua/WWW/themes/Perihelion/sectors-all.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/home/joshua/WWW/themes/Perihelion/sectors-all.tpl', 3, false),array('function', 'cycle', '/home/joshua/WWW/themes/Perihelion/sectors-all.tpl', 16, false),)), $this); ?>
<!-- sectors-all -->

   <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['theme_path'])."/pager.tpl", 'smarty_include_vars' => array('rowcount' => count($this->_tpl_vars['sectors']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>   
          
   <table class="standard" align=center>
     <tr>
       <th><?php echo $this->_tpl_vars['help_sector_all']; ?>
 ID</th>
       <th>Sector Name</th>
       <th>Qty</th>
       <th>Owner</th>
       <th>Coordinate</th>
       <th>Distance</th>
     </tr>  

    <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['start'] = (int)$this->_tpl_vars['pager_pos'];
$this->_sections['row']['max'] = (int)25;
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['sectors']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['row']['show'] = true;
if ($this->_sections['row']['max'] < 0)
    $this->_sections['row']['max'] = $this->_sections['row']['loop'];
$this->_sections['row']['step'] = 1;
if ($this->_sections['row']['start'] < 0)
    $this->_sections['row']['start'] = max($this->_sections['row']['step'] > 0 ? 0 : -1, $this->_sections['row']['loop'] + $this->_sections['row']['start']);
else
    $this->_sections['row']['start'] = min($this->_sections['row']['start'], $this->_sections['row']['step'] > 0 ? $this->_sections['row']['loop'] : $this->_sections['row']['loop']-1);
if ($this->_sections['row']['show']) {
    $this->_sections['row']['total'] = min(ceil(($this->_sections['row']['step'] > 0 ? $this->_sections['row']['loop'] - $this->_sections['row']['start'] : $this->_sections['row']['start']+1)/abs($this->_sections['row']['step'])), $this->_sections['row']['max']);
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
    <tr class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
">
      <td>&nbsp;<a href="<?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['href']; ?>
"><?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['id']; ?>
</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['name']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['qty']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['owner']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['coordinate']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['sectors'][$this->_sections['row']['index']]['distance']; ?>
 ly&nbsp;</td>
    </tr>
    <?php endfor; endif; ?>
  </table>
  
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['theme_path'])."/pager.tpl", 'smarty_include_vars' => array('rowcount' => count($this->_tpl_vars['sectors']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>   
  
<!-- End sectors-all -->