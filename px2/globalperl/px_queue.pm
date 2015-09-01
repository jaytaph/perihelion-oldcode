package px_queue;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;



# ===========================================================================================================
# set_ticks ()
#
# Description:
#
# ParamList
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub set_ticks ($$) {
  my $queue_id = shift;
  my $ticks = shift;
  errors::assert (errors::is_value ($vessel_id));
  errors::assert (errors::is_value ($ticks));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE h_queue SET ticks=? WHERE id=?", $ticks, $queue_id);
  return errors->ERR_OK;
}


# ===========================================================================================================
# Delete_Entry ()
#
# Description:
#
# ParamList
#
# Returns:
#     ERR_OK      success
#     ERR_*       failure
#
sub delete_entry ($) {
  my $queue_id = shift;
  errors::assert (errors::is_value ($vessel_id));

  px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM h_queue WHERE id=?", $queue_id);
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
  errors::assert (not errors::is_empty($handle));

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
  errors::assert (not errors::is_empty($handle));

  return px_mysql::fetchhash ($handle);
}

# ===========================================================================================================
# init_get_all_entries ()
#
# Description:
#    Browses through all queue entries.
#
# ParamList
#    none
#
# Returns:
#    int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_entries () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM h_queue");
  return $new_handle;
}
