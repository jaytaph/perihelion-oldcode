use strict;
use lib '../globalperl';
BEGIN { require ('constants.pm'); }

$main::alliance_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#    Alliance is a bit more work than others. First up there are 3 different
#    modes in which we can operate:
#      * We can join an open alliance
#      * We can ask for joining an closed alliance
#      * We can part an alliance
#
#    Parting an alliance is easy and is done in part_alliance().
#    Joining an alliance is done in join_alliance. This function checks if
#    it's an open or closed alliance. If open, than it joins the user
#    by calling actual_alliance_joining(). If the alliance is closed, it
#    will send a request message to the owner of the alliance and add an
#    entry in the g_alliance_pending table.
#    Trouble here is that the user could decide to join another alliance in
#    the meantime. If it does, the request is still pending.
#    Once the alliance owner has accepted the user, it will enter this
#    function with a positive $user_id which is actually the request_id
#    in the g_alliance_pending table. If the $user_id is negative, it means
#    the owner has rejected the user for this alliance.
#
# Returns:
#    PARAMS          Unknown parameters specified
#    INTERNAL        Internal error
#    ALREADYJOINED   User already joined this alliance (the owner might have
#                    clicked on an old request message).
#    GONE            User joined another alliance
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub alliance () {
  my ($rx, $tx) = @_;

  item_add ($tx, "pkg_cmd", "ALLIANCE");

  my $user_id      = $rx->{uid};
  my $alliance_id  = $rx->{aid};
  my $cmd          = $rx->{alliancecmd};

  # No decent params found...
  if ($user_id eq "" or $alliance_id eq "" or $cmd eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if ($cmd != "JOIN" or $cmd != "PART" or $cmd != "REQ") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if ($cmd eq "JOIN") {
    join_alliance ($user_id, $alliance_id);
  }
  if ($cmd eq "PART") {
    part_alliance ($user_id, $alliance_id);
  }
  if ($cmd eq "REQ") {
    # Check if the user already joined another alliance in the meanwhile...
    my $user = px_user::get_user ($user_id);
    if (errors::is_error ($user) and $user == errors->ERR_NO_SUCH_USER) {
      item_add ($tx, "status", "STATUS_ERR");
	    item_add ($tx, "msg", "INTERNAL");
      return;
    }

    if (px_alliance::user_in_alliance ($alliance_id, $user)) {
      item_add ($tx, "status", "STATUS_ERR");
  	  item_add ($tx, "msg", "ALREADYJOINED");
      return;
    }

    if (request_alliance ($user_id) == errors->ERR_FALSE) {
      item_add ($tx, "status", "STATUS_ERR");
  	  item_add ($tx, "msg", "GONE");
      return;
    }
  }

  item_add ($tx, "status", "STATUS_OK");
  return;
}

# -------------------------------------------------------------------------
#
# Returns:
#    ERR_TRUE       User joined the alliance
#    ERR_FALSE      User doesn't want to join anymore
#
sub request_alliance () {
  my $pending_id = shift;   # Note: we use user_id here.

  # Get the entry, or return with error if the item is gone...
  my $pending = px_alliance::get_alliance_request (abs($pending_id));
  if (errors::is_error ($pending) and $pending == errors->ERR_NO_SUCH_REQUEST) {
    return errors->ERR_FALSE;
  }

  # We don't need the request anymore
  px_alliance::delete_pending_request (abs($pending_id));


  # Was the request denied (negative alliance_id)?
  if ($pending_id < 0) {
    my $alliance = px_alliance::get_alliance ($pending->{alliance_id});

    my $owner = px_user::get_user ($pending->{user_id});
    px_message::create_message (px_message->MSG_USER, $alliance->{owner_id}, "Alliance Diplomat", "Alliance request denied",
                                "You denied the request for the ".$owner->{race}." race to join your alliance.",
                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
    px_message::create_message (px_message->MSG_USER, $pending->{user_id}, "Alliance Diplomat", "Alliance request denied",
                                "The alliance council has denied the request for you to enter the alliance ".$alliance->{name}.".",
                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
    return errors->ERR_CANNOT_JOIN;

  } else {
    actual_alliance_joining ($pending->{alliance_id}, $pending->{user_id});
  }

  # Now, remove the pending row from the table, we don't use it anymore...
  px_alliance::delete_pending_request (abs($pending_id));

  return errors->ERR_TRUE;
}


# -------------------------------------------------------------------------
#
sub part_alliance {
  my ($user_id, $alliance_id) = @_;

  my $alliance = px_alliance::get_alliance ($alliance_id);
  if (errors::is_error ($alliance) and $alliance == errors->ERR_NO_SUCH_ALLIANCE) { return errors->ERR_CANNOT_JOIN; }

  px_alliance::part_alliance ($alliance_id, $user_id);

  my $owner = px_user::get_user ($user_id);
  px_message::create_message (px_message->MSG_USER, $alliance->{owner_id}, "Alliance Diplomat", "User parted your alliance",
                              "The ".$owner->{race}." race parted your alliance.",
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
  px_message::create_message (px_message->MSG_USER, $user_id, "Alliance Diplomat", "Parting of alliance",
                              "You just parted the alliance ".$alliance->{name}.".",
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
}


# -------------------------------------------------------------------------
#
sub join_alliance {
  my ($user_id, $alliance_id) = @_;

  my $alliance = px_alliance::get_alliance ($alliance_id);
  if (error::is_error ($alliance) and $alliance == errors->ERR_NO_SUCH_ALLIANCE) { return errors->ERR_CANNOT_JOIN; }

  if ($alliance->{type} == constants->ALLIANCE_FREE_ENTRY) {
    actual_alliance_joining ($alliance_id, $user_id);
  }

  if ($alliance->{type} == constants->ALLIANCE_OWNER_CONFIRM) {
    px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_alliance_pending (alliance_id, user_id) VALUES (?, ?)", $alliance_id, $user_id);
    my $pending_id = px_mysql::get_last_insert_id ();

    my $owner = px_user::get_user ($user_id);
    px_message::create_message (px_message->MSG_USER, $alliance->{owner_id}, "Alliance Diplomat", "User requesting to join your alliance",
                                "The ".$owner->{race}." race has requested to join your alliance.<br><br><li><a href=alliance.php?rid=".$pending_id.">Click here to accept the request</a><br><li><a href=alliance.php?rid=".(0 - $pending_id).">Click here to deny the request</a>",
                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
    px_message::create_message (px_message->MSG_USER, $user_id, "Alliance Diplomat", "Request of joining of alliance",
                                "Since this alliance has strict rules on the joined members, the alliance counsil is deciding on your request to join the alliance. ".$alliance->{name}.".",
                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
  }
}


# -----------------------------------------------------------------------------------------------------
sub actual_alliance_joining {
  my ($alliance_id, $user_id) = @_;

  my $alliance = px_alliance::get_alliance ($alliance_id);
  if (is_error ($alliance) and $alliance == errors->ERR_NO_SUCH_ALLIANCE) { return errors->ERR_CANNOT_JOIN; }

  # Set the alliance_id in the user row
  px_alliance::join_alliance ($alliance_id, $user_id);

  # TODO: we have to check everybody who also is in this alliance. Make sure we set everybody to friendly, AND set them
  # in the discovered list if not already done so...

  # Send a message to the alliance owner and to the new joined user
  my $owner = px_user::get_user ($user_id);
  px_message::create_message (px_message->MSG_USER, $alliance->{owner_id}, "Alliance Diplomat", "User joined your alliance",
                              "The ".$owner->{race}." race joined your alliance.",
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
  px_message::create_message (px_message->MSG_USER, $user_id, "Alliance Diplomat", "Joining of alliance",
                              "You just joined the alliance ".$alliance->{name}.".",
                              constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_GLOBAL);
}



return 1;
#
# vim: ts=4 syntax=perl nowrap
#

