<?php /* Smarty version 2.6.2, created on 2004-06-04 19:46:51
         compiled from /home/joshua/WWW/themes/Perihelion/vessel-user.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', '/home/joshua/WWW/themes/Perihelion/vessel-user.tpl', 3, false),array('function', 'cycle', '/home/joshua/WWW/themes/Perihelion/vessel-user.tpl', 20, false),)), $this); ?>
<!-- vessel-user.tpl -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--

 use 'status_nohref' for no hyperlinks inside the status
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

  <table class='standard' align=center border=0 width=75%>
    <tr>
      <th>Name</th>
      <th>Type</th>
      <th>Status</th>
      <th>Coords</th>
    </tr>
    
    
    <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['vessels']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
      <tr class=<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
>
      <td><img src='<?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['image']; ?>
'><a href='<?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['href']; ?>
'><?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['name']; ?>
</a></td>
      <td><?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['type']; ?>
</td>
      <td><?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['status_nohref']; ?>
</td>
      <td><?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['distance']; ?>
 / <?php echo $this->_tpl_vars['vessels'][$this->_sections['row']['index']]['angle']; ?>
</td>
    <?php endfor; endif; ?>
    
  </table>
  <br>
  <br>

<!-- end vessel-user.tpl -->