<?php /* Smarty version 2.6.2, created on 2004-06-03 12:38:05
         compiled from Perihelion/./login.tpl */ ?>
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
            <tr><td>&nbsp;     </td><td><?php echo $this->_tpl_vars['registerref']; ?>
</td></tr>
            <tr><td>&nbsp;     </td><td><?php echo $this->_tpl_vars['forgotpassref']; ?>
</td></tr>
            <tr><td>&nbsp;     </td><td><a href=about.php>Click here to find out more about Perihelion.</a></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2">Questions? Good! Email them at:</td></tr>
			  	  <tr><td colspan="2"><img src=<?php echo $this->_tpl_vars['email']; ?>
></td></tr>          
            <tr><td colspan="2"><?php echo $this->_tpl_vars['nowplaying']; ?>
</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td class=false colspan=2><b>&nbsp;<?php echo $this->_tpl_vars['errorcode']; ?>
</b></td></tr>  
            <tr><td colspan="2">&nbsp;</td></tr>
          </table>
        </td>
      </tr>
    </table>
  </form>

<!-- End Login -->