use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::science_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#    Sets the ratio and different science levels for a user.
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    100                 Percentages don't add up to 100 percent.
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub science () {
  my ($rx, $tx) = @_;
  my ($user_id, $ratio, $invention, $building, $vessel, $explore);

  item_add ($tx, "pkg_cmd", "SCIENCE");

  $user_id = $rx->{id};
  $ratio = $rx->{ratio};
  $invention = $rx->{invention};
  $building = $rx->{building};
  $vessel = $rx->{vessel};
  $explore = $rx->{explore};

  # No decent params found...
  if ($user_id eq "" or $ratio eq "" or $invention eq "" or $building eq "" or $vessel eq "" or $explore eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if (int($invention) + int($building) + int($vessel) + int($explore) != 100) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "100");
    return;
  }

  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE g_users SET science_ratio=?, science_vessel=?, science_building=?, science_invention=?, science_explore=? WHERE user_id=?", $ratio, $vessel, $building, $invention, $explore, $user_id);
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

