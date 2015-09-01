<?php /* Smarty version 2.6.2, created on 2004-06-03 10:42:11
         compiled from messages-galaxy-msg.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', 'messages-galaxy-msg.tpl', 3, false),)), $this); ?>
<!-- messages-galaxy-msg -->  

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 from									string			Name 'from' user
 datetime							string			Date and time of the message
 image								url					URL to user image
 subject							string			Subject of the message
 body									string			Message body
 
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>     
 
  <table class='standard' align='center'>
    <tr><td colspan=2><b>Message intercepted from: <?php echo $this->_tpl_vars['from']; ?>
 (<?php echo $this->_tpl_vars['datetime']; ?>
)</b></td></tr>
    <tr>
      <td rowspan=2 valign=top width=150><img width=100 height=100 src=<?php echo $this->_tpl_vars['image']; ?>
></td>
      <td width=100%<b>Subject: </b><?php echo $this->_tpl_vars['subject']; ?>
</td>
    </tr>
    <tr><td colspan=2><font color=#cccc33><?php echo $this->_tpl_vars['body']; ?>
</font></td></tr>
  </table>
  <br>
  <br>
  
<!-- End messages-galaxy-msg -->