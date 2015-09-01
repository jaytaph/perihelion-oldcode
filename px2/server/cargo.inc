use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::cargo_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub cargo () {
  my ($rx, $tx) = @_;
  my ($user_id, $vessel_id, $planet_id, $sl, $percent, $ore_nr);

  item_add ($tx, "pkg_cmd", "CARGO");

  $planet_id = $rx->{pid};
  $vessel_id = $rx->{vid};
  $sl        = $rx->{sl};
  $percent   = $rx->{pc};
  $ore_nr    = $rx->{sp};

  # No decent params found...
  if ($vessel_id eq "" or $planet_id eq "" or $sl eq "" or $percent eq "" or $ore_nr eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if ($ore_nr > px_ore::get_ore_count()) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if ($percent > 100 or $percent < 0) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }

  if ($sl != "dump" or $sl != "store") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }


  # Unload ores onto the planet
  if ($sl eq "dump") {
  	if ($ore_nr == constants->O_ALL) {
  	  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) { dump_ore ($i, $percent, $vessel_id, $planet_id); }
  	} else {
      dump_ore ($ore_nr, $percent, $vessel_id, $planet_id);
	  }
  }

  # Load ores into vessel
  my $tons = 0;
  if ($sl eq "store") {
	  if ($ore_nr == constants->O_ALL) {
  	  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) { $tons += store_ore ($i, $percent, $vessel_id, $planet_id); }
	  } else {
      $tons += store_ore ($ore_nr, $percent, $vessel_id, $planet_id);
	  }
  }

  item_add ($tx, "tons", $tons);
  item_add ($tx, "status", "STATUS_OK");
  return;
}

# -------------------------------------------------------------------------
# Dump from vessel to the planet
sub dump_ore {
  my ($ore_nr, $percent, $vessel_id, $planet_id) = @_;
  my ($result, $tmp, $vessel_ores, $planet_ores);

  # Get the ores from the vessel into a nice array
  $tmp = px_vessel::get_vessel_items ($vessel_id);
  $vessel_ores = $tmp->{ores};

  $tmp = px_planet::get_ores ($planet_id);
  $planet_ores = $tmp->{stock_ores};

  # Calculate how much ores we need to take out
  $tmp = int (px_ore::get_ore ($vessel_ores, $ore_nr) / 100 * $percent);

  # And remove it from the planet and add it to the vessel
  $vessel_ores = px_ore::set_ore ($vessel_ores, $ore_nr, px_ore::get_ore ($vessel_ores, $ore_nr)-$tmp);
  $planet_ores = px_ore::set_ore ($planet_ores, $ore_nr, px_ore::get_ore ($planet_ores, $ore_nr)+$tmp);

  px_vessel::set_ores ($vessel_id, $vessel_ores);
  px_planet::update_ores ($planet_id, "", $planet_ores);
}

# -------------------------------------------------------------------------
# Store from planet to the vessel
sub store_ore {
  my ($ore_nr, $percent, $vessel_id, $planet_id) = @_;
  my ($result, $tmp, $vessel_ores, $planet_ores);

  # Get the ores from the vessel into a nice array
  $tmp = px_vessel::get_vessel_items ($vessel_id);
  $vessel_ores = $tmp->{ores};

  $tmp = px_planet::get_ores ($planet_id);
  $planet_ores = $tmp->{stock_ores};

  # Calculate how much ores we need to take out
  $tmp = int (px_ore::get_ore ($planet_ores, $ore_nr) / 100 * $percent);

  # We can only load a limited ammount of cargo...
  if ((px_ore::get_ore ($vessel_ores, $ore_nr) + $tmp) > $px_config::config->{s_cargoload_max}) {
    $tmp = $px_config::config->{s_cargoload_max} - px_ore::get_ore ($vessel_ores, $ore_nr);
  }
  # If we can't add anymore ores into the vessel, skip it.
  if ($tmp == 0) { return $tmp; }


  # And remove it from the planet and add it to the vessel
  $vessel_ores = px_ore::set_ore ($vessel_ores, $ore_nr, px_ore::get_ore ($vessel_ores, $ore_nr)+$tmp);
  $planet_ores = px_ore::set_ore ($planet_ores, $ore_nr, px_ore::get_ore ($planet_ores, $ore_nr)-$tmp);

  px_vessel::set_ores ($vessel_id, $vessel_ores);
  px_planet::update_ores ($planet_id, "", $planet_ores);
}

return 1;
#
# vim: ts=4 syntax=perl nowrap
#

