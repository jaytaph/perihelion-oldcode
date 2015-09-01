<?php
  // Include Files
  include "includes.inc.php";

  session_identification ();

  if (isset ($_GET['user_id'])) {
    $user_id = decrypt_get_vars ($_GET['id']);
  } else {
    $user_id = user_ourself();
  }

  if (isset ($_GET['zoom'])) {
    $zoom = decrypt_get_vars ($_GET['zoom']);
  } else {
    $zoom = 0;
  }

  if (isset ($_GET['f'])) {
    $flags = decrypt_get_vars ($_GET['f']);
  } else {
    $flags = -1;
  }



  $name_array = array("alpha", "beta", "gamma", "delta");

  if (substr ($_GALAXY['image'], -4) == ".jpg") {
    $img2   = imagecreatefromjpeg ($_CONFIG['PATH'].$_GALAXY['image_dir']."/galaxy/".$_GALAXY['image']);
    $width_scale  = imagesx ($img2) / 400;
    $height_scale = imagesy ($img2) / 400;
  } else {
    $img2   = imagecreatefromgif ($_CONFIG['PATH'].$_GALAXY['image_dir']."/galaxy/".$_GALAXY['image']);
    $width_scale  = imagesx ($img2) / 400;
    $height_scale = imagesy ($img2) / 400;
  }

  $img    = imagecreate (400, 400);
  $white  = imagecolorallocate ($img, 255, 255, 255);
  $yellow = imagecolorallocate ($img, 255, 255,   0);
  $blue   = imagecolorallocate ($img,   0, 255, 255);
  $black  = imagecolorallocate ($img,   0,   0,   0);
  $red    = imagecolorallocate ($img, 255,   0,   0);
  $green  = imagecolorallocate ($img,   0, 255,   0);
  $purple = imagecolorallocate ($img, 255,   0, 255);



  $ox = array (0,1,0,1,2,3,2,3,0,1,0,1,2,3,2,3);
  $oy = array (0,0,1,1,0,0,1,1,2,2,3,3,2,2,3,3);
  $factors = array ();

  $size = 400;
  $startX = 0;
  $startY = 0;

  // No zoom
  if ($zoom >= 1) {
    $tmp = $zoom;
    while ($tmp > 16){
      $tmp_id = $tmp % 16;
      if ($tmp_id == 0) $tmp_id = 16;
      array_push ($factors, $tmp_id-1);

      $tmp--;
      $tmp = intval ($tmp / 16);
    };

    $tmp_id = $tmp % 16;
    if ($tmp_id == 0) $tmp_id = 16;
    array_push ($factors, $tmp_id-1);
  }

  $factors = array_reverse ($factors);
  foreach ($factors as $factor) {
    $size = $size / 4;
    $startX += $ox[$factor] * $size;
    $startY += $oy[$factor] * $size;
  }

  // Zoom image to destination
  imagecopyresized ($img, $img2, 0, 0, $startX*$width_scale, $startY*$height_scale, 400, 400, $size*$width_scale, $size*$height_scale);

  // And print a nice stringy at the top left
  $str = "Grid ".$zoom;
  $font = 2;
  imagestring ($img, $font, 6, 3, $str, $black);
  imagestring ($img, $font, 5, 2, $str, $white);

  if ($user_id != 0 and flags_is_checked ($flags, FLAGS_SECTORS)) {

    $result = sql_query ("SELECT  s.* FROM s_sectors AS s, g_sectors AS g WHERE FIND_IN_SET( s.id, g.csl_sector_id ) and g.user_id=".$user_id);
    while ($sector = sql_fetchrow ($result)) {
      $color = $yellow;

      if (user_is_mutual_friend ($user_id, $sector['user_id']) and $sector['user_id'] != $user_id) {
        if (! flags_is_checked ($flags, FLAGS_SECTOR_FRIEND)) continue;
        $color = $green;
      }
      if (user_is_enemy ($user_id, $sector['user_id'])) {
        if (! flags_is_checked ($flags, FLAGS_SECTOR_ENEMY)) continue;
        $color = $red;
      }

      if (user_is_neutral ($user_id, $sector['user_id'])  or $sector['user_id'] == UID_NOBODY) {
        if (! flags_is_checked ($flags, FLAGS_SECTOR_NEUTRAL)) continue;
        $color = $yellow;
      }

      if (user_is_in_aliance ($user_id, $sector['user_id'])) {
        if (! flags_is_checked ($flags, FLAGS_SECTOR_ALLIANCE)) continue;
        $color = $purple;
      }

      if ($sector['user_id'] == $user_id) {
        $color = $white;
      }

      if (! flags_is_checked ($flags, FLAGS_SECTOR_NAMES)) $sector['name'] = "";
      graphics_print_sector ($img, $startX, $startY, $size, $sector['distance'], $sector['angle'], $sector['name'], $color);
    }



    if (flags_is_checked ($flags, FLAGS_VESSELS)) {
      $result = sql_query ("SELECT * FROM g_vessels WHERE user_id=".$user_id." AND status != 'ORBIT'");
      while ($vessel = sql_fetchrow ($result)) {
        graphics_print_vessel ($img, $startX, $startY, $size, $vessel['distance'], $vessel['angle'], $vessel['name']);
      }
    }

    if (flags_is_checked ($flags, FLAGS_PRESETS)) {
      $result = sql_query ("SELECT * FROM g_presets WHERE user_id=".$user_id);
      while ($preset = sql_fetchrow ($result)) {
        graphics_print_preset ($img, $startX, $startY, $size, $preset['distance'], $preset['angle'], $preset['name']);
      }
    }

    if (flags_is_checked ($flags, FLAGS_WORMHOLES)) {
      $result = sql_query ("SELECT * FROM s_anomalies WHERE type='".ANOMALY_WORMHOLE."'");
      while ($wormhole = sql_fetchrow ($result)) {
        if (anomaly_is_discovered_by_user ($wormhole['id'], $user_id)) {
          graphics_print_wormhole ($img, $startX, $startY, $size, $wormhole);
        }
      }
    }

  }

  graphics_print_scale ($img, $size);


  header ("Content-type: image/JPEG");
  imageJPEG ($img);
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
function graphics_print_scale ($img, $size) {
  global $_GALAXY;
  global $white;
  global $black;

  $font = 1;

  imageline ($img, 10, 385, 60, 385, $white);
  imageline ($img, 10, 382, 10, 388, $white);
  imageline ($img, 60, 382, 60, 388, $white);

  $scale = intval ( $_GALAXY['galaxy_size'] * 2 / 400 * $size / 8 ) . " ly";

  $fx = (imagefontwidth ($font) * strlen ($scale));
  $fx = 25 - ($fx / 2);
  imagestring ($img, $font, 14+$fx, 387, $scale, $black);
  imagestring ($img, $font, 13+$fx, 386, $scale, $white);
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
function graphics_print_wormhole ($img, $startX, $startY, $size, $wormhole) {
  global $red;

  $font = 2;
  if ($size == 400) $font = 1;

  $result = sql_query ("SELECT * FROM w_wormhole WHERE id=".$wormhole['id']);
  $worminfo = sql_fetchrow ($result);

  $sector = sector_get_sector ($wormhole['sector_id']);

  list($x1, $y1) = graphics_calc_xy ($startX, $startY, $size, $sector['distance'], $sector['angle'], "YES");
  list($x2, $y2) = graphics_calc_xy ($startX, $startY, $size, $worminfo['distance'], $worminfo['angle'], "YES");

  imageline ($img, $x1, $y1, $x2, $y2, $red);
  graphics_set_point ($img, $font, $x2, $y2, $wormhole['name'], $red);
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
function graphics_print_sector ($img, $startX, $startY, $size, $distance, $angle, $name, $color) {
  $font = 2;
  if ($size == 400) $font = 1;

  list($x, $y) = graphics_calc_xy ($startX, $startY, $size, $distance, $angle);
  graphics_set_point ($img, $font, $x, $y, $name, $color);
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
function graphics_print_vessel ($img, $startX, $startY, $size, $distance, $angle, $name) {
  global $blue;
  global $red;

  $font = 2;
  if ($size == 400) $font = 1;

  list($x, $y) = graphics_calc_xy ($startX, $startY, $size, $distance, $angle);
  graphics_set_vessel_point ($img, $font, $x, $y, $name, $blue);
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
function graphics_print_preset ($img, $startX, $startY, $size, $distance, $angle, $name) {
  global $purple;
  global $white;

  $font = 2;
  if ($size == 400) $font = 1;

  list($x, $y) = graphics_calc_xy ($startX, $startY, $size, $distance, $angle);
  graphics_set_point ($img, $font, $x, $y, $name, $purple, "N");
//  graphics_set_point ($img, $font, $x, $y, $name, $white);
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
function graphics_calc_xy ($startX, $startY, $size, $distance, $angle, $offscale = "NO") {
  global $_GALAXY;

  // Get the X and Y value
  $x = ($distance / ($_GALAXY['galaxy_size'] / 190)) * cos (deg2rad ($angle/1000 - 90));
  $y = ($distance / ($_GALAXY['galaxy_size'] / 190))* sin (deg2rad ($angle/1000 - 90));

  // Translate it into the middle of the picture
  $x = intval ($x + 200);
  $y = intval ($y + 200);

  if ($offscale == "NO") {
    if ($x < $startX or $x > $startX+$size or $y < $startY or $y > $startY+$size) {
      return array (0, 0);
    }
  }

  // If we have to print it, find the correct X and Y values for that point in our box
  $zoomfactor = intval (400 / $size);
  $x -= $startX;
  $y -= $startY;
  $x *= $zoomfactor;
  $y *= $zoomfactor;
  return array ($x, $y);

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
function graphics_set_point ($img, $font, $x, $y, $name, $col, $shadow="Y") {
  global $black;
  global $blue;

  if ($x == 0 and $y == 0) return;

  if ($shadow == "Y") {
    $points[0] = $x-1-1; $points[1] = $y-1-1;
    $points[2] = $x-1-1; $points[3] = $y+1+1;
    $points[4] = $x+1+1; $points[5] = $y+1+1;
    $points[6] = $x+1+1; $points[7] = $y-1-1;
    imagefilledpolygon ($img, $points, 4, $black);
  }

  $points[0] = $x-1; $points[1] = $y-1;
  $points[2] = $x-1; $points[3] = $y+1;
  $points[4] = $x+1; $points[5] = $y+1;
  $points[6] = $x+1; $points[7] = $y-1;
  imagefilledpolygon ($img, $points, 4, $col);

  $fw = imagefontwidth ($font);
  $fh = imagefontheight ($font);
  $w = $fw * strlen ($name);

  if ($shadow == "Y") imagestring ($img, $font, $x+6, $y+6, $name, $black);
  imagestring ($img, $font, $x+5, $y+5, $name, $col);
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
function graphics_set_vessel_point ($img, $font, $x, $y, $name, $col) {
  global $black;
  global $blue;

  if ($x == 0 and $y == 0) return;

  $points[0] = $x-1; $points[1] = $y-1;
  $points[2] = $x-1; $points[3] = $y+0;
  $points[4] = $x+0; $points[5] = $y+0;
  $points[6] = $x+0; $points[7] = $y-1;
  imagefilledpolygon ($img, $points, 4, $col);

  $fw = imagefontwidth ($font);
  $fh = imagefontheight ($font);
  $w = $fw * strlen ($name);

  imagestring ($img, $font, $x+6, $y-6, $name, $black);
  imagestring ($img, $font, $x+5, $y-5, $name, $col);

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
function flags_is_checked ($flags, $bit) {
  return $flags & pow (2, $bit);
}

?>
