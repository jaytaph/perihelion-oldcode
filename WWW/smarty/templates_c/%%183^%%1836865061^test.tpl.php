<?php /* Smarty version 2.6.2, created on 2004-06-01 11:08:18
         compiled from test.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'test.tpl', 4, false),array('function', 'html_options', 'test.tpl', 8, false),)), $this); ?>
<html>

<head>
  <title><?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, 'No Title Found') : smarty_modifier_default($_tmp, 'No Title Found')); ?>
</title>
</head>

<select name=user>
<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['id'],'output' => $this->_tpl_vars['names'],'selected' => '5'), $this);?>

</select>

</html>