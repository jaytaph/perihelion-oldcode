package px_preset;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


# ===========================================================================================================
# Delete_Entry()
#
# Description:
#    Deletes an preset entry
#
# ParamList
#    Entry id       id of the entry
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub delete_entry ($) {
  my $entry_id = shift;
  errors::assert (errors::is_value ($entry_id));

  px_mysql::query (constants->QUERY_NOKEEP, "DELETE FROM g_presets WHERE id=?", $entry_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Create_Entry()
#
# Description:
#    Creates an preset entry
#
# ParamList
#     user_id    id of the user
#     name       name of the preset
#     distance   Distance part of the coordinate
#     angle      Angle part of the coordinate
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub create_entry ($$$$) {
  my ($user_id, $name, $distance, $angle) = @_;
  errors::assert (errors::is_value ($user_id));
  errors::assert (not errors::is_empty ($name));
  errors::assert (errors::is_value ($distance));
  errors::assert (errors::in_range ($angle, 0, 360000));

  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM g_presets WHERE name=? AND user_id=?", $name, $user_id);
  if ($sth->rows != 0) { return errors->ERR_ENTRY_ALREADY_EXISTS; }

  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO g_presets (user_id, name, distance, angle) VALUES (?,?,?,?)", $user_id, $name, $distance, $angle);
  return errors->ERR_OK;
}


