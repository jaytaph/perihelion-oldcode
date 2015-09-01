<?php
    // Include Files
    include "includes.inc.php";
 
  print_header ("", "no");
    
  $template = new Smarty ();  
  $template->display ($_RUN['theme_path']."/about.tpl");
 
  print_footer ();


