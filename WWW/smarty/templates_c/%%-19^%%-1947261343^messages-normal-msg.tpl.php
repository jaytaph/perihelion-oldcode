<?php /* Smarty version 2.6.2, created on 2004-06-07 07:06:22
         compiled from /home/joshua/WWW/themes/Perihelion/messages-normal-msg.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', '/home/joshua/WWW/themes/Perihelion/messages-normal-msg.tpl', 3, false),)), $this); ?>
<!-- messages-normal-msg -->  

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 priority_img					url         URL to priority image
 id										int         ID of the message
 priority_str         string      Priority
 from									string			Name 'from' user
 datetime							string			Date and time of the message 
 delete_href          url					URL to delete this message 
 subject							string			Subject of the message
 body									string			Message body
 
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>     
 
  <table class='standard' align='center' width='80%'>
    <tr>
      <td><img src=<?php echo $this->_tpl_vars['priority_img']; ?>
> <b>(<?php echo $this->_tpl_vars['id']; ?>
) Message from: <?php echo $this->_tpl_vars['from']; ?>
 (<?php echo $this->_tpl_vars['datetime']; ?>
)</b></td>
      <td align=right>[ <a href=<?php echo $this->_tpl_vars['delete_href']; ?>
></b>X</b></a> ]</td>
    </tr>
    <tr><td colspan=2>Subject: <?php echo $this->_tpl_vars['subject']; ?>
</td></tr>
    <tr><td colspan=2><?php echo $this->_tpl_vars['body']; ?>
</td></tr>
  </table>
  <br>
  <br>
  
<!-- End messages-normal-msg -->