use strict;
use lib '../globalperl/';
use px_vessel;
BEGIN { require ('constants.pm'); }

$main::move_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub move () {
  my ($rx, $tx) = @_;
  my ($planet_id, $name, $impulse, $warp, $type, $user_id, $vessel_id, $dst_id);
  my ($dst_sector_id, $dst_planet_id, $dst_sector, $dst_planet);
  my $delta_distance;
  my $delta_angle;
  my $ticks;
  my $sth;



  item_add ($tx, "pkg_cmd", "MOVE");

  $vessel_id = $rx->{vid};
  $user_id = $rx->{uid};
  $dst_id = $rx->{did};

  # Set the correct galaxy. This makes sure we move the correct vessel in the database..
  px_mysql::select_galaxy ($rx->{galaxy});

  # Check if we have a sector transfer (S in front of the destionation id)
  my $sector_transfer = 0;
  if ($dst_id =~ /^S/i) {
    $sector_transfer = 1;
	  $dst_id =~ s/S//;
  }

  # No decent params found...
  if ($vessel_id eq "" or $dst_id eq "" or $user_id eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  # Planet id 0, this means hold at current position...
  if ($dst_id == 0) {
    px_vessel::stop ( { vessel_id=>$vessel_id } );
    item_add ($tx, "status", "STATUS_OK");
	  return;
  }

  # TODO
  # We should check if the vessel is an explorer and that the dst_id
  # is NOT on the planet_ids list, send NOEXPLORE if so...
  my $vessel     = px_vessel::get_vessel ($vessel_id);
  my $src_planet = px_planet::get_planet ($vessel->{planet_id});
  my $src_sector = px_sector::get_sector ($vessel->{sector_id});

  if ($sector_transfer == 0) {
    my $result = px_vessel::move ( { vessel_id=>$vessel_id, sector=>"no", planet_id=>$dst_id } );

  }
  if ($sector_transfer == 1) {
    my $result = px_vessel::move ( { vessel_id=>$vessel_id, sector=>"yes", sector_id=>$dst_id } );
  }

  item_add ($tx, "status", "STATUS_OK");
  return;
}






return 1;
#
# vim: ts=4 syntax=perl nowrap
#

