<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  //session_identification (); 
  session_start();  // We don't need to be logged in to use the help system i think...
  
  
  print_header ();
  print_title ("Help",
               "Here we will try to tell you everything you need to know about Perihelion.");
               
  $topic = decrypt_get_vars ($_REQUEST['hid']);
  
  $template = new Smarty ();
  
  $result = sql_query ("SELECT * FROM perihelion.help WHERE id LIKE '$topic'");
  if (sql_countrows ($result) == 0) {  	
  	$template->assign ("help", "");
  } else {
  	$row = sql_fetchrow ($result);
    $template->assign ("topic", $topic);
    $template->assign ("help", convert_px_to_html_tags ($row['help']));
  }
  
  $template->display ($_RUN['theme_path']."/help.tpl");

  print_footer ();
  exit;

?>