<!-- preferences-form -->

{comment}
<!--
 ========================================================================================================

 ========================================================================================================
-->
{/comment}

<form method='post' action='{$SCRIPT_NAME}'>
<input type=hidden name=cmd value='{$cmd}'>
<input type=hidden name=frmid value='{$frmid}'>
<input type=hidden name=uid value='{$uid}'>

<table class='standard' align='center'>
  <tr><th colspan=2>Change Preferences</th></tr>  
  <tr><td colspan=2>&nbsp;</td></tr>
  
  {validate field='name' criteria='notEmpty' message='<tr class="false"><td colspan="2"><li>Name cannot be empty</td></tr>'}
  <tr>        
    <td>&nbsp;Name:</td>
    <td><input type='text' name='name' value='{$name|default}' size='30' maxlength='30'></td>
  </tr>
  {validate field='email' criteria='notEmpty' message='<tr class="false"><td colspan="2"><li>Your email address cannot be empty</td></tr>'}
  {validate field='email' criteria='isEmail'  message='<tr class="false"><td colspan="2"><li>Email should be a valid email address</td></tr>'}
  {validate field='email' criteria='isCustom' function='validate_email_is_ours_or_does_not_exists' message='<tr class="false"><td colspan="2"><li>Email address is already registered</td></tr>'}
  <tr>   
    <td>&nbsp;Email:</td>
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
          <td><input type=radio {if !isset($gender) || $gender eq "M"}checked{/if} name=gender value='M'>Male</td>
          <td><input type=radio {if isset($gender) && $gender eq "F"}checked{/if} name=gender value='F'>Female</TD>
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
  
  {validate field='city' criteria='notEmpty' message='<tr class="false"><td colspan="2"><li>Your city cannot be empty</td></tr>'}
  <tr>   
    <td>&nbsp;City:</td>
    <td><input type='text' name='city' value='{$city|default}' size='30' maxlength='50'></td>
  </tr>
  
  {validate field='country' criteria='notEmpty' message='<tr class="false"><td colspan="2"><li>Your country cannot be empty</td></tr>'}
  <tr>   
    <td>&nbsp;Country:</td>
    <td><input type='text' name='country' value='{$country|default}' size='30' maxlength='30'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>    

  <tr><td colspan=2>&nbsp;Only fill this in when you want to change the current password:</td></tr>    
  {if isset($login_pass1) and $login_pass1 eq ""}
    {assign var="login_pass1" value=" "}
  {/if}
  {if isset($login_pass2) and $login_pass2 eq ""}
    {assign var="login_pass2" value=" "}
  {/if}
  {validate field='login_pass1' criteria='isEqual' field2='login_pass2' message='<tr class="false" colspan="2"><td><li>Your passwords do not match</td></tr>'}
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
    <td><input type='text' name='tag' value="{$tag|default}" size='30' maxlength='200'></td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  <tr>   
    <td>&nbsp;Perihelion Theme:</td>
    {if not isset ($theme)}      
      {assign var="theme" value=" "}
    {/if}
    <td>{html_options name='theme' values=$themes_ids output=$themes_names selected=$theme}</td>
  </tr>  
  <tr><td colspan=2>&nbsp;</td></tr>

  {validate field='current_pass' criteria='notEmpty' message='<tr class="false"><td colspan="2"><li>Your current password cannot be empty</td></tr>'}
  {validate field='current_pass' criteria='isCustom' function='validate_passwd' message='<tr class="false" colspan="2"><td><li>Invalid password.</td></tr>'}
  <tr>   
    <td>&nbsp;Current password:</td>
    <td><input type='password' name='current_pass' size='30' maxlength='30'></td>
  </tr>  

  
  <tr><td>&nbsp;</td><td><input type='submit' name='submit' value='Set Preferences'></td></tr>
  <tr><td colspan=2>&nbsp;</td></tr>
</table>
 
</form>

<!-- end preferences-form -->