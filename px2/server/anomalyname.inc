use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::anomalyname_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Renames a anomaly name.
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    NAME                Name already exist
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub anomalyname () {
  my ($rx, $tx) = @_;

  item_add ($tx, "pkg_cmd", "SETNAME");

  my $anomaly_id = $rx->{anomaly_id};
  my $user_id = $rx->{user_id};
  my $name = $rx->{name};

  # No decent params found...
  if ($anomaly_id eq "" or $user_id eq "" or $name eq "") {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "PARAMS");
    return;
  }

  if (px_anomaly::anomaly_exists ($name)) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "NAME");
    return;
  }

  # Check if the planet is in the undiscovered list, then remove it
  # from there and place it in the planet_ids
  my $user_anomalies = px_anomaly::get_user_anomalies ($user_id);
  if (px_csl::in_list ($user_anomalies->{undiscovered_ids}, $anomaly_id)) {
    my $csl_undiscovered = px_csl::remove_from_list ($user_anomalies->{undiscovered_ids}, $anomaly_id);
    my $csl_discovered   = px_csl::add_to_list ($user_anomalies->{planet_ids}, $anomaly_id);
    px_anomaly::set_user_anomalies ($user_id, $csl_discovered, $csl_undiscovered);
	}

  # Set the name and owner
  px_anomaly::set_owner ($anomaly_id, $user_id);
  px_anomaly::set_name ($anomaly_id, $name);

  item_add ($tx, "status", "STATUS_OK");
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

