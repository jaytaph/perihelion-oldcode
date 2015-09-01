<!-- register-form -->

{comment}
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
    {validate field='email' criteria='isCustom' function='validate_email' message='email addresse is already registered'}
    
  Questions? I don't think so..
  
 ========================================================================================================
-->
{/comment}

<form method='post' action='{$SCRIPT_NAME}'>
<table class='standard' align='center'>
  <tr><th colspan=2>Register as a new user</th></tr>  
  <tr><td colspan=2>&nbsp;</td></tr>
  
  {validate field='name' criteria='notEmpty' message='<tr ><td colspan="2"><li><b>Name cannot be empty</b></td></tr>'}
  <tr>        
    <td>&nbsp;Name:</td>
    <td><input type='text' name='name' value='{$name|default}' size='30' maxlength='30'></td>
  </tr>
  {validate field='email' criteria='notEmpty' message='<tr><td colspan="2"><li><b>Your email address cannot be empty</b></td></tr>'}
  {validate field='email' criteria='isEmail'  message='<tr><td colspan="2"><li><b>Email should be a valid email address</b></td></tr>'}
  {validate field='email' criteria='isCustom' function='validate_email' message='<tr ><td colspan="2"><li><b>Email address is already registered</b></td></tr>'}
  <tr>   
    <td>&nbsp;{$help_register_email} Email:</td>
    <td><input type='text' name='email' value='{$email|default}' size='30'></td>
  </tr>
  <tr>  
    <td>&nbsp;</td>
    <td><input type='checkbox' {if isset($inform) && $inform eq "on"}checked{/if} name='inform'>Spam me the latest Perihelion news!</td>
  </tr>
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>  
    <td>&nbsp;Gender:</td>
    <td>
      <table border='0' width='100%'>
        <tr>
          <td><input type=radio {if !isset($gender) || $gender eq "M"}checked{/if} name=gender value=M>Male</td>
          <td><input type=radio {if isset($gender) && $gender eq "F"}checked{/if} name=gender value=F>Female</TD>
        </tr>
      </table>
    </td>
  </tr>
  
  {if isset($dob_Day)}
    {assign var="dob" value="$dob_Year-$dob_Month-$dob_Day"}
  {else}
    {assign var="dob" value="smarty.now"}
  {/if}
  <tr>   
    <td>&nbsp;Date of Birth:</td>
    <td>{html_select_date prefix="dob_" field_order="DMY" time=$dob start_year="-80" end_year="+1"}</td>
  </tr>    
  
  {validate field='city' criteria='notEmpty' message='<tr><td colspan="2"><li><b>Your city cannot be empty</b></td></tr>'}
  <tr>   
    <td>&nbsp;City:</td>
    <td><input type='text' name='city' value='{$city|default}' size='30' maxlength='50'></td>
  </tr>
  
  {validate field='country' criteria='notEmpty' message='<tr><td colspan="2"><li><b>Your country cannot be empty</b></td></tr>'}
  <tr>   
    <td>&nbsp;Country:</td>
    <td><input type='text' name='country' value='{$country|default}' size='30' maxlength='30'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>    

  {validate field='login_name' criteria='notEmpty' message='<tr><td colspan="2"><li><b>Your login name cannot be empty</b></td></tr>'}
  {validate field='login_name' criteria='isCustom' function='validate_login' message='<tr ><td colspan="2"><li><b>Login name is already registered</b></td></tr>'}
  <tr>   
    <td>&nbsp;Login name:</td>
    <td><input type='text' name='login_name' value='{$login_name|default}' size='30' maxlength='30'></td>
  </tr>  
  {validate field='login_pass1' criteria='notEmpty' message='<tr colspan="2"><td><li><b>Your password cannot be empty</b></td></tr>'}
  {validate field='login_pass1' criteria='isEqual'  field2='login_pass2' message='<tr  colspan="2"><td><li><b>Your passwords do not match</b></td></tr>'}
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
    <td>&nbsp;{$help_register_tag} Tag Line:</td>
    <td><input type='text' name='tag' value='{$tag|default}' size='30' maxlength='200'></td>
  </tr>  
  {validate field='specie_name' criteria='notEmpty' message='<tr ><td colspan="2" colspan="2"><li><b>Your specie name cannot be empty</b></td></tr>'}
  {validate field='specie_name' criteria='isCustom' function='validate_specie' message='<tr ><td colspan="2"><li><b>Specie name is already registered</b></td></tr>'}
  <tr>   
    <td>&nbsp;{$help_register_speciename} Specie name:</td>
    <td><input type='text' name='specie_name' value='{$specie_name|default}' size='30' maxlength='30'></td>
  </tr>  
  {validate field='sector_name' criteria='notEmpty' message='<tr ><td colspan="2"><li><b>Your sector name cannot be empty</b></td></tr>'}
  {validate field='sector_name' criteria='isCustom' function='validate_sector' message='<tr ><td colspan="2"><li><b>Sector name is already registered</b></td></tr>'}
  <tr>   
    <td>&nbsp;{$help_register_sectorname} Sector name:</td>
    <td><input type='text' name='sector_name' value='{$sector_name|default}' size='30' maxlength='30'></td>
  </tr>  
  {validate field='planet_name' criteria='notEmpty' message='<tr ><td colspan="2"><li><b>Your planet name cannot be empty</b></td></tr>'}
  {validate field='planet_name' criteria='isCustom' function='validate_planet' message='<tr ><td colspan="2"><li><b>Planet name is already registered</b></td></tr>'}
  <tr>   
    <td>&nbsp;{$help_register_planetname} Planet name:</td>
    <td><input type='text' name='planet_name' value='{$planet_name|default}' size='30' maxlength='30'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>   
    <td>&nbsp;{$help_register_theme} Perihelion Theming:</td>
    {if not isset ($theme)}      
      {assign var="theme" value=" "}
    {/if}
    <td>{html_options name='theme' values=$themes_ids output=$themes_names selected=$theme}</td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>
  
  <tr><td>&nbsp;</td><td><input type='submit' name='submit' value='{if !isset($submit)}register yourself{else}Try again{/if}'></td></tr>
  <tr><td colspan=2>&nbsp;</td></tr>
</table>
 
</form>

<!-- end register-form -->