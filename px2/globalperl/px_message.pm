package px_message;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

use constant MSG_USER       => 1;
use constant MSG_GALAXY     => 2;
use constant MSG_ALLIANCE   => 3;





# ===========================================================================================================
# create_message ()
#
# Description:
#    Creates a new message
#
# ParamList
#   mailbox       MSG_USER, MSG_GALAXY, MSG_ALLIANCE
#   user_id       id of the user to send the message to
#   from          name of the department or user or race who sends the message
#   subject       subject of the message
#   msg           string with the message (can contain HTML)
#
#   prio          MSG_USER    priority of the message (MSG_PRIO_*)
#   type          MSG_USER    department where to store the message in (MSG_TYPE_*)
#
#   level         MSG_GALAXY  minimal exploration level to view the message
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub create_message {
  my $mailbox = shift;
  my $user_id = shift;
  my $from = shift;
  my $subject = shift;
  my $msg = shift;
  errors::assert (not errors::is_empty ($mailbox));
  errors::assert (errors::is_value ($user_id));
  errors::assert (not errors::is_empty ($from));
  errors::assert (not errors::is_empty ($subject));
  errors::assert (not errors::is_empty ($msg));

  print "Mailbox: $mailbox\n";


  if ($mailbox eq MSG_USER) {
    my $prio = shift;
    my $type = shift;
    errors::assert (not errors::is_empty ($prio));
    errors::assert (not errors::is_empty ($type));
    px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO m_messages (user_id, type, deleted, priority, datetime, msg_from, msg_subject, text) VALUES (?, ?, 0, ?, NOW(), ?, ?, ?) ", $user_id, $type, $prio, $from, $subject, $msg);

    # sometimes, messages can leak to other users.. be carefull with what you send :)
    my $tmp = this_message_leaks ($user_id);
    if (not errors::is_error ($tmp)) {
      px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO m_messages (user_id, type, deleted, priority, datetime, msg_from, msg_subject, text) VALUES (?, ?, 0, ?, NOW(), ?, ?, ?) ", $tmp, constants->MSG_TYPE_GLOBAL, constants->MESSAGE_PRIO_HIGH, "Message Interception" , "Interception: ".$subject, "This message was intercepted from outer space:<br><br>".$msg);
    }
  }

  if ($mailbox eq MSG_ALLIANCE) {
    px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO m_alliance (deleted, alliance_id, datetime, msg_from, msg_subject, text) VALUES (0, ?, NOW(), ?, ?, ?) ", $user_id, $from, $subject, $msg);
  }

  if ($mailbox eq MSG_GALAXY) {
    my $level = shift;
    errors::assert (errors::is_value ($level));
    px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO m_galaxy (deleted, level, datetime, msg_from, msg_subject, text) VALUES (0, ?, NOW(), ?, ?, ?) ", $level, $from, $subject, $msg);
  }

  return errors->ERR_OK;
}

# ===========================================================================================================
# delete_message ()
#
# Description:
#    Delete a message from the system
#
# ParamList
#    msg_id            message id
#    mailbox           MSG_USER, MSG_GALAXY, MSG_ALLIANCE
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub delete_message ($$) {
  my $msg_id = shift;
  my $mailbox = shift;
  errors::assert (errors::is_value ($msg_id));
  errors::assert (not errors::is_empty ($mailbox));

  if ($mailbox eq MSG_USER) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE m_messages SET deleted=1 WHERE id=?", $msg_id);
  }
  if ($mailbox eq MSG_GALAXY) {
    return errors->ERR_CANT_DELETE_GALAXY_MESSAGE;
    # px_mysql::query (constants->QUERY_NOKEEP, "UPDATE m_galaxy SET deleted=1 WHERE id=?", $msg_id);
  }
  if ($mailbox eq MSG_ALLIANCE) {
    return errors->ERR_CANT_DELETE_ALLIANCE_MESSAGE;
    # px_mysql::query (constants->QUERY_NOKEEP, "UPDATE m_alliance SET deleted=1 WHERE id=?", $msg_id);
  }

  return errors->ERR_OK;
}


# ===========================================================================================================
# destination_is_galaxy ()
#
# Description:
#    Returns ERR_TRUE if the message is targeted for the galaxy
#
# ParamList
#    user_id            user id
#
# Returns:
#    ERR_TRUE      Message is for the galaxy
#    ERR_FALSE     Message is not for the galaxy
#
sub destination_is_galaxy ($) {
  my $user_id = shift;
  errors::assert (errors::is_value ($user_id));

  if ($user_id == 0) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}

# ===========================================================================================================
# destination_is_user ()
#
# Description:
#    Returns ERR_TRUE if the message is targeted for a user
#
# ParamList
#    user_id            user id
#
# Returns:
#    ERR_TRUE      Message is for a user
#    ERR_FALSE     Message is not for a user
sub destination_is_user ($) {
  my $user_id = shift;
  errors::assert (errors::is_value ($user_id));

  if ($user_id > 0) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}

# ===========================================================================================================
# destination_is_alliance ()
#
# Description:
#    Returns ERR_TRUE if the message is targeted for an alliance
#
# ParamList
#    user_id            user id
#
# Returns:
#    ERR_TRUE      Message for an alliance
#    ERR_FALSE     Message not for an alliance
sub destination_is_alliance ($) {
  my $user_id = shift;
  errors::assert (errors::is_value ($user_id));

  if ($user_id < 0) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}


# ===========================================================================================================
# this_message_leaks ()
#
# Description:
#    Returns ERR_FALSE if the message does not leak, or the user id to leak the message to.
#
# ParamList
#    user_id       user id of the owner of this message
#
# Returns:
#    ERR_FALSE     Message is not for a user
#    id            user id of the user to leak the message to.
#
sub this_message_leaks ($) {
  my $user_id = shift;
  errors::assert (errors::is_value ($user_id));

  my $dst_user_id;
  my $flags;

  if (rand(100) >= $px_config::galaxy->{msg_leakage}) { return errors->ERR_FALSE; }

  $dst_user_id = px_user::get_random_user ();
  $flags = px_user::get_user_flags ($dst_user_id);

  print "Message want to leak to user $dst_user_id\n";

  if ($dst_user_id == $user_id) { return errors->ERR_FALSE; }
  if ($flags->{can_warp} != 1) { return errors->ERR_FALSE; }

  print "Leaking message to user $dst_user_id\n";
  return $dst_user_id;
}