<?php /* Smarty version 2.6.2, created on 2004-06-07 10:38:59
         compiled from /home/joshua/WWW/themes/Perihelion/whoisonline.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', '/home/joshua/WWW/themes/Perihelion/whoisonline.tpl', 10, false),)), $this); ?>
<!-- whoisonline -->

  <table class="standard" align="center" width="60%">
    <tr>
      <th>Full Name</th>
      <th>Last action</th>
    </tr>

    <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['onlineusers']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
    <tr class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
">
      <td>&nbsp;<a href='<?php echo $this->_tpl_vars['onlineusers'][$this->_sections['row']['index']]['href']; ?>
'><?php echo $this->_tpl_vars['onlineusers'][$this->_sections['row']['index']]['user']; ?>
</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['onlineusers'][$this->_sections['row']['index']]['idle']; ?>
&nbsp;</td>
    </tr>
    <?php endfor; endif; ?>
  </table>
  
<!-- End whoisonline -->