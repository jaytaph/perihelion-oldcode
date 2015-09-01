use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub do_trade () {
  my ($sth, $traderoute, $vessel, $src_planet, $dst_planet, $wait_tick);

  my $loop_id = px_trade::init_get_all_traderoutes ();
  while ($traderoute = px_trade::get_next_entry ($loop_id)) {
    $vessel     = px_vessel::get_vessel ($traderoute->{vessel_id});
	  $src_planet = px_planet::get_planet ($traderoute->{src_planet_id});
	  $dst_planet = px_planet::get_planet ($traderoute->{dst_planet_id});

    # If a vessel is flying trans-sector, it stops at the border of a sector.
    # In that case, just fly to the planet we need...
    if ($vessel->{status} eq "SPACE") {
	    if ($vessel->{dst_sector_id} == $src_planet->{sector_id}) {
	      print "TODO: At border of source sector, flying to source planet\n";
	    }
	    if ($vessel->{dst_sector_id} == $dst_planet->{sector_id}) {
	      print "TODO: At border of destination sector, flying to destination planet\n";
	    }
  	}


    # Check if we are in orbit, and if so, wait until we can fly again...
    if ($vessel->{status} eq "ORBIT" and
        $vessel->{dst_planet_id} == $traderoute->{src_planet_id}) {

      print "TRADEROUTE SRC_WAIT is: ".$traderoute->{src_wait}." ticks\n";
      print "TRADEROUTE TMP_WAIT is: ".$traderoute->{tmp_wait}." ticks\n";

      # This is the first tick that we are in orbit, unload the ores and load
	    # the new ones.

	    if ($traderoute->{tmp_wait} == 0) {
	      px_comm::init_server ();

        # Unload cargo from vessel
 	      my @orelist = (1,2,3,4,5,6);
	      foreach my $ore (@orelist) {
	        dump_ore ($ore, $traderoute->{vessel_id}, $traderoute->{src_planet_id});
        }
        # Load cargo into vessel
		    my $ores = 0;
	      my @orelist = split (",", $traderoute->{src_ore});
	      foreach my $ore (@orelist) {
	        $ores += store_ore ($ore, $traderoute->{vessel_id}, $traderoute->{src_planet_id});
		      print "We store $ores ores now...\n";
        }
   	    px_comm::fini_server ();

		    # Didn't find any ores to collect, so we should wait a while the next
	    	# time we are on the source planet
		    if ($ores == 0 and $#orelist > 0) {
		      $wait_tick = $traderoute->{src_wait} * 2;
		      if ($wait_tick > 32) { $wait_tick = 32; }
		      print "We have to wait $wait_tick ticks on SRC the next time...\n";
		    } else {
		      $wait_tick = 1;
		      print "SRC is 1 now...\n";
	    	}
	    	px_trade::set_source_wait_ticks ($traderoute->{id}, $wait_tick);
	    } # tmpwait == 0


      # Update the wait ticker
      px_trade::increase_temporary_wait_flag ($traderoute->{id});

      # And fly away if possible
      if ($traderoute->{tmp_wait} >= $traderoute->{src_wait}) {
	  	  print "UP UP AND AWAY TO DEST\n";
        px_trade::reset_temporary_wait_flag ($traderoute->{id});
	      px_comm::init_server ();
        move_to_destination ($traderoute->{vessel_id}, $traderoute->{dst_planet_id});
        px_comm::fini_server ();
		    next;
	    }
	  }  # if orbit on src planet


    # Check if we are in orbit, and if so, wait until we can fly again...
    if ($vessel->{status} eq "ORBIT" and
        $vessel->{dst_planet_id} == $traderoute->{dst_planet_id}) {

      print "TRADEROUTE DST_WAIT is: ".$traderoute->{dst_wait}." ticks\n";
      print "TRADEROUTE TMP_WAIT is: ".$traderoute->{tmp_wait}." ticks\n";


      # This is the first tick that we are in orbit, unload the ores and load
	    # the new ones.
	    if ($traderoute->{tmp_wait} == 0) {
	      px_comm::init_server ();

        # Unload cargo from vessel
 	      my @orelist = (1,2,3,4,5,6);
	      foreach my $ore (@orelist) {
	        dump_ore ($ore, $traderoute->{vessel_id}, $traderoute->{dst_planet_id});
        }
        # Load cargo into vessel
		    my $ores = 0;
	      my @orelist = split (",", $traderoute->{dst_ore});
	      foreach my $ore (@orelist) {
	        $ores += store_ore ($ore, $traderoute->{vessel_id}, $traderoute->{dst_planet_id});
		      print "We store $ores ores now...\n";
        }
   	    px_comm::fini_server ();

		    # Didn't find any ores to collect, so we should wait a while the next
	    	# time we are on the source planet
	     	if ($ores == 0 and $#orelist > 0) {
		      $wait_tick = $traderoute->{dst_wait} * 2;
		      if ($wait_tick > 32) { $wait_tick = 32; }
		      print "We have to wait $wait_tick ticks on DEST the next time...\n";
		    } else {
		      $wait_tick = 1;
		      print "DEST is reset to 1\n";
	  	  }
        px_trade::set_destination_wait_ticks ($traderoute->{id}, $wait_tick);
	    } # tmpwait == 0

      # Update the wait ticker
      px_trade::increase_temporary_wait_flag ($traderoute->{id});

      # And fly away if possible
      if ($traderoute->{tmp_wait} >= $traderoute->{dst_wait}) {
	  	  print "UP UP AND AWAY TO SRC\n";
        px_trade::reset_temporary_wait_flag ($traderoute->{id});
	      px_comm::init_server ();
        move_to_destination ($traderoute->{vessel_id}, $traderoute->{src_planet_id});
        px_comm::fini_server ();
		    next;
	    }
  	} # if orbit on dst planet

  } # loop all traderoutes
  px_trade::fini_loop ($loop_id);
}


sub move_to_destination () {
  my ($vessel_id, $planet_id) = @_;
  my $data;
  my $vessel = px_vessel::get_vessel ($vessel_id);
  $data->{vid} = $vessel_id;
  $data->{did} = $planet_id;
  $data->{uid} = $vessel->{user_id};
  px_comm::send_to_server ("MOVE", $data);
  my $pkg = px_comm::recv_from_server ();
}

sub dump_ore () {
  my ($ore, $vessel_id, $planet_id) = @_;
  my $data;
  $data->{sl} = 'dump';
  $data->{pc} = 100;
  $data->{sp} = $ore;
  $data->{vid} = $vessel_id;
  $data->{pid} = $planet_id;
  px_comm::send_to_server ("CARGO", $data);
  my $pkg = px_comm::recv_from_server ();
}

sub store_ore () {
  my ($ore, $vessel_id, $planet_id) = @_;
  my $data;
  $data->{sl} = 'store';
  $data->{pc} = 100;
  $data->{sp} = $ore;
  $data->{vid} = $vessel_id;
  $data->{pid} = $planet_id;
  px_comm::send_to_server ("CARGO", $data);
  my $pkg = px_comm::recv_from_server ();

  return $pkg->{tons};
}



return 1;
#
# vim: ts=4 syntax=perl nowrap
#