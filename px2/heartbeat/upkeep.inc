use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }
use px_ore;
use px_item;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -----------------------------------------------------------------------------------------
sub do_upkeep () {
  do_planet_upkeep ();
  do_vessel_upkeep ();
}

# -----------------------------------------------------------------------------------------
sub do_planet_upkeep () {
  my ($sth, $sth2, $planet);
  my (@ores, $costs);

  # Do all planets.
  my $loop_id = px_planet::init_get_all_owned_planets ();
  while ($planet = px_planet::get_next_entry ($loop_id)) {

    my $user = px_user::get_user ($planet->{user_id});
    my $ores = px_planet::get_ores ($planet->{id});

    # Set the credits
    $user->{credits} -= $planet->{upkeep_costs};
    if ($user->{credits} < 0) {
      # Uh oh.. we need credits here. We don't have enough credits, so we should do something with the
      # buildings. Probably set them inactive or something.
      print "Not enough credits for upkeep of planet ".$planet->{name}."...\n";
      $user->{credits} = 0;
    }
    px_user::set_credits ($planet->{user_id}, $user->{credits}, px_user->SET_CREDITS_ABS);


    # Set the upkeep to the planet
    for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
      my $ore = px_ore::get_ore ($ores->{stock_ores}, $i);
      $ore -= px_ore::get_ore ($planet->{upkeep_ores}, $i);
      if ($ore < 0) {
        print "Not enough ".px_ore::get_ore_name ($i)." on planet (".$planet->{id}.") ".$planet->{name}."\n";
        # upkeep_set_stuff_inactive ();
        $ore = 0;
      }
      $ores->{stock_ores} = px_ore::set_ore ($ores->{stock_ores}, $i, $ore);
    }

    # And update it
    px_planet::update_ores ($planet->{id}, $ores->{cur_ores}, $ores->{stock_ores});
  }

  px_planet::fini_loop ($loop_id);
}


# -----------------------------------------------------------------------------------------
sub do_planet_items_upkeep () {
  my ($sth, $sth2, $i, $tmp, $planet, %items);

  my (@ores, $costs);

  # Do all planets.
  my $loop_id = px_planet::init_get_all_owned_planets ();
  while ($planet = px_planet::get_next_entry ($loop_id)) {
    if (not px_anomaly::is_planet ($planet)) { next; }

    # Set initial upkeep to nothing
    for ($i=0; $i!=px_ore::get_ore_count(); $i++) {
      $ores[$i] = 0;
    }
    $costs = 0;

#    print "Processing planet ".$planet->{name}."\n";
    $tmp = px_planet::get_surface ($planet->{id});
    if ($tmp->{cargo_ids} eq "") { next; }
    if (substr($tmp->{cargo_ids}, -1, 1) eq ",") { chop ($tmp->{cargo_ids}); }

    # Find out how many items we have of each type on the planet
    my %items;
    for $i (split (/,/, $tmp->{cargo_ids})) {
      if (exists $items{$i}) {
        $items{$i}++;
      } else {
        $items{$i} = 1;
      }
    }

    # Now, get all the costs and ore upkeeps from all items times the number of items of it on the planet
    foreach my $idx (keys %items) {
      my $count = $items{$idx};

      # Don't count when it's inactive cargo...
      if (px_item::is_vessel_item ($idx) or px_item::is_weapon ($idx)) { next; }

      $tmp = px_item::get_item ($idx);

      $costs += ($tmp->{upkeep_costs} * $count);
      for ($i=0; $i!=px_ore::get_ore_count(); $i++) {
        $ores[$i] += (px_ore::get_ore ($tmp->{upkeep_ores}, $i) * $count);
      }
    }


    # Set the credits, if possible. If not, set some items to inactive...
    $tmp = px_user::get_user ($planet->{user_id});
    $tmp->{credits} -= $costs;
    if ($tmp->{credits} - $costs < 0) {
      # Uh oh.. we need credits here. We don't have enough credits, so we should do something with the
      # itemss. Probably set them inactive or something.
      print "Not enough credits...";
      $tmp->{credits} = 0;
    }
    px_user::set_credits ($planet->{user_id}, $tmp->{credits}, px_user->SET_CREDITS_ABS);


#    # Uh oh.. we need to check for ores here. If we don't have enough ores, we should do something with the
#    # itemss. Probably set them inactive or something.
#    px_user::set_credits ($planet->{user_id}, $costs, px_user->SET_CREDITS_SUB);


#    print "Upkeep: $costs  - ";
#    for ($i=0; $i!=px_ore::get_ore_count(); $i++) {
#      print " $i:".$ores[$i];
#    }
#    print "\n";
  }
}

# -----------------------------------------------------------------------------------------
sub do_vessel_upkeep () {
  my ($sth, $sth2, $vessel);
  my (@ores, $costs);

  # Do all vessels.
  my $loop_id = px_vessel::init_get_all_vessels ();
  while ($vessel = px_vessel::get_next_entry ($loop_id)) {

    my $user = px_user::get_user ($vessel->{user_id});

    # Set the credits
    $user->{credits} -= $vessel->{upkeep_costs};
    if ($user->{credits} < 0) {
      # Uh oh.. we need credits here. We don't have enough credits, so we should do something with the
      # buildings. Probably set them inactive or something.
      print "Not enough credits for upkeep of vessel ".$vessel->{name}."...\n";
      $user->{credits} = 0;
    }
    px_user::set_credits ($vessel->{user_id}, $user->{credits}, px_user->SET_CREDITS_ABS);
  }
  px_vessel::fini_loop ($loop_id);
}


# -----------------------------------------------------------------------------------------
sub do_vessel_items_upkeep () {
  my ($sth, $sth2, $i, $tmp, $vessel, %items);

  my (@ores, $costs);

  # Do all vessels
  my $loop_id = px_vessel::init_get_all_vessels ();
  while ($vessel = px_vessel::get_next_entry ($loop_id)) {

    # Set initial upkeep to nothing
    for ($i=0; $i!=px_ore::get_ore_count(); $i++) {
      $ores[$i] = 0;
    }
    $costs = 0;

#    print "Processing vessel ".$vessel->{name}."\n";

    $tmp = px_vessel::get_vessel_items ($vessel->{id});
    if ($tmp->{weapon_ids} eq "") { next; }
    if (substr($tmp->{weapon_ids}, -1, 1) eq ",") { chop ($tmp->{weapon_ids}); }

    # Find out how many items we have of each type on the vessel
    my %items;
    for $i (split (/,/, $tmp->{weapon_ids})) {
      if (exists $items{$i}) {
        $items{$i}++;
      } else {
        $items{$i} = 1;
      }
    }

    # Now, get all the costs and ore upkeeps from all items times the number of items of it on the vessel
    foreach my $idx (keys %items) {
      my $count = $items{$idx};

#      # Count when it's inactive cargo...
#      if (px_item::is_weapon ($idx)) { next; }

      $tmp = px_item::get_item ($idx);

      $costs += ($tmp->{upkeep_costs} * $count);
      for ($i=0; $i!=px_ore::get_ore_count(); $i++) {
        $ores[$i] += (px_ore::get_ore ($tmp->{upkeep_ores}, $i) * $count);
      }
    }


    # Set the credits, if possible. If not, set some items to inactive...
    $tmp = px_user::get_user ($vessel->{user_id});

    $tmp->{credits} -= $costs;
    if ($tmp->{credits} - $costs < 0) {
      # Uh oh.. we need credits here. We don't have enough credits, so we should do something with the
      # itemss. Probably set them inactive or something.
      print "Not enough credits...";
      $tmp->{credits} = 0;
    }
    px_user::set_credits ($vessel->{user_id}, $tmp->{credits}, px_user->SET_CREDITS_ABS);


#    # Uh oh.. we need to check for ores here. If we don't have enough ores, we should do something with the
#    # itemss. Probably set them inactive or something.
#    px_user::set_credits ($planet->{user_id}, $costs, px_user->SET_CREDITS_SUB);

#    print "Upkeep: $costs  - ";
#    for ($i=0; $i!=px_ore::get_ore_count(); $i++) {
#      print " $i:".$ores[$i];
#    }
#    print "\n";
  }
}



return 1;
#
# vim: ts=4 syntax=perl nowrap
#