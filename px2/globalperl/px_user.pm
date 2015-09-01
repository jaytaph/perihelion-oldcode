package px_user;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

use constant SET_CREDITS_ABS    => 1;
use constant SET_CREDITS_ADD    => 2;
use constant SET_CREDITS_SUB    => 3;

# ===========================================================================================================
# Set_Known_Sectors ()
#
# Description:
#
# ParamList
#    user_id      user_id of the user
#    sector_csl   CSL with all known sectors
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_known_sectors ($$) {
  my $user_id = shift;
  my $sector_csl = shift;
  errors::assert (errors::is_value ($user_id));
  errors::assert (errors::is_value ($sector_csl));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_sectors SET sector_ids=? WHERE user_id=?", $sector_csl, $user_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Credits ()
#
# Description:
#    Sets the credits for user $user_id to $credits when SET_CREDITS_ABS
#    Subtracts $credits from $user_id when SET_CREDITS_SUB
#    Adds $credits from $user_id when SET_CREDITS_ADD
#
# ParamList
#    user_id      user_id of the user
#    ammount      ammount of credits
#    offset       offset
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_credits ($$$) {
  my $user_id = shift;
  my $ammount = shift;
  my $offset = shift;
  errors::assert (errors::is_value ($user_id));
  errors::assert (errors::is_value ($ammount));
  errors::assert (errors::in_range ($offset, 1, 3));

  if ($offset == SET_CREDITS_ABS) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET credits=? WHERE user_id=?", $ammount, $user_id);
  }
  if ($offset == SET_CREDITS_SUB) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET credits=credits-? WHERE user_id=?", $ammount, $user_id);
  }
  if ($offset == SET_CREDITS_ADD) {
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET credits=credits+? WHERE user_id=?", $ammount, $user_id);
  }

  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Speed ()
#
# Description:
#   Sets the maximum speed capabilities of a user
#
# ParamList
#   user_id    ID of the user
#   impulse    Impulse speed
#   warp       Warp speed
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_speed ($$$) {
  my $user_id = shift;
  my $impulse = shift;
  my $warp = shift;

  errors::assert (errors::is_value($user_id));
  errors::assert (errors::is_in_range ($impulse, 0, 100));
  errors::assert (errors::is_in_range ($warp, 0, 100));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET impulse=?, warp=? WHERE user_id=?", $impulse, $warp, $user_id);
  return errors->ERR_OK;
}


# ===========================================================================================================
# Set_Anomalies ()
#
# Description:
#    Sets known and unknown anomalies for the user
#
# ParamList
#    user_id            ID of the user
#    discovered_csl     Comma seperated list of known planets
#    undiscovered_csl   Comma seperated list of known but undiscovered planets
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_anomalies ($) {
  my ($user_id) = shift;
  my ($discovered_csl) = shift;
  my ($undiscovered_csl) = shift;
  errors::assert (errors::is_value ($user_id));
  errors::assert (errors::is_csl ($discovered_csl));
  errors::assert (errors::is_csl ($undiscovered_csl));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_planets SET planet_ids=?, undiscovered_ids=? WHERE user_id=?", $discovered_csl, $undiscovered_csl, $user_id);
  return errors->ERR_OK;
}



# ===========================================================================================================
# Get_Anomalies ()
#
# Description:
#    Returns all known and unknown anomalies for the user
#
# ParamList
#    user_id      ID of the user
#
# Returns:
#    hashref       Reference to info hash
#    ERR_*         Failure
#
my $sth_get_anomalies = 0;
sub get_anomalies ($) {
  my ($user_id) = shift;
  errors::assert (errors::is_value ($user_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_anomalies == 0) {
    $sth_get_anomalies = $px_mysql::dbhandle->prepare ("SELECT * FROM g_planets WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_anomalies->execute ($user_id) or die "Reason: $DBI::strerr";
  return $sth_get_anomalies->fetchrow_hashref;
}


# ===========================================================================================================
# Get_Species ()
#
# Description:
#   Gets the race information of a certain user
#
# ParamList
#   user_id   ID of the user
#
# Returns:
#   hashref   Reference to hash with information
#   ERR_*     Failure
#
my $sth_get_species = 0;
sub obsolete_get_species ($) {
  my $user_id = shift;
  errors::assert (errors::is_value($user_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_species== 0) {
    $sth_get_species = $px_mysql::dbhandle->prepare ("SELECT * FROM s_species WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_species->execute ($user_id) or die "Reason: $DBI::strerr";
  return $sth_get_species->fetchrow_hashref;
}

# ===========================================================================================================
# Get_perihelion_User ()
#
# Description:
#   Gets the global perihelion user information of a certain user
#
# ParamList
#   user_id   ID of the user
#
# Returns:
#   hashref   Reference to hash with information
#   ERR_*     Failure
#
my $sth_get_perihelion_user = 0;
sub get_perihelion_user ($) {
  my $user_id = shift;
  errors::assert (errors::is_value($user_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_perihelion_user == 0) {
    $sth_get_perihelion_user = $px_mysql::dbhandle->prepare ("SELECT * FROM perihelion.u_users WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_perihelion_user->execute (int($user_id)) or die "Reason: $DBI::strerr";
  return $sth_get_perihelion_user->fetchrow_hashref;
}

# ===========================================================================================================
# Get_Anomaly_Lists ()
#
# Description:
#   Gets the anomaly lists for this user.
#
# ParamList:
#   user_id   ID of the user
#
# Returns:
#   hashref   Reference to hash with information
#   ERR_*     Failure
#
my $sth_get_anomaly_lists = 0;
sub get_anomaly_lists ($) {
  my $user_id = shift;
  errors::assert (errors::is_value($user_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_anomaly_lists == 0) {
    $sth_get_anomaly_lists = $px_mysql::dbhandle->prepare ("SELECT * FROM g_planets WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_anomaly_lists->execute (int($user_id)) or die "Reason: $DBI::strerr";
  return $sth_get_anomaly_lists->fetchrow_hashref;
}

# ===========================================================================================================
# Get_User ()
#
# Description:
#   Gets the user information of a certain user
#
# ParamList:
#   user_id   ID of the user
#
# Returns:
#   hashref   Reference to hash with information
#   ERR_*     Failure
#
my $sth_get_user = 0;
sub get_user ($) {
  my $user_id = shift;
  errors::assert (errors::is_value($user_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_user == 0) {
    $sth_get_user = $px_mysql::dbhandle->prepare ("SELECT * FROM g_users WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_user->execute (int($user_id)) or die "Reason: $DBI::strerr";
  return $sth_get_user->fetchrow_hashref;
}

# ===========================================================================================================
# Get_User_Flags ()
#
# Description:
#   Gets the user flags of a certain user
#
# ParamList
#   user_id   ID of the user
#
# Returns:
#   hashref   Reference to hash with information
#   ERR_*     Failure
#
my $sth_get_user_flags = 0;
sub get_user_flags ($) {
  my $user_id = shift;
  errors::assert (errors::is_value($user_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_user_flags == 0) {
    $sth_get_user_flags = $px_mysql::dbhandle->prepare ("SELECT * FROM g_flags WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_user_flags->execute (int($user_id)) or die "Reason: $DBI::strerr";
  return $sth_get_user_flags->fetchrow_hashref;
}

# ===========================================================================================================
# Get_Random_User ()
#
# Description:
#   Returns a random user from the galaxy.
#
# ParamList
#   None
#
# Returns:
#   Int     random ID of a user
#
sub get_random_user () {
  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT COUNT(*) as count FROM g_users");
  my $row = px_mysql::fetchhash ($sth);

  $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM g_users LIMIT ".int(rand($row->{count})).",1");
  $row = px_mysql::fetchhash ($sth);

  return $row->{user_id};
}

# ===========================================================================================================
# Update_Total_Population ()
#
# Description:
#   Sets the total population from a user to $population
#
# ParamList:
#     user_id      ID of the user
#     population   New total population
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
my $sth_update_total_population = 0;
sub update_total_population ($$) {
  my $user_id = shift;
  my $population = shift;
  errors::assert (errors::is_value($user_id));
  errors::assert (errors::is_value($population));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_update_total_population == 0) {
    $sth_update_total_population = $px_mysql::dbhandle->prepare ("UPDATE g_users SET population=population+? WHERE user_id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_update_total_population->execute ($population, $user_id) or die "Reason: $DBI::strerr";
  return errors->ERR_OK;
}



# ===========================================================================================================
# Fini_Loop()
#
# Description:
#   Finishes an loop started with an init_get_all_* function
#
# ParamList
#   handle    id of the loop
#
# Returns:
#   int       error code
#
sub fini_loop ($) {
  my $handle = shift;
  errors::assert (not errors::is_empty ($handle));

  if ($handle != 0) {
    px_mysql::query_finish ($handle);
  }
  return errors->ERR_OK;
}

# ===========================================================================================================
# Get_Next_Entry ()
#
# Description:
#   Get's the next item in the loop started with an init_get_all_* function
#
# ParamList
#     handle    handle of the loop
#
# Returns:
#     hash      hash of the item-data
#     false     beyond last item of the loop
#
sub get_next_entry ($) {
  my $handle = shift;
  errors::assert (not errors::is_empty ($handle));

  return px_mysql::fetchhash ($handle);
}

# ===========================================================================================================
# init_get_all_active_users ()
#
# Description:
#   Browses through all active users in the game
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_active_users () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM g_users WHERE active=1");
  return $new_handle;
}




