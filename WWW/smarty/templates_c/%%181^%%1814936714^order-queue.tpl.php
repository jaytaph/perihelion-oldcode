<?php /* Smarty version 2.6.2, created on 2004-06-07 07:06:14
         compiled from /home/joshua/WWW/themes/Perihelion/order-queue.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', '/home/joshua/WWW/themes/Perihelion/order-queue.tpl', 3, false),array('function', 'cycle', '/home/joshua/WWW/themes/Perihelion/order-queue.tpl', 31, false),)), $this); ?>
<!-- order queue -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 building|invention|vessel|flight   
   .count             int         Number of items in the queue
   .what[]            string      What is in the queue
   .ticks[]           int         How many ticks left
              
 itemcount  				  int					Total number of items in the queue's
 
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

<?php if ($this->_tpl_vars['itemcount'] == 0): ?>
  <table class="standard" align=center width=60%>
    <tr><td>There are no current orders</td></tr>
  </table>
<?php else: ?>

  <?php if ($this->_tpl_vars['building']['count'] != 0): ?>
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Building Construction</th></tr>
      <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['building']['what']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
'><td><?php echo $this->_tpl_vars['building']['what'][$this->_sections['row']['index']]; ?>
</td><td><?php echo $this->_tpl_vars['building']['ticks'][$this->_sections['row']['index']]; ?>
 tick<?php if ($this->_tpl_vars['building']['ticks'][$this->_sections['row']['index']] != 1): ?>s<?php endif; ?> left.</td></tr>
      <?php endfor; endif; ?>
    </table>
    <br>
    <br>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['vessel']['count'] != 0): ?>
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Vessel Construction And Upgrading</th></tr>
      <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['vessel']['what']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
'><td><?php echo $this->_tpl_vars['building']['what'][$this->_sections['row']['index']]; ?>
</td><td><?php echo $this->_tpl_vars['vessel']['ticks'][$this->_sections['row']['index']]; ?>
 tick<?php if ($this->_tpl_vars['vessel']['ticks'][$this->_sections['row']['index']] != 1): ?>s<?php endif; ?> left.</td></tr>
      <?php endfor; endif; ?>
    </table>
    <br>
    <br>
  <?php endif; ?>
  
  <?php if ($this->_tpl_vars['item']['count'] != 0): ?>
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Item Construction</th></tr>
      <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['item']['what']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
'><td><?php echo $this->_tpl_vars['item']['what'][$this->_sections['row']['index']]; ?>
</td><td><?php echo $this->_tpl_vars['item']['ticks'][$this->_sections['row']['index']]; ?>
 tick<?php if ($this->_tpl_vars['item']['ticks'][$this->_sections['row']['index']] != 1): ?>s<?php endif; ?> left.</td></tr>
      <?php endfor; endif; ?>
    </table>
    <br>
    <br>
  <?php endif; ?>  

  <?php if ($this->_tpl_vars['flight']['count'] != 0): ?>
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Spaceship Flightplans</th></tr>
      <?php if (isset($this->_sections['row'])) unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['flight']['what']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
'><td><?php echo $this->_tpl_vars['flight']['what'][$this->_sections['row']['index']]; ?>
</td><td><?php echo $this->_tpl_vars['flight']['ticks'][$this->_sections['row']['index']]; ?>
 tick<?php if ($this->_tpl_vars['flight']['ticks'][$this->_sections['row']['index']] != 1): ?>s<?php endif; ?> left.</td></tr>
      <?php endfor; endif; ?>
    </table>
    <br>
    <br>
  <?php endif; ?>


<?php endif; ?>


<!-- End order queue -->