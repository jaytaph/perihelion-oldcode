<?php

  // ========================================================================================
  // Get the global configuration. At this point we
  // still haven't started our session, and thus our userinfo[] hash is not
  // known. This query doesn't need the userinfo[] (it uses perihelion db) but
  // the queries after it does...
  $result  = sql_query ("SELECT * FROM perihelion.c_config LIMIT 1");
  $sql_config  = sql_fetchrow ($result);
  $_CONFIG = array_merge ($_CONFIG, $sql_config);
  
?>