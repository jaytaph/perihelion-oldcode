use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#   Decreases all wormhole counters and jumps to a new location if needed.
#
#   Do all wormholes:
#     Check if it's stabilized, if so, do next wormhole
#     Decrease wormhole counter
#     If it's zero, jump to a new location
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
#
sub do_wormholes () {

  my $loop_id = px_wormhole::init_get_all_wormholes ();
  while (my $wormhole = px_wormhole::get_next_entry ($loop_id)) {
    my $worminfo = px_wormhole::get_wormhole ($wormhole->{id});
    if (errors->is_error ($worminfo) and $worminfo == errors->ERR_NO_WORMHOLE) { next; }

    if (px_wormhole::is_stabilized ($worminfo) == errors->ERR_ALREADY_STABILIZED) { next; }

    px_wormhole::decrease_tick ($worminfo);

    if (px_wormhole::time_for_jump ($worminfo)) { px_wormhole::jump ($worminfo); }
  }
  px_wormhole::fini_loop ($loop_id);
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#