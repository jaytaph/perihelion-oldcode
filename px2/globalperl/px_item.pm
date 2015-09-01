package px_item;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

# ===========================================================================================================
# is_vessel_item ()
#
# Description:
#    Returns if the item is a vessel
#
# ParamList
#    vessel_id           id of the item
#
# Returns:
#     0       no vessel
#     1       vessel
#
sub is_vessel_item ($) {
  my $item_id = shift;
  errors::assert (errors::is_value ($item_id));

  my $item = px_item::get_item ($item_id);
  return errors->ERR_TRUE if ($item->{type} eq constants->ITEM_TYPE_VESSEL);
  return errors->ERR_FALSE;
}

# ===========================================================================================================
# is_weapon ()
#
# Description:
#    Returns if the item is a weapon
#
# ParamList
#    weapon_id           id of the item
#
# Returns:
#     0       no weapon
#     1       weapon
#
sub is_weapon ($) {
  my $item_id = shift;
  errors::assert (errors::is_value ($item_id));

  my $item = px_item::get_item ($item_id);
  return errors->ERR_TRUE if ($item->{type} eq constants->ITEM_TYPE_WEAPON);
  return errors->ERR_FALSE;
}

# ----------------------------------------------------------------------------
my $sth_get_item = 0;
sub get_item ($) {
  my $item_id = shift;
  errors::assert (errors::is_value ($item_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_item == 0) {
    $sth_get_item = $px_mysql::dbhandle->prepare ("SELECT * FROM s_inventions WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_item->execute ($item_id) or die "Reason: $DBI::strerr";
  return $sth_get_item->fetchrow_hashref;
}
