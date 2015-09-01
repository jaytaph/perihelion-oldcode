use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::relation_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub relation () {
  my ($rx, $tx) = @_;

  my ($uid, $dst_uid, $state_id);
  my ($result, $tmp);
  my (@neutral_ids, @friend_ids, @enemy_ids, $neutral_arr, $friend_arr, $enemy_arr);
  my ($src_user);

  item_add ($tx, "pkg_cmd", "RELATION");

  $uid       = $rx->{uid};
  $dst_uid   = $rx->{dst_uid};
  $state_id  = $rx->{wid};

  # Get user stuff
  $result = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM g_knownspecies WHERE user_id=?", $uid);
  $tmp = px_mysql::fetchhash ($result);
  @neutral_ids = split (",", $tmp->{neutral_ids});
  @friend_ids  = split (",", $tmp->{friend_ids});
  @enemy_ids   = split (",", $tmp->{enemy_ids});


  # Remove the user from the fields (we don't know which field)
  foreach my $index (0 .. $#neutral_ids) {
    if ($neutral_ids[$index] == $dst_uid) { delete $neutral_ids[$index]; }
  }
  foreach my $index (0 .. $#friend_ids) {
    if ($friend_ids[$index] == $dst_uid) { delete $friend_ids[$index]; }
  }
  foreach my $index (0 .. $#enemy_ids) {
    if ($enemy_ids[$index] == $dst_uid) { delete $enemy_ids[$index]; }
  }

  $src_user = px_user::get_user ($uid);

  # Now, put the user in the correct field
  if ($state_id == constants->RELATION_FRIEND)  {
    push (@friend_ids, $dst_uid);
    px_mysql::send_message ($dst_uid, constants->MSG_TYPE_GLOBAL, constants->MESSAGE_PRIO_LOW, "Ambassador of the ".$src_user->{race}." race", "Changing status", "The ".$src_user->{race}." race changed their status to friendly.");
  }
  if ($state_id == constants->RELATION_NEUTRAL) {
    push (@neutral_ids, $dst_uid);
    px_mysql::send_message ($dst_uid, constants->MSG_TYPE_GLOBAL, constants->MESSAGE_PRIO_LOW, "Ambassador of the ".$src_user->{race}." race", "Changing status", "The ".$src_user->{race}." race changed their status to neutral.");
  }
  if ($state_id == constants->RELATION_ENEMY)     {
    push (@enemy_ids, $dst_uid);
    px_mysql::send_message ($dst_uid, constants->MSG_TYPE_GLOBAL, constants->MESSAGE_PRIO_HIGH, "Ambassador of the ".$src_user->{race}." race", "Changing to ENEMY status", "The ".$src_user->{race}." race changed their status to enemy. Prepare for war!");
  }

  # And put the arrays back
  my $neutral_arr = join (",", @neutral_ids);
  my $friend_arr = join (",", @friend_ids);
  my $enemy_arr = join (",", @enemy_ids);

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_knownspecies SET neutral_ids=?, enemy_ids=?, friend_ids=? WHERE user_id=?", $neutral_arr, $enemy_arr, $friend_arr, $uid);

  item_add ($tx, "status", "STATUS_OK");
  return;
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

