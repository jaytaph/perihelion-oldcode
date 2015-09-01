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
# TODO Ratio is not good. We need to create a floating ratio, otherwise we
#      could give people 7 points when they only have a 50% ratio.. it's
#      not right, but it's ok...
sub do_science () {
  my ($sth2, $user, $building);

  # All users get science...
  my $loop_id = px_user::init_get_all_active_users ();
  while ($user = px_user::get_next_entry ($loop_id)) {

    # ---------------------------------------------------------------
    # Set science levels
  	my $tmp;
    my $old_b = $user->{building_level};
    my $b = $user->{building_level};
    my $b_r = $user->{science_building};
    my $e = $user->{explore_level};
    my $e_r = $user->{science_explore};
    my $v = $user->{vessel_level};
    my $v_r = $user->{science_vessel};
    my $g = $user->{invention_level};
    my $g_r = $user->{science_invention};

	  my $r = $px_config::config->{h_sciencepoints} * ($user->{science_ratio} / 100);

    $tmp = ($b_r / 100) * $r;
    $tmp = int ($tmp+0.5);
    $b = $b + $tmp;
    $tmp = ($e_r / 100) * $r;
    $tmp = int ($tmp+0.5);
    $e = $e + $tmp;
    $tmp = ($v_r / 100) * $r;
    $tmp = int ($tmp+0.5);
    $v = $v + $tmp;
    $tmp = ($g_r / 100) * $r;
    $tmp = int ($tmp+0.5);
    $g = $g + $tmp;

    # If we havent discovered ships yet, we don't use the vessel and explore ratio
    if ($user->{impulse} == 0) {
      $v = 0;
      $v_r = 0;
      $e = 0;
      $e_r = 0;
    }


    # And update it
	  px_mysql::update_science ($user->{user_id}, $v, $g, $b, $e);


    # ---------------------------------------------------------------
    # Find out if we have invented new buildings
    if ($b != $old_b) {
      my $loop_id2 = px_building::init_get_all_buildings_between_building_levels ($b, $old_b);
      while ($building = px_building::get_next_entry ($loop_id2)) {
	      px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Invention", "New building discovered",
                                    "Your scientists have discovered a ".$building->{name}.": ".$building->{description},
                                    constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_INVENTION);
      }
      px_building::fini_loop ($loop_id2);
    }


    # ---------------------------------------------------------------
    # Set the new impulse speed or warp speed if applicable
    my $impulse = $user->{impulse};
    my $warp = $user->{warp};

    # We are still discovering impulse speeds
    if ($impulse > 0 and $impulse != 100) {
   	  $impulse = int ($v / $px_config::config->{impulse_dividor});
	    if ($impulse == 0) { $impulse = 1; };
   	  if ($impulse > 100) { $impulse = 100; }
   	}

   	# We are discovering warp speeds
   	if ($warp > 0 and $warp != 99) {
   	  $warp = int ($v / $px_config::config->{warp_dividor});
	    if ($warp < 10) { $warp = 10; }
   	  if ($warp > 99) { $warp = 99; }
   	}

    # Only set it if things have changed...
    if ($impulse != $user->{impulse} or $warp != $user->{warp}) {

      # Get flags of the user
      my $flags = px_user::get_user_flags ($user->{user_id});

  	  # Finally! We can fly at 100 impulse, and we already have build a spacedock, so we can fly at warp now...
  	  if ($impulse == 100 and $flags->{can_warp} == 1 and $user->{warp} == 0) {
        px_sector::set_sector_as_public ($user->{sector_id});
		    goto_warp ($user->{user_id});
      }

  	  # Send a message every 10,20,30,...100% impulse
  	  if ($warp == 0 and $impulse % 10 == 0 and $impulse != 0 and $impulse != 100) {
	      px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Engineering", "Impulse milestone",
		                                "Your engineers have discovered newer and faster ways to travel. Your ships can now reach speeds of ".$impulse." percent of lightspeed.",
		                                constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_INVENTION);
	    }

  	  # Send a message at every warp factor...
  	  if ($warp != 0 and $warp % 10 == 0) {
  	    px_message::create_message (px_message->MSG_USER, $user->{user_id}, "Engineering", "Warp milestone",
                                    "Your engineers have discovered newer and faster ways to travel. Your ships can now reach speeds of warp factor ".int($warp/10),
                                    constants->MESSAGE_PRIO_LOW, constants->MSG_TYPE_INVENTION);
      }

      px_user::set_speed ($user->{user_id}, $impulse, $warp);
   	}
  }
  px_user::fini_loop ($loop_id);
}



return 1;
#
# vim: ts=4 syntax=perl nowrap
#