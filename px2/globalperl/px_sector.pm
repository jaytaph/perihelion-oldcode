package px_sector;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

# ===========================================================================================================
# Create()
#
# Description:
#    Generates a new sector and returns it's sector id
#
# ParamList
#    user_id           ID of the user of the sector
#    name              Sector name
# Returns:
#    sector_id         ID of the sector in the database
#
sub create {
  my $param = @_[0];

  my ($private, $sth, $tmp);

  # By default, the sector is a private one
  $private = 1;

  # user id 0 means the sector is not owned by anyone (and thus not a private sector as well)
  if ($param->{user_id} == 0) {
    $private = 0;
  }

  # --------------------
  # Create our sector
  my ($distance, $angle) = px_sector::get_random_coordinate ();
  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO s_sectors (id, user_id, private, sector, name, distance, angle)
                                             values (0, ?, $private, LAST_INSERT_ID(), ?, ?, ?)",
                                             $param->{user_id}, $param->{name}, $distance, $angle);
  my $sector_id = px_mysql::get_last_insert_id ();

  # We generate a name from the sector id when we don't have any name for it...
  if ($param->{user_id} == 0) {
    my $sector_name = sprintf ("%04d", $sector_id);
    px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_sectors SET sector=? WHERE id=?", $sector_id, $sector_id);
    px_sector::set_name ($sector_id, $sector_name);
  }

  return $sector_id;
}

# ===========================================================================================================
# Set_Owner ()
#
# Description:
#   Sets the owner of a sector
#
# ParamList
#   sector_id   ID of the sector
#   user_id     ID of the user
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_owner ($$) {
  my $sector_id = shift;
  my $user_id = shift;
  errors::assert (errors::is_value ($sector_id));
  errors::assert (errors::is_value ($user_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_sectors SET user_id=? WHERE id=?", $user_id, $sector_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Set_Name ()
#
# Description:
#   Sets the name of a sector.
#
# ParamList
#     sector_id   ID of the sector to name
#     name        New name of the sector
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_name ($$) {
  my $sector_id = shift;
  my $name = shift;
  errors::assert (errors::is_value ($sector_id));
  errors::assert (not errors::is_empty ($name));

  if (sector_exists ($name)) { return errors->ERR_ALREADY_EXISTS; };

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_sectors SET name=? WHERE id=?", $name, $sector_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# Get_Anomaly_Count ()
#
# Description:
#   Returns the number of anomalies in this sector
#
# ParamList
#   sector_id    ID of the sector
#
# Returns:
#   int         Number of anomalies present in this sector
#
sub get_anomaly_count ($) {
  my $sector_id = shift;
  errors::assert (errors::is_value ($sector_id));

  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT COUNT(*) AS count FROM s_anomalies WHERE sector_id=?", $sector_id);
  my $tmp = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);

  return $tmp->{count};
}

# ===========================================================================================================
# Get_Sector ()
#
# Description:
#   Returns a hash from a certain sector pointed by $sector_id
#
# ParamList
#   sector_id    ID of the sector
#
# Returns:
#   hashref      Reference to a hash with sector information
#
my $sth_get_sector = 0;
sub get_sector ($) {
  my $sector_id = shift;
  errors::assert (errors::is_value ($sector_id));

  # If this is the first call, prepare the query, otherwise just execute it..
  if ($sth_get_sector == 0) {
    $sth_get_sector = $px_mysql::dbhandle->prepare ("SELECT * FROM s_sectors WHERE id=?") or die "Cannot prepare query. Reason: $DBI::strerr";
  }

  # Execute and return the first hashref
  $sth_get_sector->execute ($sector_id) or die "Reason: $DBI::strerr";
  return $sth_get_sector->fetchrow_hashref;
}

# ===========================================================================================================
# Get_Sectors_From_User ()
#
# Description:
#   Returns a comma seperated list (CSL) of sectors visible for a particulair user
#
# ParamList
#     user_id    ID of the user
#
# Returns:
#     CSL        Comma Seperated List of the visible sectors
#
sub get_sectors_from_user ($) {
  my $user_id = shift;
  errors::assert (errors::is_value ($user_id));

  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM g_sectors WHERE user_id=?", $user_id);
  my $sectorlist = px_mysql::fetchhash ($sth);

  return $sectorlist;
}

# ===========================================================================================================
# Sector_Exists ()
#
# Description:
#   Returns wether or not a sector exists or not by a certain name.
#
# ParamList
#     sector_name  Name of the sector to look for
#
# Returns:
#     ERR_TRUE     Sector exists
#     ERR_FALSE    Sector does not exists
#
sub sector_exists ($) {
  my $sector_name = shift;
  errors::assert (not errors::is_empty ($sector_name));

  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM s_sectors WHERE name=?", $sector_name);
  if ($sth->rows != 0) { return errors->ERR_TRUE; }

  return errors->ERR_FALSE;
}

# ===========================================================================================================
# Get_Random_Coordinate ()
#
# Description:
#   Generates a random coordinate inside the galaxy boundaries.
#
# ParamList
#   none
#
# Returns:
#   (distance, angle)     New coordinate
#
sub get_random_coordinate () {
  my $distance = int( rand($px_config::galaxy->{galaxy_size} - $px_config::galaxy->{galaxy_core}) + $px_config::galaxy->{galaxy_core});
  my $angle = int(rand(360000));
  return ($distance, $angle);
}

# ===========================================================================================================
# Set_Sector_As_Public ()
#
# Description:
#   Sets the sector to a public available sector. Other users can see this sector.
#
# ParamList
#   sector_id   ID of the sector
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_sector_as_public ($) {
  my $sector_id = shift;
  errors::assert (errors::is_value ($sector_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_sectors SET private=0 WHERE id=?", $sector_id);
  return errors->ERR_OK;
}