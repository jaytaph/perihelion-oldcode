use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# -------------------------------------------------------------------------
# Description:
#   Increases the current stardate with 0.1 tick
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
# Increase the stardate by .1
sub do_stardate () {
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_stardate SET stardate = stardate + 0.1");
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#