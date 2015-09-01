<?php
  // Include Files
  include "includes.inc.php";

  // Session Identification
  session_identification ();

  // Extra headers for TD..
  $extra_headers =
        "<STYLE TYPE=\"text/css\" >      " .
        "  TH.red    { background-color: red }    " .
        "  TH.orange { background-color: orange } " .
        "  TH.white { background-color: white; color: black} " .
        "</STYLE>";
  print_header ($extra_headers);
  print_title ("Alliances",
               "An alliance is a coorperation between 2 or more users. Create your own alliance and increase it's size by let other users join in on your alliance.");


  $cmd = input_check ("create", 0,
                      "show", "aid", 0,
                      "partjoin", "!frmid", "aid", "uid", 0,
                      "requestjoin", "!frmid", "rid", 0);

  if ($cmd == "create") {
    create_alliance ();
  }
  if ($cmd == "show") {
    show_alliance (user_ourself(), $aid);
  }
  if ($cmd == "partjoin") {
    partjoin_alliance ($aid, $uid);
    show_alliance (user_ourself(), $aid);
  }
  if ($cmd == "requestjoin") {
    request_join_alliance ($rid);
  }


  create_submenu ( array (
                          "Show Alliances"      => "alliance.php?cmd=".encrypt_get_vars("show"),
                          "Create New Alliance" => "alliance.php?cmd=".encrypt_get_vars("create"),
                   )
                 );

  print_footer ();
  exit;


// ============================================================================
// Show_Alliance()
//
// Description:
//   Shows the alliance $alliance_id in the context of the user_id.
//   When alliance_id is empty, all alliances known to the user are shown.
//
// Parameters:
//    $user_id      User ID that views. This doesn't have to be the current user
//                  who plays the game (but most of the time it is).
//    $alliance_id  Alliance ID to view. If empty then all alliances that are
//                  known to the user are shown
//
// Returns:
//    Nothing
function show_alliance ($user_id, $alliance_id) {
  assert (is_numeric ($user_id));
  assert (is_numeric ($alliance_id) or empty ($alliance_id));

  if ($alliance_id != "") {
    alliance_showinfo ($alliance_id, $user_id);
    return;
  }

  $user = user_get_user ($user_id);

  $result  = sql_query ("SELECT * FROM g_alliance");
  while ($alliance = sql_fetchrow ($result)) {
    if ($user['alliance_id'] == $alliance['id']) {
      alliance_showinfo ($alliance['id'], $user_id);
    }
    if (alliance_discovered_a_member ($user_id, $alliance['id'])) {
      alliance_showinfo ($alliance['id'], $user_id);
    }
  }
}

// ============================================================================
// PartJoin_Alliance()
//
// Description:
//   Let's the user $user_id parts or joins an alliance designated by $alliance_id.
//   If the alliance_id is negative, it means the user parts. If it's positive, the
//   user joins the alliance.
//
// Parameters:
//     $alliance_id      Alliance ID of the alliance to join or part. A negative
//                       value means the user wants to part. A positive value
//                       means that the user wants to join.
//     $user_id          User ID that wants to join or part
//
// Returns:
//     Nothing
//
function partjoin_alliance ($alliance_id, $user_id) {
  assert (is_numeric ($alliance_id));
  assert (is_numeric ($user_id));

  if ($alliance_id < 0) {
    $data['alliancecmd'] = "PART";
  } else {
    $data['alliancecmd'] = "JOIN";
  }
  $data['aid'] = abs ($alliance_id);
  $data['uid'] = $user_id;

  $errors['PARAMS'] = "No decent params given.";
  comm_send_to_server ("ALLIANCE", $data, "", $errors);
}



// ============================================================================
// Request_Join_Alliance()
//
// Description:
//   Sends the answer of a request for joining an alliance to the px_server.
//   The server will handle this by sending a message to the user who wants
//   to join if he can do so.
//
// Parameters:
//    $request_id   ID of the entry with the request. A negative value means
//                  that the request was denied. A positive value means that
//                  the request is accepted.
//
// Returns:
//    Nothing
//
function request_join_alliance ($request_id) {
  assert (is_numeric ($request_id));

  $data['alliancecmd'] = "REQ";
  $data['aid'] = 0;
  $data['uid'] = $request_id;
  $errors['PARAMS'] = "No decent params given.";
  $errors['GONE'] = "This request is no longer valid.";
  $errors['ALREADYJOINED'] = "The user already joined another alliance.";
  comm_send_to_server ("ALLIANCE", $data, "", $errors);
}


// =============================================================================
// Alliance_Discovered_A_Member ()
//
// Description:
//    Checks if $uid has discovered a member of the alliance $aid.
//
// Parameters:
//    $user_id        User ID which wants to know
//    $alliance_id    Alliance ID to find
//
// Returns:
//    true      The user $uid has already discovered a member of the alliance $aid
//    false     The user $uid did not discover any members of the alliance $aid
//
function alliance_discovered_a_member ($user_id, $alliance_id) {
  assert (is_numeric ($user_id));
  assert (is_numeric ($alliance_id));

  $tmp = user_get_knownspecies ($user_id);
  $knownusers = csl_merge_fields ("", $tmp['csl_friend_id']);
  $knownusers = csl_merge_fields ($knownusers, $tmp['csl_neutral_id']);
  $knownusers = csl_merge_fields ($knownusers, $tmp['csl_enemy_id']);
  sort ($knownusers, SORT_NUMERIC);

  $result  = sql_query ("SELECT * FROM g_users WHERE alliance_id=".$alliance_id);
  $user = sql_fetchrow ($result);
  if (in_array ($user['user_id'], $knownusers)) return true;

  return false;
}


?>