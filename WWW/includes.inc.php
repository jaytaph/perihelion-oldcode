<?php
  include_once "constants.inc.php";        // Standard type defines
  include_once "config.inc.php";           // Configuration routines
  include_once "sql.inc.php";              // MySQL functions
  include_once "comm.inc.php";             // Communications functions
  include_once "general_print.inc.php";    // General functions
  include_once "general_encrypt.inc.php";  // General functions
  include_once "general_calc.inc.php";     // General functions
  include_once "general_misc.inc.php";     // General functions
  
  include_once "global_alliance.php";      // Global "objects"
  include_once "global_anomaly.php";
  include_once "global_blackhole.php";
  include_once "global_building.php";
  include_once "global_item.php";
  include_once "global_help.php";
  include_once "global_nebula.php";
  include_once "global_ore.php";
  include_once "global_planet.php";
  include_once "global_scan.php";
  include_once "global_sector.php";
  include_once "global_score.php";
  include_once "global_starbase.php";
  include_once "global_user.php";
  include_once "global_vessel.php";
  include_once "global_wormhole.php";

  include_once "startup.inc.php";				// Things that has to be done at every run...
  
  include_once "smarty/Smarty.class.php";
  include_once "smarty/SmartyValidate.class.php";

?>
