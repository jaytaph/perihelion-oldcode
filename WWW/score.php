<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Scoring board");


  $cmd = input_check ("show", "!tbl", "ofs", "uid", 0);

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    show_score ($tbl, $ofs, $uid);
    score_showuser ($uid);
  }

  print_footer ();
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
function show_score ($table, $offset, $user_id) {
  assert (is_string ($table));
  assert (is_string ($offset));
  assert (is_numeric ($user_id));
  global $_CONFIG;

  // Define minimum and maximum ranking for this page
  if ($offset == "") $offset = 1;
  $min = $offset;
  $max = $offset + $_CONFIG['SCORE_VIEWSIZE'];
  if ($max > score_get_last_rank()) $max = score_get_last_rank();
  
  
  // Print nice title   
  if ($table == "overall") $str = "Overall Ranking"; 
  if ($table == "resource") $str = "Resource Ranking"; 
  if ($table == "strategic") $str = "Strategic Ranking"; 
  if ($table == "exploration") $str = "Exploration Ranking"; 
  print_subtitle ($str);

  
  // Create sub menu 
  create_submenu ( array (
                     "Resource Ranking"    => "score.php?cmd=".encrypt_get_vars("show")."&tbl=".encrypt_get_vars("resource"),
                     "Exploration Ranking" => "score.php?cmd=".encrypt_get_vars("show")."&tbl=".encrypt_get_vars("exploration"),
                     "Strategic Ranking"   => "score.php?cmd=".encrypt_get_vars("show")."&tbl=".encrypt_get_vars("strategic"),
                     "Overall Ranking"     => "score.php?cmd=".encrypt_get_vars("show")."&tbl=".encrypt_get_vars("overall")
                   )
                 );

  $pagecount = round(((score_get_last_rank() - score_get_first_rank()) / $_CONFIG['SCORE_VIEWSIZE']) + 0.5);
  $pages = array ();
  for ($i=1; $i!=$pagecount+1; $i++) {
    $a = array ("P$i" => "score.php?cmd=".encrypt_get_vars("show")."&tbl=".encrypt_get_vars($table)."&ofs=".encrypt_get_vars( ($i-1) * $_CONFIG['SCORE_VIEWSIZE']+1));
    $pages = array_merge ($pages, $a);  	
  }
  create_submenu ($pages);
  
  
  // Print the table
  $cls = "bl";  
  echo "<table border=0 align=center width=75%>\n";
  echo "  <tr class=wb><th colspan=3>".$str."</th><tr>\n"; 
  for ($i=$min; $i!=$max+1; $i++) {
    list ($uid, $name, $points) = score_get_rank ($table, $i);
    if ($uid == $user_id) {
    	echo "<tr class=".$cls."><td>&nbsp;<b><big>".$i.".</big></b>&nbsp;</td><td>&nbsp;<b><big>".$name."</big></b>&nbsp;</td><td>&nbsp;<b><big>(".$points." points)</big></b>&nbsp</td></tr>\n";
    } else {
      echo "<tr class=".$cls."><td>&nbsp;".$i.".&nbsp;</td><td>&nbsp;".$name."&nbsp;</td><td>&nbsp;(".$points." points)&nbsp</td></tr>\n";
    }

    if ($cls == "bl") { $cls = "lbl"; } else { $cls = "bl"; } 
  }
  echo "</table>";
  echo "<br><br>";
  
}

?>




