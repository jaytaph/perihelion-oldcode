use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::sectorname_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#   Renames a sector name.
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    NAME                Name already exist
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub sectorname () {
  my ($rx, $tx) = @_;
  my ($sector_id, $name);
  my ($user_id, $name);

  item_add ($tx, "pkg_cmd", "SECTOR");

  $sector_id = $rx->{sector_id};
  $user_id = $rx->{user_id};
  $name = $rx->{name};

  # No decent params found...
  if ($sector_id eq "" or $user_id eq "" or $name eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if (px_sector::sector_exists ($name) == errors->ERR_ALREADY_EXISTS) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "NAME");
  } else {
    px_sector::set_owner ($sector_id, $user_id);
    px_sector::set_name ($sector_id, $name);
    item_add ($tx, "status", "STATUS_OK");
  }
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

