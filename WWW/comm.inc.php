<?php
 $COMM_SOCK = 0;          // Global socket descriptor

/******************************************************************************
 * Establish a connection to the server and passes
 * through the identification process
 */
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
function comm_init_server () {
    global $COMM_SOCK;
    global $_USER;
    global $_CONFIG;

    // Create socket
    $COMM_SOCK = socket_create (AF_INET, SOCK_STREAM, 0);
    if (! $COMM_SOCK) perihelion_die ("Communication Error", "socket(): ".strerror ($COMM_SOCK));

    // Connect socket
    @socket_connect ($COMM_SOCK, $_CONFIG['COMM_HOST'], $_CONFIG['COMM_PORT']) or perihelion_die ("Communcation Error", "Connect(): ". socket_strerror (socket_last_error($COMM_SOCK)) .  ". Please try again...");

    // Send ID package and exit when no correct ID was given...
    $data['user']=$_CONFIG['COMM_USER'];
    $data['pass']=$_CONFIG['COMM_PASS'];

    if ($_USER == "") {
      $data['uid']=0;
      $data['cdb']=$_CONFIG['default_db'];
    } else {
      $data['uid']=$_USER['id'];
      $data['cdb']=$_USER['galaxy_db'];
    }

    comm_s2s ("ID", $data);
    $pkg = comm_recv_from_server ();
    if ($pkg['status'] != "STATUS_OK") {
        comm_fini_server ();
        perihelion_die ("Communcation Error", "Error while authorising to the server...");
    }
}

/*****************************************************************************
 * Closes the server by sending a END-OF-SESSION package
 */
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
function comm_fini_server () {
    global $COMM_SOCK;

    if ($COMM_SOCK == 0) perihelion_die ("Communication Error", "comm_s2s: socket not inialized!");
    comm_s2s ("EOS");
    $pkg = comm_recv_from_server ();
    socket_close ($COMM_SOCK);
    $COMM_SOCK = 0;
}

/****************************************************************************
 * Sends data to the server
 */
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
function comm_s2s ($cmd, $data="") {
    global $COMM_SOCK;

    assert (isset ($cmd));
    assert (isset ($data));

    if ($COMM_SOCK == 0) perihelion_die ("Communication Error", "comm_s2s: socket not inialized!");

    // Create Package
    $pkg = array ();
    $pkg ['pkg_cmd'] = $cmd;
    if (isset($data) && $data != "") $pkg = array_merge ($pkg, $data);

    // Sends the whole $pkg-array
    while (list ($key, $val) = each ($pkg)) {
        $val = "$val";
        if ($key == "") continue;
        socket_write ($COMM_SOCK, &$key) or perihelion_die ("Communication Error", "Server sending error occured.");
        socket_write ($COMM_SOCK, "==>") or perihelion_die ("Communication Error", "Server sending error occured.");

        // Final checkup before we send to the server.. There is no possible way a < or > tag could slip through,
        // but if it did, we catch it here... again...
        if ($val == "") $val = " ";
        $val = str_replace ("\'", "'", $val);
        $val = str_replace ("<", "&lt;", $val);
        $val = str_replace (">", "&gt;", $val);

        socket_write ($COMM_SOCK, &$val) or perihelion_die ("Communication Error", "Server sending error occured.");
        socket_write ($COMM_SOCK, "\r\n") or perihelion_die ("Communication Error", "Server sending error occured.");
    }
    // And close with a dot.
    socket_write ($COMM_SOCK, ".\r\n") or perihelion_die ("Communication Error", "Server sending error occured.");

}

/******************************************************************************
 * Receives data from the server.
 */
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
function comm_recv_from_server () {
  global $COMM_SOCK;

  if ($COMM_SOCK == 0) perihelion_die ("Communication Error", "recv_from_server: socket not inialized!");

  // Read until we find a dot.
  $data = array ();
  do {
    // Read a whole line
    $buf = "";
    $done = false;
    do {
      $char = socket_read ($COMM_SOCK, 1, PHP_BINARY_READ);
      if ($char === false) {
        print_line ("Error while reading from socket: ".socket_strerror(socket_last_error()));
//        exit;
      }
      $buf = $buf . $char;
      if (ord($char) == 13) { $done = true; }
    } while (! $done);

    // Neatify the buffer. Make sure all \r and \n's are gone...
    $buf = trim ($buf);

    // And exit when we find a dot
    if ($buf == ".") continue;


    // Split line
    if (substr ('==>', $buf)) {
      list ($buf_name, $buf_data) = split ('==>', $buf, 2);
      $data [$buf_name] = $buf_data;
    }


  } while ($buf != ".");

  // Return array
  return $data;
}



/******************************************************************************
 * Returns OK on ok
 *         ERROR on unknown error (not found in $errors hash)
 *         <NAME> if $pkg[msg] returned <NAME>
 */
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
function comm_send_to_server ($command = "NOOP", $data = "", $ok = "", $errors = "") {
  assert (!empty($command));
  assert (!empty($data));

  global $_USER;
  global $_CONFIG;

  $data['galaxy'] = $_USER['galaxy_db'];
  if ($data['galaxy'] == "") $data['galaxy'] = $_CONFIG['default_db'];

  comm_init_server ();
  comm_s2s ($command, $data);
  $pkg = comm_recv_from_server ();
  comm_fini_server ();

  if ($pkg['status'] == "STATUS_OK") {
    if ($ok != "") print_line ($ok);
    return 1;
  }
  if ($pkg['status'] == "STATUS_ERR") {
    if (isset ($pkg['msg'])) {
      $errorstr = $errors[$pkg['msg']];
    } else {
      $errorstr = "";
      $pkg['msg'] = "No additional information available";
    }
    if (empty ($errorstr)) {
      print_line ("An unknown error occurred. The message is: ".$pkg['msg']);
    } else {
      print_line ("Processing Error: ".$errorstr);
      return $pkg['msg'];
    }
  }
  return 0;
}

?>
