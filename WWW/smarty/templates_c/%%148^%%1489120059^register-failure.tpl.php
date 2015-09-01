<?php /* Smarty version 2.6.2, created on 2004-06-03 12:33:11
         compiled from Perihelion/./register-failure.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', 'Perihelion/./register-failure.tpl', 3, false),)), $this); ?>
<!-- register-failure -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ========================================================================================================
  
  Shown after a failed registration
  
 ========================================================================================================
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

<table align=center>
<tr><td>
 There was an error while registrating you to the system.<br>
  <a href='register.php'>Click here to try again</a> or <a href='index.php'>here to go to the main page...</a>
</td></tr>
</table>


<!-- end register-failure -->