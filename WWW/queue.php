<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Queue information",
                 "In this screen you can view all orders currently being progressed. All orders are grouped by buildings, products, vessels and flights for easy viewing.");

    $cmd = input_check ("show", "uid", 0);

    if ($cmd == "show") {    	
      if ($uid == "") $uid = user_ourself();
      queue_print ($uid);
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
function queue_print ($user_id) {
	assert (is_numeric ($user_id));
	
	global $_RUN;

  $totalcount = 0;	
	
	$template = new Smarty ();
	$template->debugging = true;
	
	// Buildings	
	$count = 0;
	$tmpvar = array();  
  $result = sql_query ("SELECT * FROM h_queue WHERE type='".QUEUE_BUILD."' AND user_id=".$user_id);
  while ($queue = sql_fetchrow ($result)) {
  	$totalcount++;
  	$count++;

    $building = building_get_building ($queue['building_id']);
    $planet   = anomaly_get_anomaly ($queue['planet_id']);
    
  	$tmpvar['what'][] = "Building ".$building['name']." on ".$planet['name'];
    $tmpvar['ticks'][] = $queue['ticks'];
  } 
	$tmpvar['count'] = $count;
	$template->assign ("building", $tmpvar);


	// Items
	$count = 0;
	$tmpvar = array();  
  $result = sql_query ("SELECT * FROM h_queue WHERE type='".QUEUE_INVENTION."' AND user_id=".$user_id);
  while ($queue = sql_fetchrow ($result)) {
  	$totalcount++;
  	$count++;

    $item   = item_get_item ($queue['building_id']);
    $planet = anomaly_get_anomaly ($queue['planet_id']);
       
  	$tmpvar['what'][] = "Manufacturing ".$item['name']." on ".$planet['name'];
    $tmpvar['ticks'][] = $queue['ticks'];
  } 
	$tmpvar['count'] = $count;
	$template->assign ("item", $tmpvar);


	// Vessels
	$count = 0;
	$tmpvar = array();  
  $result = sql_query ("SELECT * FROM h_queue WHERE type='".QUEUE_VESSEL."' AND user_id=".$user_id);
  while ($queue = sql_fetchrow ($result)) {
  	$totalcount++;
  	$count++;

    $vessel = vessel_get_vessel ($queue['vessel_id']);
    $planet = anomaly_get_anomaly ($queue['planet_id']);
       
  	$tmpvar['what'][] = "Building ".$vessel['name']." on ".$planet['name'];
    $tmpvar['ticks'][] = $queue['ticks'];
  } 
	$tmpvar['count'] = $count;
	$template->assign ("vessel", $tmpvar);
	
	
	// Upgrade (NOTE: THESE ARE ALSO PART OF THE VESSEL[] ARRAY
	$count = 0;
	$tmpvar = array();  
  $result = sql_query ("SELECT * FROM h_queue WHERE type='".QUEUE_UPGRADE."' AND user_id=".$user_id);
  while ($queue = sql_fetchrow ($result)) {
  	$totalcount++;
  	$count++;

    $vessel = vessel_get_vessel ($queue['vessel_id']);
    $planet = anomaly_get_anomaly ($queue['planet_id']);
       
  	$tmpvar['what'][] = "Upgrading ".$vessel['name']." on ".$planet['name'];
    $tmpvar['ticks'][] = $queue['ticks'];
  } 
	$tmpvar['count'] = $count;
	$template->assign ("vessel", $tmpvar);


	// Flights
	$count = 0;
	$tmpvar = array();  
  $result = sql_query ("SELECT * FROM h_queue WHERE type='".QUEUE_FLIGHT."' AND user_id=".$user_id);
  while ($queue = sql_fetchrow ($result)) {
  	$totalcount++;
  	$count++;

    $result2 = sql_query ("SELECT * FROM g_vessels WHERE id=".$queue['vessel_id']);
    $vessel = sql_fetchrow ($result2);
    if ($vessel['dst_planet_id'] == 0) {
      if ($vessel['dst_sector_id'] == 0) {
        $tmpvar['what'][] = "Flying ".$vessel['name']." to outer space.";
      } else {
        $sector = sector_get_sector ($vessel['dst_sector_id']);
        $tmpvar['what'][] = "Flying ".$vessel['name']." to sector ".$sector['name'];
      }
    } else {
      $planet = anomaly_get_anomaly ($vessel['dst_planet_id']);
      $tmpvar['what'][] = "Flying ".$vessel['name']." to ".$planet['name'];
    }        	
    $tmpvar['ticks'][] = $queue['ticks'];
  } 
	$tmpvar['count'] = $count;
	$template->assign ("flight", $tmpvar);


	$template->assign ("itemcount", $totalcount);	
	$template->display ($_RUN['theme_path']."/order-queue.tpl");
}



?>
