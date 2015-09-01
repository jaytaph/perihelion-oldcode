use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::anomalydesc_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Changes a anomaly description.
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub anomalydesc () {
  my ($rx, $tx) = @_;

  item_add ($tx, "pkg_cmd", "SETDESC");

  my $anomaly_id = $rx->{anomaly_id};
  my $description = $rx->{description};

  # No decent params found...
  if ($anomaly_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
  	item_add ($tx, "msg", "PARAMS");
    return;
  }

  px_anomaly::set_description ($anomaly_id, $description);

  item_add ($tx, "status", "STATUS_OK");
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

