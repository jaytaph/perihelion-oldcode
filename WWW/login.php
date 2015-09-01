<?php
    // Include Files
    include "includes.inc.php";

    if (!isset ($_POST['submit'])) {
        login ("");
    } elseif (empty ($_POST['name'])) {
        login ("Please enter username!");
    } elseif (empty ($_POST['pass'])) {
        login ("Please enter password!");
    } else {
        $result = sql_query ("SELECT * FROM perihelion.u_users WHERE login_name LIKE \"".$_POST['name']."\"");
        $row = sql_fetchrow ($result);
        if (!empty ($row)) {
            $result = sql_query ("SELECT PASSWORD(\"".$_POST['pass']."\")");
            $row2 = sql_fetchrow ($result);
            if ($row2[0]==$row['login_pass']) {           	
                session_init ($row);
            	                
                // Send to server
                comm_init_server ();
                $data['id']=$row['id'];
                $data['sess_id']=session_id();
                comm_s2s ("LOGIN", $data);
                comm_fini_server ();

                // And go to index page
                passtrough ($_CONFIG['URL']."/index.php");
            } else {
                login ("Wrong password!");
            }
        } else {
            login ("User does not exist!");
        }
    }
    exit;




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
//
function login ($comment) {	
  assert (isset($comment));
  global $_CONFIG;
  global $_RUN;

  $musics = array ("lanoyee.mp3", "dishes.mp3", "dishes2.mp3");  
  $musics_full = array ("La Noyee - Yann Tiersen - Le fabuleux destin d'Amelie poulain", "Dishes - Yann Tiersen - Goodbye Lenin", "Dishes - Yann Tiersen - Goodbye Lenin");
  $idx = array_rand ($musics);
  $mp3 = $musics[$idx];
  $mp3full = $musics_full[$idx];
    
  $extra_header = "<script language='JavaScript' type='text/JavaScript'>
                   <!--
                      function MM_controlSound(x, _sndObj, sndFile) { //v3.0
                        var i, method = '', sndObj = eval(_sndObj);
                        if (sndObj != null) { 
                          if (navigator.appName == 'Netscape') method = 'play';
                        else {
                          if (window.MM_WMP == null) {
                            window.MM_WMP = false;
                            for(i in sndObj) if (i == 'ActiveMovie') {
                              window.MM_WMP = true; break;
                            }
                          }
                          if (window.MM_WMP) method = 'play';
                            else if (sndObj.FileName) method = 'run';
                          } 
                        }
                        if (method) eval(_sndObj+'.'+method+'()');
                        else window.location = sndFile;
                      }

                      function MM_openBrWindow(theURL,winName,features) { //v2.0
                        window.open(theURL,winName,features);
                      }
                   //-->
                  </script>
                  <EMBED NAME='CS1084698965752' SRC='$mp3' LOOP=true AUTOSTART=true MASTERSOUND HIDDEN=true WIDTH=0 HEIGHT=0></EMBED>
                  ";

  
  
  print_header ($extra_header, "no", "onLoad='MM_controlSound('play','document.CS1084698965752','$mp3')");
    
  $template = new Smarty();

  $template->assign ("image", $_CONFIG['IMAGE_URL']."/backgrounds/perihelion.jpg");
  $template->assign ("email", $_CONFIG['IMAGE_URL']."/backgrounds/email.gif");
  if ($comment == "") {
  	$template->assign ("errorcode", "");
  } else {
    $template->assign ("errorcode", "<li>".$comment."</li>");
  }
  
  $template->assign ("registerref", "Don't click here to register as a new user,");
  $template->assign ("forgotpassref", "and not here when you forgot your password...");
  $template->assign ("nowplaying", "Now playing:<br>$mp3full");
  
  $template->display ($_RUN['theme_path']."/login.tpl");  
 


// Project Sizing
  $h_l = $h_w = $h_c = 0;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/px2/heartbeat/*.pl");
  $h_l += $l; $h_w += $w; $h_c += $c;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/px2/heartbeat/*.inc");
  $h_l += $l; $h_w += $w; $h_c += $c;
  
  $s_l = $s_w = $s_c = 0;  
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/px2/server/*.pl");
  $s_l += $l; $s_w += $w; $s_c += $c;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/px2/server/*.inc");
  $s_l += $l; $s_w += $w; $s_c += $c;
  
  $g_l = $g_w = $g_c = 0;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/px2/globalperl/*.pm");
  $g_l += $l; $g_w += $w; $g_c += $c;

  $w_l = $w_w = $w_c = 0;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/WWW/*.php");
  $w_l += $l; $w_w += $w; $w_c += $c;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/WWW/admin/*.php");
  $w_l += $l; $w_w += $w; $w_c += $c;

  $t_l = $t_w = $t_c = 0;
  list ($l, $w, $c) = grab_wc_totals ("/home/joshua/WWW/themes/Perihelion/*.tpl");
  $t_l += $l; $t_w += $w; $t_c += $c;
  
  
  
  echo "<table width=75% border=3 align=center bordercolor=#353550 bordercolorlight=#9595B0 bordercolordark=#555570>\n";
  echo "<tr><th colspan=4>Project Sizing</th></tr>\n";
  echo "<tr><td>Module</td><td>Lines of Code</td><td>Words of code</td><td>Chars of code</td></tr>";
  echo "<tr><td>Website (PHP code)</td>    <td>$w_l</td><td>$w_w</td><td>$w_c</td></tr>\n";
  echo "<tr><td>Website (Templates)</td>   <td>$t_l</td><td>$t_w</td><td>$t_c</td></tr>\n";
  echo "<tr><td>Heartbeat (Perl code)</td> <td>$h_l</td><td>$h_w</td><td>$h_c</td></tr>\n";
  echo "<tr><td>Server (Perl code)</td>    <td>$s_l</td><td>$s_w</td><td>$s_c</td></tr>\n";
  echo "<tr><td>Global (Perl code)</td>    <td>$g_l</td><td>$g_w</td><td>$g_c</td></tr>\n";
  echo "<tr><td><b>Total</b></td>          <td><b>".($w_l+$t_l+$h_l+$s_l+$g_l)."</b></td><td><b>".($w_w+$t_w+$h_w+$s_w+$g_w)."</b></td><td><b>".($w_c+$t_c+$h_c+$s_c+$g_c)."</b></td></tr>\n";
  echo "</table>\n"; 
  echo "<br>\n";
  echo "<br>\n";
 
// Show the TODO list on the login page... This makes the page a little bit 
// more interessting for non perihelion users... 
  
  $handle = fopen ("_TODO", "r");
  $todo = fread ($handle, 81920);
  fclose ($handle);
  
  $col = "white";
  
  $p1 = strpos ($todo, "[TODO]") + 6;
  $p2 = strpos ($todo, "[/TODO]");
    
  $itemlist = "";  
  $items = 0;
  $todo = substr ($todo, $p1, $p2-$p1);
  $todo = split ("\n", $todo);
  foreach ($todo as $line) {
  	if ($line == "") continue;
  	$tmp = substr ($line, 0, 2);
  	if ($tmp == "- ") $col = "blue";
  	if ($tmp == "V ") $col = "green";
  	if ($tmp == "* ") {
  		$col = "white";
  		$items++;
  	}
  	if ($tmp == "C ") $col = "yellow";
  	if ($tmp == "U ") $col = "red";  	
    $itemlist .= "<font color=$col>".htmlspecialchars($line)."</font>\n";
  }
  
  
  echo "<table width=75% border=3 align=center bordercolor=#353550 bordercolorlight=#9595B0 bordercolordark=#555570>";
  echo "<tr><th>ToDo List<br>( $items Items Remaining )</th></tr><tr><td>";
  echo "<pre>";
  echo $itemlist; 
  echo "</pre>";
  echo "</td></tr>";
  echo "</table>";
  
 
  print_footer ();
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
//
function passtrough ($url) {
  assert (!empty($url));

  print_header ("<meta http-equiv=\"refresh\" CONTENT=\"1; URL=$url\">");
  print_subtitle ("<b>Loading...</b>");  
  print_line ("<a href=\"$url\">Click here if you are not being redirected...</a>");
  print_footer ();
}




function grab_wc_totals ($cmd) {
	$l = $w = $c = 0;
	
	exec ("wc ".$cmd, $output);
	foreach ($output as $line) {	
		if (preg_match ("/(\d+)\s+(\d+)\s+(\d+) total/", $line, $matches)) {
			$l = $matches[1];
			$w = $matches[2];
			$c = $matches[3];
		}
	}
	
	return array ($l, $w, $c);
}

?>
