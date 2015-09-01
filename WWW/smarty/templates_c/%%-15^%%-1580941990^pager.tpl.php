<?php /* Smarty version 2.6.2, created on 2004-06-07 08:35:51
         compiled from /home/joshua/WWW/themes/Perihelion/pager.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'pager', '/home/joshua/WWW/themes/Perihelion/pager.tpl', 3, false),)), $this); ?>
<!-- pager -->

<?php echo smarty_function_pager(array('rowcount' => $this->_tpl_vars['rowcount'],'limit' => 25,'shift' => 0,'show' => 'page','pos' => 'pager_pos','forwardvars' => "",'no_first' => true,'separator' => "|",'txt_pos' => 'side','pre_tag' => "[ ",'post_tag' => " ]",'class_num' => 'pager_num','class_numon' => 'pager_numon','class_text' => 'pager_text'), $this);?>


<!-- end pager -->