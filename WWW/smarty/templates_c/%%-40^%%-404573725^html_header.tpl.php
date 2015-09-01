<?php /* Smarty version 2.6.2, created on 2004-06-03 12:15:38
         compiled from Perihelion//html_header.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'Perihelion//html_header.tpl', 4, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
  <head>
    <title><?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, "Perihelion - The Game") : smarty_modifier_default($_tmp, "Perihelion - The Game")); ?>
</title>
    
    <!-- All code copyright (C) 2004 Joshua Thijssen -->
    
    <meta http-equiv="Content-Style-Type" content="text/css">
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['css_path']; ?>
" type="text/css">
          
    <basefont face="Arial" size="2" />
    
    <?php echo $this->_tpl_vars['extra_headers']; ?>


		<?php echo '
    <script type="text/javascript" language="javascript">
       function targetBlank (url) {
         blankWin = window.open(url,"_blank","width=640, height=400, menubar=no, toolbar=no, location=no, directories=no, fullscreen=no, titlebar=no, hotkeys=no, status=no, scrollbars=no, resizable=yes");       
       }
    </script>
    '; ?>


  </head>
  
  <body bgcolor="black" <?php echo ((is_array($_tmp=@$this->_tpl_vars['background'])) ? $this->_run_mod_handler('default', true, $_tmp, "") : smarty_modifier_default($_tmp, "")); ?>
 text="white" link="white" vlink="white" alink="white" <?php echo $this->_tpl_vars['body_tags']; ?>
>

<!-- End Header -->