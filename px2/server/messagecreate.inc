use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::messagecreate_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Creates a new message. It depends on the dst_uid where the message
#   will be sent to:
#      * if dst_uid is positive, the message is for an user
#      * if dst_uid is negative, the message is for an alliance
#      * if dst_uid is zero, the message is for the galaxy.
#
#   We should change this into a decent flag or something, because we can't
#   add more destinations this way :(
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    SUBJECT             Empty subject specified
#    MSG                 Empty message specified
#    LEVEL               Level is not a number
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub messagecreate () {
  my ($rx, $tx) = @_;

  item_add ($tx, "pkg_cmd", "MESSAGECREATE");

  my $src_uid = $rx->{src_uid};
  my $dst_uid = $rx->{dst_uid};
  my $prio    = $rx->{prio};
  my $subject = $rx->{subject};
  my $msg     = $rx->{msg};
  my $level   = $rx->{level};
  my $target  = $rx->{target};

  # No decent params found...
  if ($src_uid eq "" or $target eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if ($subject eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "SUBJECT");
    return;
  }

  if ($msg eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "MSG");
    return;
  }

  # Check params for galaxy messages
  if ($target eq "GALAXY") {
    if (not $level =~ /^\d{1,5}$/) {
      item_add ($tx, "status", "STATUS_ERR");
	    item_add ($tx, "msg", "LEVEL");
      return;
    }
  }
  # Check params for alliance messages
  if ($target eq "ALLIANCE") {
  }
  # Check params for user messages
  if ($target eq "USER") {
    if ($prio eq "") {
      item_add ($tx, "status", "STATUS_ERR");
  	  item_add ($tx, "msg", "MSG");
      return;
    }
  }


  my $tmp = px_user::get_user ($src_uid);
  my $from = "High ambassador of the ".$tmp->{race}." race";

  if ($target eq "GALAXY") {
    px_message::create_message (px_message->MSG_GALAXY, 0, $from, $subject, $msg, $level);

  } elsif ($target eq "ALLIANCE") {
    px_message::create_message (px_message->MSG_ALLIANCE, $dst_uid, $from, $subject, $msg, $level, );

  } elsif ($target eq "USER") {
    px_message::create_message (px_message->MSG_USER, $dst_uid, $from, $subject, $msg, $prio);
  }
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#



