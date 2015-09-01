<?php

  $start1 = gettimeofday();       // Starting time of the generation
  $end1 = 0;

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
// Converts all tags neccessary for editing..
function px2html4edit ($str) {
  $str = str_replace ("[br]", "\n", $str);
  return $str;
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
// This function converts the px tags [br], [url], [bold], [center], [big], [small]
// into the corresponding html tags. This is the safest way to let users edit html
// tags without worrying about html injections.
function convert_px_to_html_tags ($str) {
  $str = str_replace ("[br]", "<br>", $str);

  $str = str_replace ("[bold]", "<b>", $str);
  $str = str_replace ("[/bold]", "</b>", $str);

  $str = str_replace ("[sup]", "<sup>", $str);
  $str = str_replace ("[/sup]", "</sup>", $str);

  $str = str_replace ("[center]", "<center>", $str);
  $str = str_replace ("[/center]", "</center>", $str);

  $str = str_replace ("[big]", "<big>", $str);
  $str = str_replace ("[/big]", "</big>", $str);

  $str = str_replace ("[small]", "<small>", $str);
  $str = str_replace ("[/small]", "</small>", $str);

  $str = ereg_replace ("\[url ([^\[]+)\]", "<a href=\\1>", $str);
  $str = str_replace ("[/url]", "</a>", $str);

  // Do URL now...
  return $str;
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function convert_crlf_to_px_tags ($str) {
  $str = str_replace ("\r", "", $str);
  $str = str_replace ("\n", "[br]", $str);
  return $str;
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function ob_nonewline ($buf) {
  $buf = str_replace ("\n", "", $buf);
  $buf = str_replace ("\r", "", $buf);
  return $buf;
}



// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function perihelion_die ($title, $line) {
	global $_CONFIG;
	
  print_header ();
  print_title ($title);
  print_line ($line);
  print_line ("<img src=".$_CONFIG['IMAGE_URL']."/backgrounds/perihelion-small.jpg>");
  print_footer ();
  exit;
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_header ($extra_headers = "", $background = "yes", $extra_body_tags = "") {
  static $header_printed = false;

  if ($header_printed == true) return;
  $header_printed = true;

  header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
  header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
  header ("Pragma: no-cache");                          // HTTP/1.0

  $hua = $_SERVER['HTTP_USER_AGENT']."<br>\n";

  if (preg_match ('/Mozilla\/\d+\.\d+/', $hua)) {
    print_iexplore ($extra_headers, $background, $extra_body_tags);
  } else {
    print_lynx ($extra_headers);
  }
}


// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_lynx ($extra_headers) {
  echo "<html><head><title>Perihelion</title>\n";
  echo $extra_headers;
  echo "</head><body>\n";
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_iexplore ($extra_headers, $background, $extra_body_tags) {
	global $_CONFIG;
	global $_USER;
	global $_RUN;	
			
  $template = new Smarty ();
  $template->assign ("css_path", $_RUN['theme_url']."/perihelion.css");
  $template->assign ("extra_headers", $extra_headers);
  $template->assign ("body_tags", $extra_body_tags);	
  if (isset($_USER)) {
  	$template->assign ("title", "Perihelion - User: ".$_USER['login_name']."  DB: ".$_USER['galaxy_db']);
  } else {
    $template->assign ("title", "Perihelion - The Game");
  }
  if ($background == "yes") {
  	$template->assign ("background", "background='".$_CONFIG['IMAGE_URL']."/backgrounds/back2.jpg' bgproperties=fixed");
  }
  
  $template->display ($_RUN['theme_path']."/html_header.tpl"); 
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_footer () {
  // In the print_header() function we saved the current time. Now we ask for
  // the time again. This way we can calculate how long it takes for a page
  // to render. Really neat i think.
  global $start1;
  global $end1;
  global $_CONFIG;
  global $_RUN;
  
  $end1 = gettimeofday();
  $totaltime1 = (float)($end1['sec'] - $start1['sec']) +
                ((float)($end1['usec'] - $start1['usec'])/1000000);


  $template = new Smarty ();    
  $template->assign ("renderingtime", $totaltime1);
  $template->display ($_RUN['theme_path']."/html_footer.tpl");
    
  if ($_CONFIG['validate_pages'] == true) validate_html ();
}  
  

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_title ($title, $description = "") {  
  global $_RUN;
     
  $template = new Smarty ();
  $template->assign ("title", $title);
  $template->assign ("description", $description); 
  $template->display ($_RUN['theme_path']."/html_title.tpl");
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_image ($image_path) {
  echo "<table align=center border=0>\n";
  echo "  <tr><td>\n";
  echo "    <center><img src=".$image_path."></center>\n";
  echo "  </td><td>\n";
  echo "</table>\n";
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_remark ($remark) {
  echo "\n";
  echo "\n";
  echo "<!-- ".$remark." --!>\n";
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_line ($line) {
  echo "<table align=center border=0>\n";
  echo "  <tr><td>".$line."</td></tr>\n";
  echo "</table>\n";
  echo "<br><br>";
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function print_subtitle ($line) {
  echo "<h2><center><i>".$line."</i></center></h2><br>\n";
  echo "<br>\n";
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function create_submenu ($menu) {
//  echo "<center><b>Commands</b></center>\n";
  echo "<center>";

  $last_entry = end($menu);
  reset ($menu);
  while (list($title, $href) = each ($menu)) {
    echo "[ <a href=".$href.">".$title."</a> ] ";
    if ($href != $last_entry) echo " - ";
  }
  echo "</center>\n";
  echo "<br><br>\n";
}

// ============================================================================================
//
//
// Description:
//
//
// Parameters:
//
//
// Returns:
//
function validate_html () {  	
  // This is veryverysneaky.. Do not underestimate the sneakieness of this piece of code.. I want to touch your feet...  
    
  // Grab our output buffer and save it in a temporary file which is accessible by the web...
  $output = ob_get_contents ();  
  $tmpname = tempnam ("", "w3c") . ".html";
  $handle = fopen ($_CONFIG['PATH'].$tmpname, "w");
  fputs ($handle, $output);
  fclose ($handle);
  
  // Open the validator and validate the temporary file
  $URLhandle = fopen("http://validator.w3.org/check?uri=http://62.195.19.164/perihelion/".$tmpname, "r");
  //$URLhandle = fopen ($_CONFIG['PATH']."/test.txt", "r");
  $result = "";
  while (!feof($URLhandle)) {
    $result .= fread($URLhandle, 8192);
  }
  fclose ($URLhandle);
      
  // Now, parse the output of that, and show results 
  $errorsfound = 0;
  $matches = array();
  
  if (preg_match ("/Errors: <\/th>.+<td>(\d+)<\/td>/s", $result, $matches)) $errorsfound = $matches[1];
      
  echo "<table>\n";
  echo "  <tr><td>Errors:</td><td>$matches[1]</td></tr>\n";
  echo "  <tr><td>URL:</td><td><a href=http://validator.w3.org/check?uri=http://62.195.19.164/perihelion/".$tmpname.">".$tmpname."</a></td></tr>\n";
    
  $strpos = strpos ($result, "Below are the results of attempting to parse this document");
  $result = substr ($result, $strpos);
  preg_match_all ("/<em>(.+)<\/em>: ".
                  "<span class=\"msg\">(.+)<\/span><\/p><p>".
                  "<code class=\"input\">(.+)<\/code>/", $result, $matches);
  
  for ($i=0; $i < count($matches[0]); $i++) {
    echo "<tr><td valign=top>".$matches[1][$i]."</td><td><b>".$matches[2][$i]."</b><br>".$matches[3][$i]."</td></tr>";  	
  }
  echo "</table>\n";  	   
 
  // Now, only unlink the file when we have found 0 errors. Otherwise we might
  // want to use it for further inspection
  if ($errorsfound == 0) {
   	unlink ($tmpname);
  }
}


?>
