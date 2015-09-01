use strict;
use px_planet;
use px_sector;
use px_nebula;
use px_blackhole;
use px_wormhole;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::newsector_privilege = UNRESTRICTED;

# -------------------------------------------------------------------------
# Description:
#   Creates a new sector with differnt kinds of anomalies inside. This
#   command is privileged to administrators only. We don't really check
#   that here, but we should.
#
# Returns:
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub newsector () {
  my ($rx, $tx) = @_;

  my ($tmp, $distance, $dbh, $sth);

  item_add ($tx, "pkg_cmd", "NEWSECTOR");

  # TODO: Yes? And who are you? Whoes goes there?? Identify yourselves or
  # fear the sharp edge of my mighty blade....:  in other words: check
  # user privilege before proceeding.

  # Create sector
  my $sector_id = px_sector::create ( { user_id=>0, name=>"" } );

  # Create the planets
  my $planet_count = int(rand (10)) + 3;
  for (my $i = 1; $i!= $planet_count+1; $i++) {
    if (rand(1000) < 40) {
      print "Creating nebula<br>\n";
      ($tmp, $distance) = px_nebula::create ( { order=>$i, sector_id=>$sector_id, user_id=>0, distance=>$distance } );

    } elsif (rand(1000) < 20) {
      print "Creating blackhole<br>\n";
      ($tmp, $distance) = px_blackhole::create ( { order=>$i, sector_id=>$sector_id, user_id=>0, distance=>$distance } );

    } elsif (rand(1000) <  5) {
      print "Creating wormhole<br>\n";
      ($tmp, $distance) = px_wormhole::create ( { order=>$i, sector_id=>$sector_id, user_id=>0, distance=>$distance } );

    } else {
      print "Creating planet<br>\n";
      ($tmp, $distance) = px_planet::create ( { is_home_planet=>"no", order=>$i, sector_id=>$sector_id, user_id=>0, distance=>$distance } );
    }
  }

  item_add ($tx, "status", "STATUS_OK");
  return;
}



return 1;
#
# vim: ts=4 syntax=perl nowrap
#

