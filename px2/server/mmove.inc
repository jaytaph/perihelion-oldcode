use strict;
use lib '../globalperl/';
use px_vessel;
BEGIN { require ('constants.pm'); }

$main::mmove_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Manually move a ship to a certain coordinate.
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    UNKNOWN_ERROR       Internal error
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub mmove () {
  my ($rx, $tx) = @_;
  my ($planet_id, $name, $impulse, $warp, $type, $user_id, $vessel_id, $dst_id);
  my ($distance, $angle);
  my $delta_distance;
  my $delta_angle;
  my $ticks;
  my $sth;

  item_add ($tx, "pkg_cmd", "MMOVE");

  $vessel_id = $rx->{vid};
  $user_id   = $rx->{uid};
  $distance  = $rx->{distance};
  $angle     = $rx->{angle};

  # Set the correct galaxy. This makes sure we move the correct vessel in the database..
  px_mysql::select_galaxy ($rx->{galaxy});

  # No decent params found...
  if ($vessel_id eq "" or $distance eq "" or $angle eq "" or $user_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  my $result = px_vessel::move ( { vessel_id=>$vessel_id, distance=>$distance, angle=>$angle } );

  if ($result == 0) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "UNKNOWN_ERROR");
  } else {
    item_add ($tx, "status", "STATUS_OK");
  }
  return;
}




return 1;
#
# vim: ts=4 syntax=perl nowrap
#

