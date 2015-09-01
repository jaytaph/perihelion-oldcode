use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#   TODO: change this function. We should do it planet by planet, not user
#         by user.
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
# Increase all players credit counts depending on the tax/science ratio
sub do_credits () {
  my ($user, $credits);

  my $loop_id = px_user::init_get_all_active_users ();
  while ($user = px_user::get_next_entry ($loop_id)) {

    # If science_ration == 100, then we don't have any credits
    if ($user->{science_ratio} != 100) {
      $credits = $user->{population} / (100 / (100-$user->{science_ratio}));
	    $credits = $credits / $px_config::config->{h_credits_dividor};

	    # This is a quick hack that lets users with low population get more money
  	  # than users with a high population.
  	  if ($user->{population} != 0) {
  	    if ($user->{population} < 10000) {
	        $credits += 10000 / $user->{population};
	      }
	    }

	    px_user::set_credits ($user->{id}, $credits, px_user->SET_CREDITS_ADD);
	  }
  }
  px_user::fini_loop ($loop_id);
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#