use strict;
use lib '../globalperl/';

$main::simattack_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
#
# Returns:
#    PARAMS              Incorrect parameter specified
#    TYPE                Don't know if it's a planet or a vessel
# -------------------------------------------------------------------------


use constant ID_UNKNOWN => 0;
use constant ID_VESSEL  => 1;
use constant ID_PLANET  => 2;

# -------------------------------------------------------------------------
sub simattack () {
  my ($rx, $tx) = @_;

  my ($attack_id_type, $defense_id_type);
  my ($attack_id, $defense_id, $battlecount);
  my @attack;

  item_add ($tx, "pkg_cmd", "SIMATTACK");

  # Get parameters from the receive hash
  $attack_id   = $rx->{attack_id};
  $defense_id  = $rx->{defense_id};
  $battlecount = $rx->{battlecount};

  # No decent params found...
  if ($attack_id eq "" or $defense_id eq "" or $battlecount eq "") {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "PARAMS");
    return;
  }

  # Get the type of ID, is it a vessel or a planet
  my $attack_id_type = ID_UNKNOWN;
  if ($attack_id =~ /^V/i) {
    $attack_id_type = ID_VESSEL;
	  $attack_id =~ s/V//;
  }
  if ($attack_id =~ /^P/i) {
    $attack_id_type = ID_PLANET;
	  $attack_id =~ s/P//;
  }

  my $defense_id_type = ID_UNKNOWN;
  if ($defense_id =~ /^V/i) {
    $defense_id_type = ID_VESSEL;
	  $defense_id =~ s/V//;
  }
  if ($defense_id =~ /^P/i) {
    $defense_id_type = ID_PLANET;
	  $defense_id =~ s/P//;
  }

  if ($defense_id_type == ID_UNKNOWN or $attack_id_type == ID_UNKNOWN) {
    item_add ($tx, "status", "STATUS_ERR");
    item_add ($tx, "msg", "TYPE");
    return;
  }

  # Do the simulation
  if ($attack_id_type == ID_VESSEL and $defense_id_type == ID_VESSEL) {
    @attack = px_attack::sim_attack_vessel ($attack_id, $defense_id, $battlecount);
  }
#  if ($attack_id_type == ID_VESSEL and $defense_id_type == ID_PLANET) {
#    @attack = px_attack::sim_attack_vessel ($attack_id, $defense_id, $battlecount);
#  }
#  if ($attack_id_type == ID_PLANET and $defense_id_type == ID_VESSEL) {
#    @attack = px_attack::sim_attack_vessel ($attack_id, $defense_id, $battlecount);
#  }
#  if ($attack_id_type == ID_PLANET and $defense_id_type == ID_PLANET) {
#    @attack = px_attack::sim_attack_vessel ($attack_id, $defense_id, $battlecount);
#  }

  my ($wins, $losses, $draws, $avg_a_defense, $avg_a_strength, $avg_d_defense, $avg_d_strength) = @attack;
  my $recommendation = "Kijk maar even...";

  item_add ($tx, "recommendation", $recommendation);
  item_add ($tx, "battlecount", $battlecount);
  item_add ($tx, "wins", $wins);
  item_add ($tx, "losses", $losses);
  item_add ($tx, "draws", $draws);
  item_add ($tx, "avg_a_defense", $avg_a_defense);
  item_add ($tx, "avg_a_strength", $avg_a_strength);
  item_add ($tx, "avg_d_defense", $avg_d_defense);
  item_add ($tx, "avg_d_strength", $avg_d_strength);

  item_add ($tx, "status", "STATUS_OK");
  return;
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#

