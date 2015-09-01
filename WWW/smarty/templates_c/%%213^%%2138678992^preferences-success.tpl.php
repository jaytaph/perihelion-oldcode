<?php /* Smarty version 2.6.2, created on 2004-06-06 15:56:26
         compiled from /home/joshua/WWW/themes/Simple/preferences-success.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', '/home/joshua/WWW/themes/Simple/preferences-success.tpl', 3, false),)), $this); ?>
<!-- preferences-form -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ========================================================================================================

 ========================================================================================================
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

 				Preferences are set..<br>
 				<br>
 				Maybe you should logout first to see any theme-changes...<br>
			  <br>
			  
<!-- end preferences-form -->