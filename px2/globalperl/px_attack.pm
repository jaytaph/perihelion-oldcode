package px_attack;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }


# Return OK status to calling program
return 1;


use constant ATTACK   => 0;
use constant DEFENCE  => 1;
use constant STRENGTH => 2;


use constant A_ATTACK   => 0;
use constant A_DEFENCE  => 1;
use constant A_STRENGTH => 2;
use constant D_ATTACK   => 3;
use constant D_DEFENCE  => 4;
use constant D_STRENGTH => 5;

# ===========================================================================================================
# attack_vessel ()
#
# Description:
#
#
# ParamList
#   none
#
# Returns:
#    ($wins, $losses, $draws, $avg_a_defense, $avg_a_strength, $avg_d_defense, $avg_a_strength)
#
sub sim_attack_vessel ($$$) {
  my $attack_vessel_id = shift;
  my $defense_vessel_id = shift;
  my $battlecount = shift;

  errors::assert (errors::is_value ($attack_vessel_id));
  errors::assert (errors::is_value ($defense_vessel_id));
  errors::assert (errors::is_value ($battlecount));

  my $wins = 0;
  my $losses = 0;
  my $draws = 0;
  my $avg_a_defense = 0;
  my $avg_a_strength = 0;
  my $avg_d_defense = 0;
  my $avg_d_strength = 0;

  for (my $i=0; $i!=$battlecount; $i++) {
    my @attack = attack_vessel ($attack_vessel_id, $defense_vessel_id);

    my @slice = @attack[$#attack-1-6, $#attack-1];
    if ($slice[A_STRENGTH] > 0 and $slice[D_STRENGTH] <= 0) { $wins++ };
    if ($slice[A_STRENGTH] <= 0 and $slice[D_STRENGTH] <= 0) { $draws++ };
    if ($slice[A_STRENGTH] <= 0 and $slice[D_STRENGTH] > 0) { $losses++ };

    $avg_a_defense  += $slice[A_DEFENCE];
    $avg_d_defense  += $slice[D_DEFENCE];
    $avg_a_strength += $slice[A_STRENGTH];
    $avg_d_strength += $slice[D_STRENGTH];
  }

  $avg_a_defense /= $battlecount;
  $avg_d_defense /= $battlecount;
  $avg_a_strength /= $battlecount;
  $avg_d_strength /= $battlecount;

  return ($wins, $losses, $draws, $avg_a_defense, $avg_a_strength, $avg_d_defense, $avg_d_strength);
}

# ===========================================================================================================
# attack_vessel ()
#
# Description:
#
#
# ParamList
#   none
#
# Returns:
#    array ( step1:  V1:A,V1:D,V1:S  -  V2:A,V2:D,V2:S
#            step2:  V1:A,V1:D,V1:S  -  V2:A,V2:D,V2:S
#                ...
#            stepX:  -1,-1,-1
#          )
#
sub attack_vessel ($$) {
  my $attack_vessel_id = shift;
  my $defense_vessel_id = shift;
  errors::assert (errors::is_value ($attack_vessel_id));
  errors::assert (errors::is_value ($defense_vessel_id));

  my @start_a_ads = ( 52,  8,  124);
  my @start_d_ads = (  23,  6, 100);
  my @cur_a_ads = @start_a_ads;
  my @cur_d_ads = @start_d_ads;

  # Initial step
  my @attack = ();
  push (@attack, @start_a_ads, @start_d_ads);

  # Do all steps
  do {
    # Move one step
    $cur_d_ads[DEFENCE] -= ($cur_a_ads[ATTACK] + randperc($cur_a_ads[ATTACK], 25) );
    $cur_a_ads[DEFENCE] -= ($cur_d_ads[ATTACK] + randperc($cur_d_ads[ATTACK], 25) );

    # Decrease strength
    if ($cur_a_ads[DEFENCE] < 0) {
      while ($cur_a_ads[DEFENCE] < $start_a_ads[DEFENCE]) {
        $cur_a_ads[STRENGTH]--;
        $cur_a_ads[DEFENCE] += $start_a_ads[DEFENCE];
      }
    }
    if ($cur_d_ads[DEFENCE] < 0) {
      while ($cur_d_ads[DEFENCE] < $start_d_ads[DEFENCE]) {
        $cur_d_ads[STRENGTH]--;
        $cur_d_ads[DEFENCE] += $start_d_ads[DEFENCE];
      }
    }

#    print "CUR_A_ADS: ".pack("A4",$cur_a_ads[ATTACK]).", ".pack("A4",$cur_a_ads[DEFENCE]).", ".pack("A4",$cur_a_ads[STRENGTH])."\n";
#    print "CUR_D_ADS: ".pack("A4",$cur_d_ads[ATTACK]).", ".pack("A4",$cur_d_ads[DEFENCE]).", ".pack("A4",$cur_d_ads[STRENGTH])."\n";

    # Push this step onto the stack
    push (@attack, @cur_a_ads, @cur_d_ads);

  } while ($cur_a_ads[STRENGTH] > 0 and $cur_d_ads[STRENGTH] > 0);

  # Final step
  push (@attack, -1,-1,-1,-1,-1,-1);
  return @attack;
}


# ====================================================================================
# Returns a random value between X and X+Y%  (100, 10) means something between 90 and 110)
sub randperc ($$) {
  my $value = shift;
  my $percentage = shift;

  my $halfrange =  ( ($value / 100) * $percentage );
  my $range = rand ($halfrange * 2);

  print "RP: $value   - ".int(($value - $halfrange) + $range)."\n";

  return int(($value - $halfrange) + $range);
}