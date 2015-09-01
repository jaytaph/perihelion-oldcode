<?php /* Smarty version 2.6.2, created on 2004-06-03 10:55:48
         compiled from ./messages-main.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', './messages-main.tpl', 3, false),array('modifier', 'truncate', './messages-main.tpl', 37, false),)), $this); ?>
<!-- messages-main -->  

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 global|alien|planet|exploration|invention|fleet
   .href							url					link to the message box
   .low								int					number of low priority messages inside the mailbox
   .high							int					number of high priority messages inside the mailbox
   .lasttopic					string			subject of the last message inside the mailbox
              
 show_galaxy  				0|1					wether or not galaxy messages are available
 galaxy
       .href					url					link to the messagebox
       .count					int					number of messages inside the mailbox       
       .hrefsend			url					link to send an item 
 
 show_alliance				0|1					wether or not alliance messages are available
 alliance
       .href					url					link to the messagebox
       .count					int					number of messages inside the mailbox       
       .hrefsend			url					link to send an item  
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>     
 
  <table class='standard' align='center'>
    <tr><th>&nbsp;</th>      <th>&nbsp;Low Priority&nbsp;</th><th>&nbsp;High Priority&nbsp;</th><th>&nbsp;Last Topic In Message Box&nbsp;</th></tr>
    <tr>
      <td>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['global']['href']; ?>
>Global Messages</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['global']['low']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['global']['high']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['global']['lasttopic'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40) : smarty_modifier_truncate($_tmp, 40)); ?>
&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['alien']['href']; ?>
>Alien Communication</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['alien']['low']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['alien']['high']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['alien']['lasttopic'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40) : smarty_modifier_truncate($_tmp, 40)); ?>
&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['planet']['href']; ?>
>Planet Affairs</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['planet']['low']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['planet']['high']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['planet']['lasttopic'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40) : smarty_modifier_truncate($_tmp, 40)); ?>
&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['exploration']['href']; ?>
>Exploration Messages</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['exploration']['low']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['exploration']['high']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['exploration']['lasttopic'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40) : smarty_modifier_truncate($_tmp, 40)); ?>
&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['invention']['href']; ?>
>Invention Messages</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['invention']['low']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['invention']['high']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['invention']['lasttopic'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40) : smarty_modifier_truncate($_tmp, 40)); ?>
&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['fleet']['href']; ?>
>Fleet Messages</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['fleet']['low']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['fleet']['high']; ?>
&nbsp;</td>
      <td>&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['fleet']['lasttopic'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40) : smarty_modifier_truncate($_tmp, 40)); ?>
&nbsp;</td>
    </tr>
    <tr><td colspan=4>&nbsp;</td></tr>

  <?php if ($this->_tpl_vars['show_galaxy'] == '1'): ?>    
    <tr>
      <td colspan=2>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['galaxy']['href']; ?>
>Intercepted Galaxy Messages</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['galaxy']['count']; ?>
&nbsp;</td>
      <td>&nbsp;<a href=<?php echo $this->_tpl_vars['galaxy']['hrefsend']; ?>
>Send message into outer space</a>&nbsp;</td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['show_alliance'] == '1'): ?>    
    <tr>
      <td colspan=2>&nbsp;Box: <a href=<?php echo $this->_tpl_vars['alliance']['href']; ?>
>Alliance Messages</a>&nbsp;</td>
      <td>&nbsp;<?php echo $this->_tpl_vars['alliance']['count']; ?>
&nbsp;</td>
      <td>&nbsp;<a href=<?php echo $this->_tpl_vars['alliance']['hrefsend']; ?>
>Send message to alliance</a>&nbsp;</td>
    </tr>
  <?php endif; ?>
    
  </table>
  
<!-- End sectors-item -->