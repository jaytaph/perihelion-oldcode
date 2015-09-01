package px_anomaly;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;

# ===========================================================================================================
# Get_Total_Anomaly_Count ()
#
# Description:
#   Returns the number of anomalies in the galaxy
#
# ParamList
#   none
#
# Returns:
#   int         Number of anomalies present in this sector
#
sub get_total_anomaly_count () {
  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT COUNT(*) AS count FROM s_anomalies");
  my $tmp = px_mysql::fetchhash ($sth);
  px_mysql::query_finish ($sth);

  return $tmp->{count};
}


# ===========================================================================================================
# Get_Type ()
#
# Description:
#    Returns a string with the anomaly type
#
# ParamList
#    anomaly_id      ID of the anomaly
#
# Returns:
#    string          Type of the anomaly
#
sub get_type ($) {
  my ($anomaly_id) = shift;
  errors::assert (errors::is_value ($anomaly_id));

  my $type = "anomaly";
  if (px_anomaly::is_planet ($anomaly_id)) {
    $type = 'planet';
  } elsif (px_anomaly::is_nebula ($anomaly_id)) {
    $type = "nebula";
  } elsif (px_anomaly::is_blackhole ($anomaly_id)) {
    $type = "blackhole";
  } elsif (px_anomaly::is_wormhole ($anomaly_id)) {
    $type = "wormhole";
  } elsif (px_anomaly::is_starbase ($anomaly_id)) {
    $type = "starbase";
	}

	return $type;
}

# ===========================================================================================================
# set_description ()
#
# Description:
#    Sets the description of the anomaly
#
# ParamList
#    anomaly_id      ID of the anomaly
#    description            Descriptionto set
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_description ($$) {
  my $anomaly_id = shift;
  my $description = shift;
  errors::assert (errors::is_value ($anomaly_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET description=? WHERE id=?", $description, $anomaly_id);
  return errors->ERR_OK;
}

# ===========================================================================================================
# set_name ()
#
# Description:
#    Sets the name of the anomaly
#
# ParamList
#    anomaly_id      ID of the anomaly
#    name            Name to set
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_name ($$) {
  my $anomaly_id = shift;
  my $name = shift;
  errors::assert (errors::is_value ($anomaly_id));
  errors::assert (not errors::is_empty ($name));

  if (anomaly_exists ($name)) { return errors->ERR_ALREADY_EXISTS; };

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET name=? WHERE id=?", $name, $anomaly_id);
  return errors->ERR_OK;
}


# ===========================================================================================================
# Anomaly_Exists ()
#
# Description:
#   Returns whether or not an Anomaly exists by that name
#
# ParamList
#   name     Name of the Anomaly
#
# Returns:
#   ERR_TRUE   Planet with that name already exists
#   ERR_FALSE  Planet with that name does not exist
#
sub anomaly_exists ($) {
  my $name = shift;
  errors::assert (not errors::is_empty ($name));

  my $sth = px_mysql::query (constants->QUERY_NOKEEP, "SELECT * FROM s_anomalies WHERE name=?", $name);

  if ($sth->rows >= 1) { return errors->ERR_TRUE; }
  return errors->ERR_FALSE;
}

# ===========================================================================================================
# Set_Owner ()
#
# Description:
#    Lets $user_id owns the anomaly
#
# ParamList
#    anomaly_id      ID of the anomaly
#    user_id         ID of the user
#
# Returns:
#     ERR_OK     success
#     ERR_*      failure
#
sub set_owner ($$) {
  my $anomaly_id = shift;
  my $user_id = shift;
  errors::assert (errors::is_value ($anomaly_id));
  errors::assert (errors::is_value ($user_id));

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE s_anomalies SET state_id=2, user_id=?, unknown=0 WHERE id=?", $user_id, $anomaly_id);
  return errors->ERR_OK;
}


# ===========================================================================================================
# is_wormhole ()
#
# Description:
#    Returns if the anomaly is a wormhole
#
# ParamList
#    anomaly_id      id of the anomaly
#
# Returns:
#     ERR_FALSE      no wormhole
#     ERR_TRUE       wormhole
#
sub is_wormhole ($) {
  my $anomaly_id = shift;
  errors::assert (errors::is_value ($anomaly_id));

  my $anomaly = px_anomaly::get_anomaly ($anomaly_id);

  if ($anomaly->{type} ne "W") { return errors->ERR_FALSE; }
  return errors->ERR_TRUE;
}

# ===========================================================================================================
# is_starbase ()
#
# Description:
#    Returns if the anomaly is a starbase
#
# ParamList
#    anomaly_id      id of the anomaly
#
# Returns:
#     ERR_FALSE      no starbase
#     ERR_TRUE       starbase
#
sub is_starbase ($) {
  my $anomaly_id = shift;
  errors::assert (errors::is_value ($anomaly_id));

  my $anomaly = px_anomaly::get_anomaly ($anomaly_id);

  if ($anomaly->{type} ne "S") { return errors->ERR_FALSE; }
  return errors->ERR_TRUE;
}

# ===========================================================================================================
# is_blackhole ()
#
# Description:
#    Returns if the anomaly is a blackhole
#
# ParamList
#    anomaly_id      id of the anomaly
#
# Returns:
#     ERR_FALSE      no blackhole
#     ERR_TRUE       blackhole
#
sub is_blackhole ($) {
  my $anomaly_id = shift;
  errors::assert (errors::is_value ($anomaly_id));

  my $anomaly = px_anomaly::get_anomaly ($anomaly_id);

  if ($anomaly->{type} ne "B") { return errors->ERR_FALSE; }
  return errors->ERR_TRUE;
}

# ===========================================================================================================
# is_nebula ()
#
# Description:
#    Returns if the anomaly is a nebula
#
# ParamList
#    anomaly_id      id of the anomaly
#
# Returns:
#     ERR_FALSE      no nebula
#     ERR_TRUE       nebula
#
sub is_nebula ($) {
  my $anomaly_id = shift;
  errors::assert (errors::is_value ($anomaly_id));

  my $anomaly = px_anomaly::get_anomaly ($anomaly_id);

  if ($anomaly->{type} ne "N") { return errors->ERR_FALSE; }
  return errors->ERR_TRUE;
}

# ===========================================================================================================
# is_planet ()
#
# Description:
#    Returns if the anomaly is a planet
#
# ParamList
#    anomaly_id      id of the anomaly
#
# Returns:
#     ERR_FALSE      no planet
#     ERR_TRUE       planet
#
sub is_planet ($) {
  my $anomaly_id = shift;
  errors::assert (errors::is_value ($anomaly_id));

  my $anomaly = px_anomaly::get_anomaly ($anomaly_id);

  if ($anomaly->{type} ne "P") { return errors->ERR_FALSE; }
  return errors->ERR_TRUE;
}
