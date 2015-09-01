<?php /* Smarty version 2.6.2, created on 2004-06-06 15:56:19
         compiled from /home/joshua/WWW/themes/Perihelion/preferences.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', '/home/joshua/WWW/themes/Perihelion/preferences.tpl', 3, false),array('function', 'validate', '/home/joshua/WWW/themes/Perihelion/preferences.tpl', 20, false),array('function', 'html_select_date', '/home/joshua/WWW/themes/Perihelion/preferences.tpl', 57, false),array('function', 'html_options', '/home/joshua/WWW/themes/Perihelion/preferences.tpl', 102, false),array('modifier', 'default', '/home/joshua/WWW/themes/Perihelion/preferences.tpl', 23, false),)), $this); ?>
<!-- preferences-form -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ========================================================================================================

 ========================================================================================================
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

<form method='post' action='<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
'>
<input type=hidden name=cmd value='<?php echo $this->_tpl_vars['cmd']; ?>
'>
<input type=hidden name=frmid value='<?php echo $this->_tpl_vars['frmid']; ?>
'>
<input type=hidden name=uid value='<?php echo $this->_tpl_vars['uid']; ?>
'>

<table class='standard' align='center'>
  <tr><th colspan=2>Change Preferences</th></tr>  
  <tr><td colspan=2>&nbsp;</td></tr>
  
  <?php echo smarty_function_validate(array('field' => 'name','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Name cannot be empty</td></tr>'), $this);?>

  <tr>        
    <td>&nbsp;Name:</td>
    <td><input type='text' name='name' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['name'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>
  <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your email address cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'isEmail','message' => '<tr class="false"><td colspan="2"><li>Email should be a valid email address</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'isCustom','function' => 'validate_email_is_ours_or_does_not_exists','message' => '<tr class="false"><td colspan="2"><li>Email address is already registered</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;Email:</td>
    <td><input type='text' name='email' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['email'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30'></td>
  </tr>
  <tr>  
    <td>&nbsp;</td>
    <td><input type='checkbox' <?php if (isset ( $this->_tpl_vars['inform'] ) && $this->_tpl_vars['inform'] == 'on'): ?>checked<?php endif; ?> name='inform'>Spam me the latest Perihelion news!</td>
  </tr>
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>  
    <td>&nbsp;Gender:</td>
    <td>
      <table border='0' width='100%'>
        <tr>
          <td><input type=radio <?php if (! isset ( $this->_tpl_vars['gender'] ) || $this->_tpl_vars['gender'] == 'M'): ?>checked<?php endif; ?> name=gender value='M'>Male</td>
          <td><input type=radio <?php if (isset ( $this->_tpl_vars['gender'] ) && $this->_tpl_vars['gender'] == 'F'): ?>checked<?php endif; ?> name=gender value='F'>Female</TD>
        </tr>
      </table>
    </td>
  </tr>
  
  <?php if (isset ( $this->_tpl_vars['dob_Day'] )): ?>
    <?php $this->assign('dob', ($this->_tpl_vars['dob_Year'])."-".($this->_tpl_vars['dob_Month'])."-".($this->_tpl_vars['dob_Day'])); ?>
  <?php else: ?>
    <?php $this->assign('dob', "smarty.now"); ?>
  <?php endif; ?>
  <tr>   
    <td>&nbsp;Date of Birth:</td>
    <td><?php echo smarty_function_html_select_date(array('prefix' => 'dob_','field_order' => 'DMY','time' => $this->_tpl_vars['dob'],'start_year' => "-80",'end_year' => "+1"), $this);?>
</td>
  </tr>    
  
  <?php echo smarty_function_validate(array('field' => 'city','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your city cannot be empty</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;City:</td>
    <td><input type='text' name='city' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['city'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='50'></td>
  </tr>
  
  <?php echo smarty_function_validate(array('field' => 'country','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your country cannot be empty</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;Country:</td>
    <td><input type='text' name='country' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['country'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>    

  <tr><td colspan=2>&nbsp;Only fill this in when you want to change the current password:</td></tr>    
  <?php if (isset ( $this->_tpl_vars['login_pass1'] ) && $this->_tpl_vars['login_pass1'] == ""): ?>
    <?php $this->assign('login_pass1', ' '); ?>
  <?php endif; ?>
  <?php if (isset ( $this->_tpl_vars['login_pass2'] ) && $this->_tpl_vars['login_pass2'] == ""): ?>
    <?php $this->assign('login_pass2', ' '); ?>
  <?php endif; ?>
  <?php echo smarty_function_validate(array('field' => 'login_pass1','criteria' => 'isEqual','field2' => 'login_pass2','message' => '<tr class="false" colspan="2"><td><li>Your passwords do not match</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;Password:</td>
    <td><input type='password' name='login_pass1' size='30' maxlength='30'></td>
  </tr>  
  <tr>   
    <td>&nbsp;Retype password:</td>
    <td><input type='password' name='login_pass2' size='30' maxlength='30'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>   
    <td>&nbsp;Tag Line:</td>
    <td><input type='text' name='tag' value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['tag'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
" size='30' maxlength='200'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>   
    <td>&nbsp;Perihelion Theme:</td>
    <?php if (! isset ( $this->_tpl_vars['theme'] )): ?>      
      <?php $this->assign('theme', ' '); ?>
    <?php endif; ?>
    <td><?php echo smarty_function_html_options(array('name' => 'theme','values' => $this->_tpl_vars['themes_ids'],'output' => $this->_tpl_vars['themes_names'],'selected' => $this->_tpl_vars['theme']), $this);?>
</td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  <?php echo smarty_function_validate(array('field' => 'current_pass','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your current password cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'current_pass','criteria' => 'isCustom','function' => 'validate_passwd','message' => '<tr class="false" colspan="2"><td><li>Invalid password.</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;Current password:</td>
    <td><input type='password' name='current_pass' size='30' maxlength='30'></td>
  </tr>  

  
  <tr><td>&nbsp;</td><td><input type='submit' name='submit' value='Set Preferences'></td></tr>
  <tr><td colspan=2>&nbsp;</td></tr>
</table>
 
</form>

<!-- end preferences-form -->