<?php /* Smarty version 2.6.2, created on 2004-06-03 12:27:27
         compiled from Perihelion/./help.tpl */ ?>
<!-- help -->

  <?php if ($this->_tpl_vars['help'] == ""): ?> 
    <table class=standard align=center width=80%>
  	  <tr><td>Sorry but we cannot find any information about this help topic.</td></tr>
    </table>
  <?php else: ?>
    <table class=standard align=center width=80%>
  	  <tr><th><?php echo $this->_tpl_vars['topic']; ?>
</th></tr>
  	  <tr><td><?php echo $this->_tpl_vars['help']; ?>
</td></tr>
    </table>
  <?php endif; ?>    
  <br>
  <br>
  
  <table align=center border=0>
    <form><input type='button' value='Close Window' onClick='window.close()'></form>
  </table>
  <br>
  <br>
  
<!-- End help -->