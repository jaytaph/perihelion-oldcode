<!-- Login -->

  <form method="post" action="login.php">
    <table align="center" border="0">  
      <tr>
        <td>
          <img src="http://62.195.19.164/perihelion/images/backgrounds/perihelion.jpg">
        </td>  
        <td bgcolor="black" valign="top">
          <table align="center" border="0" width="400" bgcolor="black">
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>        
            <tr><td>Login name:</td><td><input tabindex="1" type="text"     maxlength="30" name="name"></td></tr>
            <tr><td>Login pass:</td><td><input tabindex="2" type="password" maxlength="30" name="pass"></td></tr>
            <tr><td>&nbsp;     </td><td><input tabindex="3" type="submit"   name="submit"  value="Login PeriHelion"></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>         
            <tr><td>&nbsp;     </td><td>{$registerref}</td></tr>
            <tr><td>&nbsp;     </td><td>{$forgotpassref}</td></tr>
            <tr><td>&nbsp;     </td><td><a href=about.php>Click here to find out more about Perihelion.</a></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2">Questions? Good! Email them at:</td></tr>
			  	  <tr><td colspan="2"><img src={$email}></td></tr>          
            <tr><td colspan="2">{$nowplaying}</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td class=false colspan=2><b>&nbsp;{$errorcode}</b></td></tr>  
            <tr><td colspan="2">&nbsp;</td></tr>
          </table>
        </td>
      </tr>
    </table>
  </form>

<!-- End Login -->
