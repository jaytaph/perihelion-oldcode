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
sub do_people () {
  my ($sth, $planet, $born, $died, $pax, $rnd);

  my $user_id = 0;
  my $delta_pax = 0;

  my $loop_id = px_planet::init_get_all_planets_with_population ();
  while ($planet = px_planet::get_next_entry ($loop_id)) {

    # Only do planets, we don't do other anomalies because we use population for other reasons as well,
    # for instance, we cound the fatalities of a blackhole in the planet population counter. (how many
    # people would normally live inside a black hole huh?)
    if (! px_anomaly::is_planet ($planet)) { next; }


    # New user id found, since we order by planet, this means we calc
	  # for planets of a different user. We must set the correct
	  # population for the old user
	  if ($user_id != $planet->{user_id}) {
	    if ($user_id != 0 and $delta_pax != 0) {
	      px_user::update_total_population ($user_id, $delta_pax);
	    }
	    $user_id = $planet->{user_id};
	    $delta_pax = 0;
  	}

    $pax = $planet->{population};

    # This place is already at it's full capacity
	  if ($pax == $planet->{population_capacity}) { next; }

	  # Some people get born
	  $rnd = rand($px_config::config->{h_bornpercentage});
	  $born = int($pax * ($rnd/100)+0.5);


    # And some people die
	  $rnd = rand($px_config::config->{h_diedpercentage});
  	$died = int($pax * ($rnd/100)+0.5);


    # When we have less people, we must make sure that we have a
  	# ongoing population rate...
	  if ($pax < 10000) { $born *= 10; }

    # When we have more people, we must make sure that we have a
  	# stable population rate...
    if ($pax > 1000000) { $born /= 10; }


	  $delta_pax += $born - $died;
	  $pax = $pax + $born - $died;

    # Send a message when we don't have enough room for more people
  	if ($pax > $planet->{population_capacity}) {
	    $pax = $planet->{population_capacity};
	    px_message::create_message (px_message->MSG_USER, $planet->{user_id}, "Planet Ambassador", "Maximum population reached",
                                  "There isn't enough room for new people on planet ".$planet->{name}.". You must build community area's on the planet.",
                                  constants->MESSAGE_PRIO_HIGH, constants->MSG_TYPE_PLANET);
  	}

    # And update it when it has changed
    if ($pax != $planet->{population}) {
	    px_planet::set_population ($planet->{id}, $pax, px_planet::SET_POPULATION_ABS);
	  }
  }

  # Make sure the last user also gets the population right...
  if ($user_id != 0 and $delta_pax != 0) {
    px_user::update_total_population ($user_id, $delta_pax);
  }


  px_planet::fini_loop ($loop_id);
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#