use strict;
use lib '../globalperl/';
use px_ore;
BEGIN { require ('constants.pm'); }

$main::tradecreate_privilege = NORMAL;

# -------------------------------------------------------------------------
# Description:
#
# Returns:
#    PARAMS              Incorrect parameter specified
# -------------------------------------------------------------------------


# -------------------------------------------------------------------------
sub tradecreate () {
  my ($rx, $tx) = @_;
  my ($user_id, $vid, $src_pid, $dst_pid);

  item_add ($tx, "pkg_cmd", "TRADECREATE");

  $vid = $rx->{vid};
  $src_pid = $rx->{src_pid};
  $dst_pid = $rx->{dst_pid};

  # No decent params found...
  if ($vid eq "" or $src_pid eq "" or $dst_pid eq "") {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "PARAMS");
    return;
  }
  # Check if we want a traderoute from and to the same planet
  if ($src_pid == $dst_pid) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "SHORTROUTE");
    return;
  }

  # check if the vessel is already inside a traderoute
  my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM a_trades WHERE vessel_id=?", $vid);
  if ($sth->rows > 0) {
    item_add ($tx, "status", "STATUS_ERR");
	  item_add ($tx, "msg", "INTRADE");
    return;
  }

  my $src_ore = "";
  my $dst_ore = "";
  my $i = px_ore::get_ore_count();
  my $str = px_ore::get_ore_name ( { index => 3 } );
  print "Ore name 3: ".$str."\n";
  print "Ore count: $i \n";

  for (my $i=0; $i!=px_ore::get_ore_count(); $i++) {
    my $tmp1 = "src_ore_$i";
    my $tmp2 = "dst_ore_$i";
    if ($rx->{$tmp1} eq "on") { $src_ore = px_csl::add_to_list ($src_ore, $i); }
    if ($rx->{$tmp2} eq "on") { $dst_ore = px_csl::add_to_list ($dst_ore, $i); }
  }

  print "SRCE_ORES: $src_ore\n";
  print "DEST_ORES: $dst_ore\n";

  px_mysql::query (constants->QUERY_NOKEEP, "INSERT INTO a_trades (vessel_id, src_planet_id, dst_planet_id, src_ore, dst_ore) VALUES (?, ?, ?, ?, ?)", $vid, $src_pid, $dst_pid, $src_ore, $dst_ore);

  item_add ($tx, "status", "STATUS_OK");
  return;
}






return 1;
#
# vim: ts=4 syntax=perl nowrap
#

