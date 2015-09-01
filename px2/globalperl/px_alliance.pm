package px_alliance;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


# ===========================================================================================================
# Part_Alliance ()
#
# Description:
#   Removes a user from an alliance
#
# Paramlist:
#     alliance_id    ID of the alliance
#     user_id        ID of the user
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub part_alliance ($$) {
  my $alliance_id = shift;
  my $user_id = shift;
  errors::assert (errors::is_value ($alliance_id));
  errors::assert (errors::is_value ($user_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET alliance_id=? WHERE user_id=?", constants->ALLIANCE_NONE, $user_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Join_Alliance ()
#
# Description:
#   Adds a user to an alliance
#
# Paramlist:
#     alliance_id    ID of the alliance
#     user_id        ID of the user
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub join_alliance ($$) {
  my $alliance_id  = shift;
  my $user_id = shift;
  errors::assert (errors::is_value ($alliance_id));
  errors::assert (errors::is_value ($user_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET alliance_id=? WHERE user_id=?", $alliance_id, $user_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Delete_Pending_Request ()
#
# Description:
#   Deletes an entry in the pending-table. This user is either rejected or accepted by the owner of the alliance
#
# Paramlist:
#     pending_id    ID of the pending request (negative value = ???  positive value = ???)
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub delete_pending_request ($) {
  my $pending_id = shift;
  errors::assert (errors::is_value ($pending_id));


  # The request could be denied or accepted. This is marked by a negative or positive value.
  # id = -5 means id 5 is rejected.
  $pending_id = abs ($pending_id);

  px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM g_alliance_pending WHERE id=?", $pending_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# User_In_Alliance()
#
# Description:
#   Returns wether or not a user is part of an alliance
#
# ParamList
#     alliance_id  id of the alliance to seek in
#     user_id      id of the user to seek
#
# Returns:
#     ERR_TRUE     user is in alliance
#     ERR_FALSE    user is not part of alliance
#
sub user_in_alliance ($$) {
  my $alliance_id = shift;
  my $user_id = shift;
  errors::assert (errors::is_value ($alliance_id));
  errors::assert (errors::is_value ($user_id));

  my $user = px_user::get_user ($user_id);

  if ($user->{alliance_id} == $alliance_id) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}

# ===========================================================================================================
# Get_Alliance()
#
# Description:
#    Returns hash with alliance info
#
# ParamList
#    alliance_id  id of the alliance to look for
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
my $sth_get_alliance = 0;
sub get_alliance ($) {
  my $alliance_id = shift;
  errors::assert (errors::is_value ($alliance_id));


  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_alliance == 0) {
    $sth_get_alliance = $px_mysql::dbhandle->prepare ("SELECT * FROM g_alliance WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_alliance->execute ($alliance_id) or die "Reason: $DBI::strerr";

  if ($sth_get_alliance->rows == 0) { return errors->ERR_NO_SUCH_ALLIANCE; }
  return $sth_get_alliance->fetchrow_hashref;
}

# ===========================================================================================================
# Get_Alliance_Request()
#
# Description:
#    Returns hash with alliance request info
#
# ParamList
#    request_id  id of the request to look for
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
my $sth_get_alliance_request = 0;
sub get_alliance_request ($) {
  my $request_id = shift;
  errors::assert (errors::is_value ($request_id));


  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_alliance_request == 0) {
    $sth_get_alliance_request = $px_mysql::dbhandle->prepare ("SELECT * FROM g_alliance_pending WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_alliance_request->execute ($request_id) or die "Reason: $DBI::strerr";

  if ($sth_get_alliance_request->rows == 0) { return errors->ERR_NO_SUCH_REQUEST; }
  return $sth_get_alliance_request->fetchrow_hashref;
}
