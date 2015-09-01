<?php
    // Include Files
    include "includes.inc.php";

    // Session Identification
    session_identification ();

    print_header ();
    print_title ("Galaxy overview",
                 "This map gives an overview of the galaxy. You can zoom into a specific region by clicking on the map. You can also type in the grid id from the particulair place you want to see or switch off some map items for easy viewing.");

    if (isset ($_POST['zoom'])) {
      $zoom = $_POST['zoom'];
      if (isset ($_POST['f'])) {
        $f = $_POST['f'];
      } else {
        $f = -1;
      }
    } else {
      // Get the zoom
      if (! isset ($_GET['zoom'])) {
        $zoom = 0;
      } else {
        $zoom = decrypt_get_vars ($_GET['zoom']);
      }
      // Get the flags
      if (! isset ($_GET['f'])) {
        $f = -1;
      } else {
        $f = decrypt_get_vars ($_GET['f']);
      }
    }


    // Create flags value from the F array;
    $flags = -1;
    if ($f != -1) {
      $flags = 0;
      if (is_array ($f)) {
        foreach ($f as $idx => $key) {
          $flags += pow (2, $idx);
        }
      } else {
        $flags = $f;
      }
    }


      form_start ();
      echo "  <br>\n";
      echo "  <table align=center>\n";
      echo "  <tr><td valign=top>\n";
      echo "    <br>\n";
      echo "    <center><b>Data Console</b></center>\n";
      echo "    <br>\n";
      echo "    Grid: <input type=text size=10 value=".$zoom." name=zoom>&nbsp;<input type=submit name=submit value=Go><br>\n";
      echo "    <br>\n";
      echo "    <input type=hidden   name=f[0] value=On>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SECTORS."] ".         is_checked ($flags, FLAGS_SECTORS).         ">Show Sectors<br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SECTOR_NAMES."] ".    is_checked ($flags, FLAGS_SECTOR_NAMES).    ">Show Sector Names<br>\n";
      echo "    <br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SECTOR_FRIEND."] ".   is_checked ($flags, FLAGS_SECTOR_FRIEND).   ">Show Friendly Sectors<br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SECTOR_NEUTRAL."] ".  is_checked ($flags, FLAGS_SECTOR_NEUTRAL).  ">Show Neutral Sectors<br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SECTOR_ENEMY."] ".    is_checked ($flags, FLAGS_SECTOR_ENEMY).    ">Show Enemy Sectors<br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SECTOR_ALLIANCE."] ". is_checked ($flags, FLAGS_SECTOR_ALLIANCE). ">Show Alliance Sectors<br>\n";
      echo "    <br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_WORMHOLES."] ".    is_checked ($flags, FLAGS_WORMHOLES).     ">Show Wormholes<br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_PRESETS."] ".      is_checked ($flags, FLAGS_PRESETS).       ">Show Presets<br>\n";
      echo "    <br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_VESSELS."] ".      is_checked ($flags, FLAGS_VESSELS).       ">Show Vessels<br>\n";
      echo "    <input type=checkbox name=f[".FLAGS_SCANNED."] ".      is_checked ($flags, FLAGS_SCANNED).       ">Show Scanned Vessels<br>\n";

      echo "    <br>\n";
      echo "    <br>\n";
      if ($zoom >= 1) {
        echo "[ <a href=main.php?id=".encrypt_get_vars(user_ourself())."&zoom=".encrypt_get_vars( intval($zoom / 16))."&f=".encrypt_get_vars ($flags).">Zoom one level out</a> ]\n";
      }

      echo "  </td><td valign=top>\n";
      echo "    <img alt='Galaxy' usemap='#galaxy' border=1 src=graphics.php?id=".encrypt_get_vars (user_ourself())."&zoom=".encrypt_get_vars ($zoom)."&f=".encrypt_get_vars ($flags).">\n";
      echo "  </td></tr>\n";
      echo "  </table>\n";
      form_end ();

      if ($zoom < pow (16, 3)) {
        echo "  <map name='galaxy'>\n";
        $name = array ("alpha", "beta", "gamma", "delta");

        $tmp = ($zoom * 16);

        for ($dx=0; $dx!=2; $dx++) {
          for ($dy=0; $dy!=2; $dy++) {
            for ($ry=0; $ry!=2; $ry++) {
              for ($rx=0; $rx!=2; $rx++) {
                $zoomid = $tmp + ($dy*8 + $dx*4 + $ry*2 + $rx + 1);

                $dname = $name[$dy*2+$dx];
                $rname = $name[$ry*2+$rx];

                $x1 = ($dx * 200) + ($rx * 100);
                $y1 = ($dy * 200) + ($ry * 100);
                $x2 = $x1 + (400 / 4);
                $y2 = $y1 + (400 / 4);
                echo "    <area href='main.php?id=".encrypt_get_vars(user_ourself())."&zoom=".encrypt_get_vars($zoomid)."&f=".encrypt_get_vars ($flags)."' alt='$dname / $rname' shape=rect coords='$x1,$y1,$x2,$y2'>\n";
              }
            }
          }
        }
        echo "  </map>\n";
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
// returns "checked" if $bit is set in $flags
function is_checked ($flags, $bit) {
  return $flags & pow(2, $bit) ? "checked" : "";
}

?>
