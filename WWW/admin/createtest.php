<?php

  // Include Files
  include "../includes.inc.php";

  // Session Identification
  session_identification ("admin");

  print_header ();
  print_title ("Create Test users for scoring purposes...", 
               "Make sure you have set the correct tables_priv rows for u_users and u_score in the database!");

  $result = sql_query ("SELECT * FROM perihelion.u_users WHERE id >= 1000");
  if (sql_countrows ($result) != 0) {
    print_line ("User Id 1000 or above already exists!");
    print_footer ();
    exit;
  }


  // Create 1000 users....
  for ($i=1000; $i!=2000; $i++) {
    sql_query ("INSERT INTO perihelion.u_users (id, login_name, name) VALUES ($i, 'plyr$i', 'Player $i')");
    sql_query ("INSERT INTO perihelion.u_score (user_id, overall, resource, strategic, exploration) VALUES ($i, RAND()*1000, RAND()*1000, RAND()*1000, RAND()*1000)");
    echo "Done with player $i<br>\n";
  }

  print_footer ();
  exit;
?>
