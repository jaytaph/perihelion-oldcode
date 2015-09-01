<?php /* Smarty version 2.6.2, created on 2004-06-07 10:59:13
         compiled from /home/joshua/WWW/themes/Perihelion/conview.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', '/home/joshua/WWW/themes/Perihelion/conview.tpl', 7, false),array('modifier', 'count', '/home/joshua/WWW/themes/Perihelion/conview.tpl', 12, false),)), $this); ?>
<!-- conview.tpl -->

   <table class='standard' align='center' border='0'>
     <tr><th colspan=6>Sector <?php echo $this->_tpl_vars['sector_id']; ?>
: <?php echo $this->_tpl_vars['sector_name']; ?>
</th></tr>

   <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['planets']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
     <tr class='<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
'>
       <td>&nbsp;<?php echo $this->_tpl_vars['planets'][$this->_sections['row']['index']]['name']; ?>
&nbsp;</td>
       <td>&nbsp;<a href='<?php echo $this->_tpl_vars['planets'][$this->_sections['row']['index']]['href']; ?>
'><?php echo $this->_tpl_vars['planets'][$this->_sections['row']['index']]['viewstring']; ?>
</a>&nbsp;</td>


     <?php $this->assign('cnt', count($this->_tpl_vars['planets'][$this->_sections['row']['index']]['href_array'])); ?>
     <?php if ($this->_tpl_vars['cnt'] > 0): ?>
       <?php if (isset($this->_sections['row2'])) unset($this->_sections['row2']);
$this->_sections['row2']['name'] = 'row2';
$this->_sections['row2']['loop'] = is_array($_loop=$this->_tpl_vars['planets'][$this->_sections['row']['index']]['href_array']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['row2']['show'] = true;
$this->_sections['row2']['max'] = $this->_sections['row2']['loop'];
$this->_sections['row2']['step'] = 1;
$this->_sections['row2']['start'] = $this->_sections['row2']['step'] > 0 ? 0 : $this->_sections['row2']['loop']-1;
if ($this->_sections['row2']['show']) {
    $this->_sections['row2']['total'] = $this->_sections['row2']['loop'];
    if ($this->_sections['row2']['total'] == 0)
        $this->_sections['row2']['show'] = false;
} else
    $this->_sections['row2']['total'] = 0;
if ($this->_sections['row2']['show']):

            for ($this->_sections['row2']['index'] = $this->_sections['row2']['start'], $this->_sections['row2']['iteration'] = 1;
                 $this->_sections['row2']['iteration'] <= $this->_sections['row2']['total'];
                 $this->_sections['row2']['index'] += $this->_sections['row2']['step'], $this->_sections['row2']['iteration']++):
$this->_sections['row2']['rownum'] = $this->_sections['row2']['iteration'];
$this->_sections['row2']['index_prev'] = $this->_sections['row2']['index'] - $this->_sections['row2']['step'];
$this->_sections['row2']['index_next'] = $this->_sections['row2']['index'] + $this->_sections['row2']['step'];
$this->_sections['row2']['first']      = ($this->_sections['row2']['iteration'] == 1);
$this->_sections['row2']['last']       = ($this->_sections['row2']['iteration'] == $this->_sections['row2']['total']);
?>
          <?php if ($this->_tpl_vars['planets'][$this->_sections['row']['index']]['href_array'][$this->_sections['row2']['index']]['str'] == ""): ?>
            <td>&nbsp;</td>
          <?php else: ?>
            <td>&nbsp;<a href='<?php echo $this->_tpl_vars['planets'][$this->_sections['row']['index']]['href_array'][$this->_sections['row2']['index']]['href']; ?>
'><?php echo $this->_tpl_vars['planets'][$this->_sections['row']['index']]['href_array'][$this->_sections['row2']['index']]['str']; ?>
</a>&nbsp;</td>
          <?php endif; ?>
       <?php endfor; endif; ?>
     <?php endif; ?>

     </tr>
   <?php endfor; endif; ?>


  </table>
  <br>
  <br>

<!-- End conview.tpl -->