package px_blackhole;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


# ===========================================================================================================
# Move_Into_Blackhole ()
#
# Description:
#   Deletes a vessel since it flew into a blackhole
#
# Paramlist:
#
#    vessel_id        ID of the vessel
#    blackhole_id     ID of the black hole
#
# Returns:
#    ERR_OK   success
#    ERR_*    failure
#
sub move_into_blackhole {
  my $blackhole_id = shift;
  my $vessel_id = shift;
  errors::assert (errors::is_value ($blackhole_id));
  errors::assert (errors::is_value ($vessel_id));

  my $vessel    = px_vessel::get_vessel ($vessel_id);
  if (not px_anomaly::is_blackhole ($blackhole_id)) { return errors->ERR_INCORRECT_PLANET_TYPE; }

  # Remove the vessel and increase the fatality counter of the blackhole (for which we use the population field)
  px_vessel::remove ($vessel_id);
  px_blackhole::increase_fatality ($blackhole_id);

	px_message::create_message (px_message->MSG_USER, $vessel->{user_id}, "Fleet commander", "SHIP LOST!",
	                            "All communications to vessel ".$vessel->{name}." are lost. A destress call was received moments before they encountererd a blackhole. The ship is lost due to the massive gravity of the blackhole.",
	                            constants->MESSAGE_PRIO_HIGH, constants->MSG_TYPE_VESSEL);
  return errors->ERR_OK;
}


# ===========================================================================================================
# Increase_Fatality ()
#
# Description:
#     Increases the fatality counter of a blackhole by one
#
# Paramlist:
#     blackhole_id     ID of the black hole
#
# Returns:
#    ERR_OK   success
#    ERR_*    failure
#
sub increase_fatality ($) {
  my $blackhole_id = shift;
  errors::assert (errors::is_value ($blackhole_id));

  px_planet::set_population ($blackhole_id, 1, px_planet->SET_POPULATION_ADD);
  return errors->ERR_OK;
}