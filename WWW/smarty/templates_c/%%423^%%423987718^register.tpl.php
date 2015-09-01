<?php /* Smarty version 2.6.2, created on 2004-06-03 13:55:55
         compiled from /home/joshua/WWW/themes/Perihelion/register.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'comment', '/home/joshua/WWW/themes/Perihelion/register.tpl', 3, false),array('function', 'validate', '/home/joshua/WWW/themes/Perihelion/register.tpl', 24, false),array('function', 'html_select_date', '/home/joshua/WWW/themes/Perihelion/register.tpl', 74, false),array('function', 'html_options', '/home/joshua/WWW/themes/Perihelion/register.tpl', 137, false),array('modifier', 'default', '/home/joshua/WWW/themes/Perihelion/register.tpl', 40, false),)), $this); ?>
<!-- register-form -->

<?php $this->_tag_stack[] = array('comment', array()); smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true);while ($_block_repeat) { ob_start(); ?>
<!--
 ========================================================================================================
  This template lets users register to perihelion. It consists of a few simple smartyvalidate lines with
  a few tricks explained here.
  
  I use a variable names $dob to concat year/month/day because they are normally 3 different
  variables. This variable is used in the html_select_date tag.
  
  Because of the high warning in my php-code, i need to use isset() and !isset() tags occasionally
  to make sure i don't get any warnings.
  
  To validate certain items i created a few php-validate routines:
  
    valid_email
    valid_login
    valid_species
    valid_sector
    valid_planet
    
  These items should be used in the form:
    <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'isCustom','function' => 'validate_email','message' => 'email addresse is already registered'), $this);?>

    
  Questions? I don't think so..
  
 ========================================================================================================
-->
<?php $this->_block_content = ob_get_contents(); ob_end_clean(); echo smarty_block_comment($this->_tag_stack[count($this->_tag_stack)-1][1], $this->_block_content, $this, $_block_repeat=false); }  array_pop($this->_tag_stack); ?>

<form method='post' action='<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
'>
<table class='standard' align='center'>
  <tr><th colspan=2>Register as a new user</th></tr>  
  <tr><td colspan=2>&nbsp;</td></tr>
  
  <?php echo smarty_function_validate(array('field' => 'name','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Name cannot be empty</td></tr>'), $this);?>

  <tr>        
    <td>&nbsp;Name:</td>
    <td><input type='text' name='name' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['name'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>
  <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your email address cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'isEmail','message' => '<tr class="false"><td colspan="2"><li>Email should be a valid email address</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'email','criteria' => 'isCustom','function' => 'validate_email','message' => '<tr class="false"><td colspan="2"><li>Email address is already registered</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;<?php echo $this->_tpl_vars['help_register_email']; ?>
 Email:</td>
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
          <td><input type=radio <?php if (! isset ( $this->_tpl_vars['gender'] ) || $this->_tpl_vars['gender'] == 'M'): ?>checked<?php endif; ?> name=gender value=M>Male</td>
          <td><input type=radio <?php if (isset ( $this->_tpl_vars['gender'] ) && $this->_tpl_vars['gender'] == 'F'): ?>checked<?php endif; ?> name=gender value=F>Female</TD>
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

  <?php echo smarty_function_validate(array('field' => 'login_name','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your login name cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'login_name','criteria' => 'isCustom','function' => 'validate_login','message' => '<tr class="false"><td colspan="2"><li>Login name is already registered</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;Login name:</td>
    <td><input type='text' name='login_name' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['login_name'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>  
  <?php echo smarty_function_validate(array('field' => 'login_pass1','criteria' => 'notEmpty','message' => '<tr class="false" colspan="2"><td><li>Your password cannot be empty</td></tr>'), $this);?>

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
    <td>&nbsp;<?php echo $this->_tpl_vars['help_register_tag']; ?>
 Tag Line:</td>
    <td><input type='text' name='tag' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['tag'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='200'></td>
  </tr>  
  <?php echo smarty_function_validate(array('field' => 'specie_name','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2" colspan="2"><li>Your specie name cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'specie_name','criteria' => 'isCustom','function' => 'validate_specie','message' => '<tr class="false"><td colspan="2"><li>Specie name is already registered</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;<?php echo $this->_tpl_vars['help_register_speciename']; ?>
 Specie name:</td>
    <td><input type='text' name='specie_name' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['specie_name'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>  
  <?php echo smarty_function_validate(array('field' => 'sector_name','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your sector name cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'sector_name','criteria' => 'isCustom','function' => 'validate_sector','message' => '<tr class="false"><td colspan="2"><li>Sector name is already registered</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;<?php echo $this->_tpl_vars['help_register_sectorname']; ?>
 Sector name:</td>
    <td><input type='text' name='sector_name' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['sector_name'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>  
  <?php echo smarty_function_validate(array('field' => 'planet_name','criteria' => 'notEmpty','message' => '<tr class="false"><td colspan="2"><li>Your planet name cannot be empty</td></tr>'), $this);?>

  <?php echo smarty_function_validate(array('field' => 'planet_name','criteria' => 'isCustom','function' => 'validate_planet','message' => '<tr class="false"><td colspan="2"><li>Planet name is already registered</td></tr>'), $this);?>

  <tr>   
    <td>&nbsp;<?php echo $this->_tpl_vars['help_register_planetname']; ?>
 Planet name:</td>
    <td><input type='text' name='planet_name' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['planet_name'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
' size='30' maxlength='30'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>   
    <td>&nbsp;<?php echo $this->_tpl_vars['help_register_theme']; ?>
 Perihelion Theming:</td>
    <?php if (! isset ( $this->_tpl_vars['theme'] )): ?>      
      <?php $this->assign('theme', ' '); ?>
    <?php endif; ?>
    <td><?php echo smarty_function_html_options(array('name' => 'theme','values' => $this->_tpl_vars['themes_ids'],'output' => $this->_tpl_vars['themes_names'],'selected' => $this->_tpl_vars['theme']), $this);?>
</td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>
  
  <tr><td>&nbsp;</td><td><input type='submit' name='submit' value='<?php if (! isset ( $this->_tpl_vars['submit'] )): ?>register yourself<?php else: ?>Try again<?php endif; ?>'></td></tr>
  <tr><td colspan=2>&nbsp;</td></tr>
</table>
 
</form>

<!-- end register-form -->