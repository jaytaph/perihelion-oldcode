use strict;
use lib '../globalperl/';

$main::attack_privilege = NORMAL;

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
sub attack () {
  my ($rx, $tx) = @_;

  my ($attack_id_type, $defense_id_type);
  my ($attack_id, $defense_id);
  my @attack;

  item_add ($tx, "pkg_cmd", "ATTACK");

  # Get parameters from the receive hash
  $attack_id = $rx->{attack_id};
  $defense_id = $rx->{defense_id};

  # No decent params found...
  if ($attack_id eq "" or $defense_id eq "") {
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

  print "Attack  : $attack_id_type\n";
  print "Defence : $defense_id_type\n";

  if ($attack_id_type == ID_VESSEL and $defense_id_type == ID_VESSEL) {
    @attack = px_attack::attack_vessel ($attack_id, $defense_id);
  }
#  if ($attack_id_type == ID_VESSEL and $defense_id_type == ID_PLANET) {
#    @attack = px_attack::attack_vessel ($attack_id, $defense_id);
#  }
#  if ($attack_id_type == ID_PLANET and $defense_id_type == ID_VESSEL) {
#    @attack = px_attack::attack_vessel ($attack_id, $defense_id);
#  }
#  if ($attack_id_type == ID_PLANET and $defense_id_type == ID_PLANET) {
#    @attack = px_attack::attack_vessel ($attack_id, $defense_id);
#  }

  print "Attack Size: ".$#attack." \n";
  my $stepcount = (($#attack + 1) / 6 );

  # Do all attack steps and place it into the transmission array
  for my $step (0..$stepcount - 1) {
    my ($a_a, $a_d, $a_s, $d_a, $d_d, $d_s) = @attack[($step*6)..(($step+1)*6)];

    print "Step ".($step+1)." :  A: $a_a  D: $a_d  S: $a_s   - A: $d_a  D: $d_d  S: $d_s  \n";
    item_add ($tx, "step".($step+1), "$a_a,$a_d,$a_s,$d_a,$d_d,$d_s");
  }

  # Send the number of steps we have
  item_add ($tx, "steps", $stepcount);

  item_add ($tx, "status", "STATUS_OK");
  return;
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#

