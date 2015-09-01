<?php /* Smarty version 2.6.2, created on 2004-06-03 12:15:38
         compiled from Perihelion//html_title.tpl */ ?>
<!-- Title -->

  <center><h1><b><?php echo $this->_tpl_vars['title']; ?>
</b></h1></center>
      
<?php if ($this->_tpl_vars['description'] != ""): ?>
  <!-- description --> 
  <table align=center border=0 width=75%>
    <tr>
      <td class=ylw>
        <center><?php echo $this->_tpl_vars['description']; ?>
</center>
      </td>
    </tr>
  </table>  
<?php endif; ?>
  
  <br>
  <br>

<!-- End Title -->