<?php
  // Include Files
  include "includes.inc.php";
 
  // Session Identification
  session_identification ();

  print_header ();
  print_title ("Buildings & Inventions",
               "All buildings, vessels and inventions currently known in the game are shown on this page. Only the details of the items you already discovered are available.");


  $cmd = input_check ("show", "uid", 0,       // Shows all things discovered
                      "showvid", "vid", 0,    // Shows detailed info of a vessel
                      "showbid", "bid", 0,    // Shows detailed info of a building
                      "showiid", "iid", 0     // Shows detailed info of a invention
                     );

  if ($cmd == "show") {
    if ($uid == "") $uid = user_ourself();
    print_disoveries ($uid);
  }
  if ($cmd == "showbid") {
    building_show_details ($bid, 0, 0, "");
  }
  if ($cmd == "showvid") {
    vessel_show_type_details ($vid, 0, 0, "");
  }
  if ($cmd == "showiid") {
    invention_show_details ($iid, 0, 0, "");
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
function print_disoveries ($user_id) {
  assert (is_numeric ($user_id));

  global $_GALAXY;
  global $_CONFIG;
  global $_RUN;
  
  
  $template = new Smarty ();
  $template->debugging = true;
     

  $user   = user_get_user ($user_id);
  
  // Buildings
  $result = sql_query ("SELECT COUNT(*) AS count FROM s_buildings");
  $count  = sql_fetchrow ($result);

  $building_discovered = 0;
  $building_nr = 0;  
  $result = sql_query ("SELECT * FROM s_buildings");
  while ($building = sql_fetchrow ($result)) {
    if ($building['build_level'] > $user['building_level']) {
    	$building_href[] = "";
    	$building_img[]  = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/general/default.gif";
    	$building_text[] = "Not discovered";    	
    } else {
    	$building_href[] = "inventions.php?cmd=".encrypt_get_vars("showbid")."&bid=".encrypt_get_vars ($building['id']);
    	$building_img[]  = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/buildings/".$building['image'].".jpg";
    	$building_text[] = $building['name'];
    	$building_discovered++;
    }    
    $building_nr++;
  }
  $template->assign ("building_discovery_percentage", round( ($building_discovered / $building_nr * 100), 2));
  $template->assign ("building_href", $building_href);
  $template->assign ("building_img",  $building_img);
  $template->assign ("building_text", $building_text);
  $template->assign ("building_count", count ($building_href));
  


  // Vessels
  $result = sql_query ("SELECT COUNT(*) AS count FROM s_vessels");
  $count  = sql_fetchrow ($result);

  $vessel_discovered = 0;
  $vessel_nr = 0;  
  $result = sql_query ("SELECT * FROM s_vessels");
  while ($vessel = sql_fetchrow ($result)) {
    if ($vessel['build_level'] > $user['building_level']) {
    	$vessel_href[] = "";
    	$vessel_img[]  = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/general/default.gif";
    	$vessel_text[] = "Not discovered";    	
    } else {
    	$vessel_href[] = "inventions.php?cmd=".encrypt_get_vars("showvid")."&vid=".encrypt_get_vars ($vessel['id']);
    	$vessel_img[]  = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/vessels/".$vessel['image'].".jpg";
    	$vessel_text[] = $vessel['name'];
    	$vessel_discovered++;
    }    
    $vessel_nr++;
  }
  $template->assign ("vessel_discovery_percentage", round( ($vessel_discovered / $vessel_nr * 100), 2));
  $template->assign ("vessel_href", $vessel_href);
  $template->assign ("vessel_img",  $vessel_img);
  $template->assign ("vessel_text", $vessel_text);
  $template->assign ("vessel_count", count ($vessel_href));



  // Items
  $result = sql_query ("SELECT COUNT(*) AS count FROM s_inventions");
  $count  = sql_fetchrow ($result);

  $item_discovered = 0;
  $item_nr = 0;  
  $result = sql_query ("SELECT * FROM s_inventions");
  while ($item = sql_fetchrow ($result)) {
    if ($item['build_level'] > $user['building_level']) {
    	$item_href[] = "";
    	$item_img[]  = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/general/default.gif";
    	$item_text[] = "Not discovered";    	
    } else {
    	$item_href[] = "inventions.php?cmd=".encrypt_get_vars("showiid")."&iid=".encrypt_get_vars ($item['id']);
    	$item_img[]  = $_CONFIG['IMAGE_URL'].$_GALAXY['image_dir']."/inventions/".$item['image'].".jpg";
    	$item_text[] = $item['name'];
    	$item_discovered++;
    }    
    $item_nr++;
  }
  $template->assign ("item_discovery_percentage", round( ($item_discovered / $item_nr * 100), 2));
  $template->assign ("item_href", $item_href);
  $template->assign ("item_img",  $item_img);
  $template->assign ("item_text", $item_text);
  $template->assign ("item_count", count ($item_href));
  
  $template->display ($_RUN['theme_path']."/inventions.tpl");  
}

?>