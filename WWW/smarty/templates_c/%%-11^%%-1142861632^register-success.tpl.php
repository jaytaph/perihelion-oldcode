<?php /* Smarty version 2.6.2, created on 2004-06-02 13:55:42
         compiled from register-success.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', 'register-success.tpl', 3, false),)), $this); ?>
<!-- register-success -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ========================================================================================================
  
  Shown after a successfull registration
  
 ========================================================================================================
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

<table align=center>
<tr><td>
  <a href='index.php'>Your registration was successfull. Now click here to login the system and start the game...</a>
</td></tr>
</table>


<!-- end register-success -->