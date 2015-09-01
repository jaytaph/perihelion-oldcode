#!/usr/bin/perl
#
use lib '../globalperl/';
use strict;
use English;
use Socket;
use px_mysql;			# Mysql connection routines
use px_config;	  # Standard configuration file
use px_comm;      # Server communication routines
use Time::HiRes;
use POSIX;

use px_ef_building;   # Execute functions (Tick_0 etc)
use px_ef_item;
use px_ef_vessel;

use px_blackhole;     # Standard 'object' functions
use px_building;
use px_csl;
use px_nebula;
use px_planet;
use px_sector;
use px_trade;
use px_user;
use px_vessel;
use px_wormhole;


BEGIN { require ('constants.pm'); }

# Global database handle, each process gets it's own one..
my $dbhandle = 0;

# =========================================================
# Server only serves this galaxy
px_config::set_galaxy ("Galaxy_001");


# Check our OS and load the include files
my @commands;
my $OS_win = ($OSNAME eq "MSWin32") ? 1 : 0;

print "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
print "Operating System Information\n";
print "  OS Name      : $OSNAME\n";
print "  Perl version : $]\n";
print "  Started on   : $BASETIME\n";
print "\n";


px_mysql::set_debug (0);

# Setup Signal Handlers
$SIG{'HUP'}  = 'heartbeat_shutdown';	# HUP signal (1) shuts down daemon correctly.
$SIG{'TERM'} = 'heartbeat_shutdown';	# TERM signal (15) also shuts down.
$SIG{'INT'}  = 'heartbeat_shutdown';	# INT signal (2) also shuts down.
$SIG{'PIPE'} = 'IGNORE';	            # Ignore error from reading/writing to a failed

# And randomize
srand ();


# Read config, this will also set up a configuration read every x minuts
$px_config::current_galaxy = "galaxy_001";
px_config::readconfig ();

$dbhandle = $px_mysql::dbhandle;
$dbhandle = $px_mysql::create_dbhandle;


# Load include files
if ($OS_win) {
  load_win_incs ();
} else {
  load_unix_incs ();
}


print "\n\n";
print "Starting heartbeat server with a pulse of ".$px_config::config->{h_heartbeat}." seconds.\n";

# Set the tick counter for this galaxy/heartbeat to 0
px_mysql::query (constants->QUERY_NOKEEP, "UPDATE h_info SET avg_tick=NULL, min_tick=9999999, max_tick=0, ticks=0, uptime=UNIX_TIMESTAMP(), rest=".$px_config::config->{h_heartbeat});

my $total_time = 0;

my $pulse = 0;
for (;;) {

  # Don't select 10 seconds, but 10 times 1 second so we can break easily from the heartbeat server.. Windows :(...
  my $i = 0;
  do {
    $| = 1;
    select (undef, undef, undef, 1);
    $i++;
    print "$i\r";
  } while ($i < $px_config::config->{h_heartbeat});

  # Get starting time
  my $t0 = Time::HiRes::gettimeofday;

  # Pulse
  $pulse++;

  # Update our counter
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE h_info SET ticks=ticks+1");


  # Innodb or bdb can use transactions so we can rollback in case
  # of an emergency. These lines do nothing on the standard myIsam's.
  px_mysql::query (constants->QUERY_NOKEEP, "SET autocommit=0");
  px_mysql::query (constants->QUERY_NOKEEP, "BEGIN");
  px_mysql::query (constants->QUERY_NOKEEP, "USE ".$px_config::current_galaxy);


  # Heartbeat...
  foreach my $cmd (@commands) {
    my $tmp = "do_".$cmd;
    print "Executing $tmp\n";

    $tmp = \&{$tmp};
    &$tmp ();
  }

  # Commit all changes to the database (Innodb and bdb only)
  px_mysql::query (constants->QUERY_NOKEEP, "COMMIT");

  # Show us how long everything took...
  my $t1 = Time::HiRes::gettimeofday;
  my $pulse_time = $t1 - $t0;
  printf "* (%d) Tick done in %.4f seconds\n", $pulse, $pulse_time;

  # Add this pulse time to a total counter
  $total_time = $total_time + $pulse_time;

  # Update counter
  px_mysql::query (constants->QUERY_NOKEEP, "UPDATE h_info SET max_tick = GREATEST(max_tick, ".$pulse_time.")," .
                                                              "min_tick = LEAST(min_tick, ".$pulse_time.")," .
                                                              "avg_tick = ".($total_time / $pulse));

}

# And close the database handle again
px_mysql::close_dbhandle ();

# End program
heartbeat_shutdown ("Normal Shutdown()");
exit;


##############################################################################
# SUB ROUTINES
##############################################################################

# ================================================================================
# Shuts down the program with an additional error message
sub heartbeat_shutdown () {
  my ($msg) = @_;

  if ($msg) { print ("\nSHUTDOWN> $msg\n"); }
  if ($dbhandle != 0) { px_mysql::disconnect ($dbhandle); }
  exit;
}

# ================================================================================
sub load_win_incs () {
	print "Incorperated functions: \n";

	# Get all *.inc files, these are commands and their main command must be
	# the <filename> (without .inc)
	my @files = `dir /b *.inc`;
	foreach my $file (@files) {
  		chomp ($file);
  		$file =~ s/\.inc//;
  		require ($file.".inc");
  		push @commands, $file;
  		print pack ("A20", "   ".$file);
 	}
	print "\n\n";
}

# ================================================================================
sub load_unix_incs () {
	print "Incorperated functions: \n";

	# Get all *.inc files, these are commands and their main command must be
	# the <filename> (without .inc)
	my @files = `ls -1 *.inc`;
	foreach my $file (@files) {
    chomp ($file);
  		$file =~ s/\.inc//;
  		require ($file.".inc");
  		push @commands, $file;
  		print pack ("A20", "   ".$file);
	}
	print "\n\n";
}


# ================================================================================
#
# vim: ts=4 syntax=perl nowrap
#