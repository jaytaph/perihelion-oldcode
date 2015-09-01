<?php

  // Defines for ores
  $_CONFIG['ore_names'] = array ("Xellium", "Vitrea", "Entrium", "Augon", "Marium", "Haligon");

  define ("ORE_ALL",        "-2");
  define ("ORE_NONE",       "-1");
  define ("ORE_XELLIUM",    "0");
  define ("ORE_VITRA",      "1");
  define ("ORE_ENTRIUM",    "2");
  define ("ORE_AUGON",      "3");
  define ("ORE_MARIUM",     "4");
  define ("ORE_HALIGON",    "5");

  // Defines for the SQL
  define ("NO_NULL_ALLOWED",     "1");
  define ("NULL_OR_ONE_ALLOWED", "2");
  define ("JUST_ONE_ALLOWED",    "3");
  define ("MULTIPLE_ALLOWED",    "4");

  // Defines for showing user information
  define ("USER_SHOWINFO_NORMAL",     "0");
  define ("USER_SHOWINFO_EXTENDED",   "1");

  define ("VESSEL_GETSTATUS_NO_HYPERLINKS",   "0");
  define ("VESSEL_GETSTATUS_SHOW_HYPERLINKS", "1");


  // Standard defines, make sure these are synced with the onces in the database!!!!
  define ("VESSEL_TYPE_EXPLORE", "E");
  define ("VESSEL_TYPE_TRADE",   "T");
  define ("VESSEL_TYPE_BATTLE",  "B");


  // Message types
  define ("MSG_TYPE_GLOBAL",      "G");
  define ("MSG_TYPE_USER",        "U");
  define ("MSG_TYPE_EXPLORATION", "E");
  define ("MSG_TYPE_INVENTION",   "I");
  define ("MSG_TYPE_PLANET",      "P");
  define ("MSG_TYPE_VESSEL",      "V");

  // Planet type defines
  define ("ANOMALY_BLACKHOLE", "B");
  define ("ANOMALY_WORMHOLE",  "W");
  define ("ANOMALY_PLANET",    "P");
  define ("ANOMALY_NEBULA",    "N");
  define ("ANOMALY_STARBASE",  "S");

  // User defines
  define ("UID_NOBODY", "0");

  // Caching function
  define ("NOCACHE",  "0");       // Do not use data in the caching
  define ("USECACHE", "1");       // Use data if present in the caching


  // Building Defines
  define ("BUILDING_HEADQUARTER_INACTIVE",   "-1");
  define ("BUILDING_HEADQUARTER",            "1");
  define ("BUILDING_VESSEL_STATION",         "9");
  define ("BUILDING_SPACEDOCK",              "11");

  // Defines for global_vessel.php/show_all_vessels
  define ("SHOW_TRADEROUTES",    "true");
  define ("NO_SHOW_TRADEROUTES", "false");

  // Defines for the queue
  define ("QUEUE_BUILD",     "B");
  define ("QUEUE_INVENTION", "I");
  define ("QUEUE_VESSEL",    "V");
  define ("QUEUE_FLIGHT",    "F");
  define ("QUEUE_UPGRADE",   "U");

  // Defines for relations
  define ("RELATION_FRIEND",  "1");
  define ("RELATION_NEUTRAL", "2");
  define ("RELATION_ENEMY",   "3");

  // Defines for item types
  define ("ITEM_TYPE_VESSEL", "V");
  define ("ITEM_TYPE_WEAPON", "W");
  define ("ITEM_TYPE_PLANET", "P");


  // Galaxy graphic flag settings
  define ("FLAGS_SECTORS",         "1");
  define ("FLAGS_SECTOR_NAMES",    "2");
  define ("FLAGS_SECTOR_FRIEND",   "3");
  define ("FLAGS_SECTOR_NEUTRAL",  "4");
  define ("FLAGS_SECTOR_ENEMY",    "5");
  define ("FLAGS_SECTOR_ALLIANCE", "6");
  define ("FLAGS_ANOMALIES",       "7");
  define ("FLAGS_WORMHOLES",       "8");
  define ("FLAGS_PRESETS",         "9");
  define ("FLAGS_VESSELS",         "10");
  define ("FLAGS_SCANNED",         "11");


  // User flags
  define ("USERFLAG_ADMIN",     "admin");     // Can use admin thingies
  define ("USERFLAG_INVISIBLE", "invisible"); // Is not viewable in who's online

?>