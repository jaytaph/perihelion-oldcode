package px_building;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


# ===========================================================================================================
# Get_Building ()
#
# Description:
#   Returns building information for a building_id
#
# ParamList
#   building_id   ID of the building
#
# Returns:
#   hashref       Hash reference of the information
#   ERR_*         Failure
#
my $sth_get_building = 0;
sub get_building ($) {
  my $building_id = shift;
  errors::assert (errors::is_value ($building_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_building == 0) {
    $sth_get_building = $px_mysql::dbhandle->prepare ("SELECT * FROM s_buildings WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_building->execute ($building_id) or die "Reason: $DBI::strerr";
  return $sth_get_building->fetchrow_hashref;
}


# ===========================================================================================================
# init_get_all_buildings_between_building_levels ()
#
# Description:
#   Browses through all buildings between 2 levels
#
# ParamList
#   min_level     Minimum level
#   max_level     Maximum level
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_buildings_between_building_levels ($$) {
  my $min_level = shift;
  my $max_level = shift;
  errors::assert (not errors::is_empty ($min_level));
  errors::assert (not errors::is_empty ($max_level));

  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_buildings WHERE building_level >= ? AND building_level <= ?", $min_level, $max_level);
  return $new_handle;
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
