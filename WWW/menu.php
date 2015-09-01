<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification();

  $i = $j = 0;

  // Declare menu items
  $item[$i][0] = "Main";
  $item[$i][++$j]['item'] = "Information";      $item[$i][$j]['href'] = "info.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "View Messages";    $item[$i][$j]['href'] = "message.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "View Orders";      $item[$i][$j]['href'] = "queue.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "Science Office";   $item[$i][$j]['href'] = "science.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "Show Inventions";  $item[$i][$j]['href'] = "inventions.php?cmd=".encrypt_get_vars ("show");
  $i++; $j=0;

  $item[$i][0] = "Starmaps";
  $item[$i][++$j]['item'] = "View Galaxy Map";  $item[$i][$j]['href'] = "main.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "View Sector Map";  $item[$i][$j]['href'] = "sector.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "View Planet Map";  $item[$i][$j]['href'] = "conview.php?cmd=".encrypt_get_vars ("show");
  $i++; $j=0;

  $item[$i][0] = 'Stock';
  $item[$i][++$j]['item'] = "Stock & Upkeep";   $item[$i][$j]['href'] = "stock.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "Mining";           $item[$i][$j]['href'] = "mining.php?cmd=".encrypt_get_vars ("show");
  $i++; $j=0;

  $item[$i][0] = "Vessels";
  $item[$i][++$j]['item'] = "View Vessels";     $item[$i][$j]['href'] = "vessel.php?cmd=".encrypt_get_vars ("showuid");
  $item[$i][++$j]['item'] = "Move Vessels";     $item[$i][$j]['href'] = "vesselmove.php?cmd=".encrypt_get_vars ("showuid");
  $item[$i][++$j]['item'] = "Create Vessels";   $item[$i][$j]['href'] = "vesselcreate.php?cmd=".encrypt_get_vars ("showaid");
  $item[$i][++$j]['item'] = "Flight Presets";   $item[$i][$j]['href'] = "vesselpreset.php?cmd=".encrypt_get_vars ("show");
  $item[$i][++$j]['item'] = "Trade Routes";     $item[$i][$j]['href'] = "trade.php?cmd=".encrypt_get_vars("show");

  //$item[$i][++$j]['item'] = "Upgrade Vessels"; $item[$i][$j]['href'] = "vesselupgrade.php";
  //$item[$i][++$j]['item'] = "View Convoys";    $item[$i][$j]['href'] = "convoy.php?cmd=".encrypt_get_vars("view");
  //$item[$i][++$j]['item'] = "Move Convoy";     $item[$i][$j]['href'] = "convoy.php?cmd=".encrypt_get_vars("move");
  //$item[$i][++$j]['item'] = "Create Convoy";   $item[$i][$j]['href'] = "convoy.php?cmd=".encrypt_get_vars("create");
  $i++; $j=0;

  //$item[$i][0] = "Trade Routes";
  //$item[$i][++$j]['item'] = "View Routes";       $item[$i][$j]['href'] = "trade.php?cmd=".encrypt_get_vars("show");
  //$item[$i][++$j]['item'] = "Create Route";      $item[$i][$j]['href'] = "trade.php?cmd=".encrypt_get_vars("create");
  //$item[$i][++$j]['item'] = "Delete Route";      $item[$i][$j]['href'] = "trade.php?cmd=".encrypt_get_vars("delete");
  //$i++; $j=0;

  $item[$i][0] = "General";
  $item[$i][++$j]['item'] = "User Statistics";    $item[$i][$j]['href'] = "stats.php?cmd=".encrypt_get_vars("show");
  $item[$i][++$j]['item'] = "Show Rankings";      $item[$i][$j]['href'] = "score.php?cmd=".encrypt_get_vars("show")."&tbl=".encrypt_get_vars("overall");
  $item[$i][++$j]['item'] = "Show Users";         $item[$i][$j]['href'] = "user.php?cmd=".encrypt_get_vars("show");
  $item[$i][++$j]['item'] = "Show Alliances";     $item[$i][$j]['href'] = "alliance.php?cmd=".encrypt_get_vars("show");
  $item[$i][++$j]['item'] = "View Preferences";   $item[$i][$j]['href'] = "prefs.php?cmd=".encrypt_get_vars("show");
  $item[$i][++$j]['item'] = "View Manual";        $item[$i][$j]['href'] = "help/index.html";
  $item[$i][++$j]['item'] = "Who's Online?";      $item[$i][$j]['href'] = "whoisonline.php";
  if (user_is_admin (user_ourself())) {
    $item[$i][++$j]['item'] = "Admin Page";       $item[$i][$j]['href'] = "admin/admin.php?cmd=".encrypt_get_vars("choose");
  }
  $item[$i][++$j]['item'] = "Logout";             $item[$i][$j]['href'] = "logout.php\" target=\"_parent";
  $i++; $j=0;




  // Generate menu inside a string
  $menu = "";
  for ($i=0; $i!=count ($item)+1; $i++) {
    if (empty ($item[$i][0])) continue;

    $menu .= "<nobr><b>".$item[$i][0]."</b></nobr><br>\n";

    for ($j=1; $j!=count ($item[$i]); ++$j) {
      if (!empty ($item[$i][$j]['item'])) {
        $menu .= "<nobr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><a href=\"".$item[$i][$j]['href']."\">".$item[$i][$j]['item']."</a></b></nobr><br>\n";
      }
    }
    $menu .= "<br>\n";
  }

  $template = new Smarty ();
  $template->assign ("menu", $menu);
  $template->assign ("css_path", $_RUN['theme_url']."/perihelion.css");
	$template->assign ("background", "background='".$_CONFIG['IMAGE_URL']."/images/backgrounds/back2.jpg' bgproperties=fixed");
  $template->display ($_RUN['theme_path']."/menu.tpl");
?>