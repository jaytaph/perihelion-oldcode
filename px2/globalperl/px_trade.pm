package px_trade;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


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
#     ERR_OK     success
#     ERR_*      failure
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
#     ERR_*     failure
#
sub get_next_entry ($) {
  my $handle = shift;
  errors::assert (not errors::is_empty ($handle));

  return px_mysql::fetchhash ($handle);
}

# ===========================================================================================================
# init_get_all_traderoutes ()
#
# Description:
#   Browses through all traderoutes
#
# ParamList
#   none
#
# Returns:
#   int     loop handle for get_next_entry() and fini_loop()
#
sub init_get_all_traderoutes () {
  my $new_handle = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM a_trades");
  return $new_handle;
}


# ===========================================================================================================
# Reset_Temporary_Wait_Flag ()
#
# Description:
#   Sets the temporary waiting flag for traderoutes to zero. If we don't have ores on the planet,
#   we wait an certain ammount of time. This flag will count how long we already are waiting...
#
# ParamList
#   trade_id      ID of the traderoute
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub reset_temporary_wait_flag ($) {
  my $trade_id = shift;
  errors::assert (errors::is_value ($trade_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE a_trades SET tmp_wait=0 WHERE id=?", $trade_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Increase_Temporary_Wait_Flag ()
#
# Description:
#   Increases the temporary waiting flag for traderoutes. If we don't have ores on the planet,
#   we wait an certain ammount of time. This flag will count how long we already are waiting...
#
# ParamList
#   trade_id      ID of the traderoute
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub increase_temporary_wait_flag ($) {
  my $trade_id = shift;
  errors::assert (errors::is_value ($trade_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE a_trades SET tmp_wait=tmp_wait+1 WHERE id=?", $trade_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Source_Wait_Ticks ()
#
# Description:
#   If we don't have ores on the planet, we wait an certain ammount of time. This flag
#   specifies how long we will wait on the source planet of a traderoute.
#
# ParamList
#     trade_id   Id of the traderoute
#     ticks      Number of ticks to set
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_source_wait_ticks ($$) {
  my $trade_id = shift;
  my $ticks = shift;
  errors::assert (errors::is_value ($trade_id));
  errors::assert (errors::is_value ($ticks));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE a_trades SET src_wait=? WHERE id=?", $ticks, $trade_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Destination_Wait_Ticks ()
#
# Description:
#   If we don't have ores on the planet, we wait an certain ammount of time. This flag
#   specifies how long we will wait on the destination planet of a traderoute.
#
# ParamList
#     trade_id   Id of the traderoute
#     ticks      Number of ticks to set
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_destination_wait_ticks ($$) {
  my $trade_id = shift;
  my $ticks = shift;
  errors::assert (errors::is_value ($trade_id));
  errors::assert (errors::is_value ($ticks));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE a_trades SET dst_wait=? WHERE id=?", $ticks, $trade_id);
  return errors->ERR_OK;
}