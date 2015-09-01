package px_comm;
use strict;
use Socket;
use px_config;


# Return OK status to calling program
return 1;


# -----------------------------------------------------------------
sub init_server () {
  my $iaddr = inet_aton ('127.0.0.1');
  my $paddr = sockaddr_in ($px_config::config->{s_tcpport}, $iaddr);
  socket (SOCK, PF_INET, SOCK_STREAM, getprotobyname ('tcp'));
  connect (SOCK, $paddr) or die ("Cannot open socket!\n");
}

# -----------------------------------------------------------------
sub fini_server () {
  close (SOCK);
}

# -----------------------------------------------------------------
sub send_to_server ($$) {
  my ($command, $hashref) = @_;
  my $s;

  $s = "pkg_cmd==>".$command."\n";
  syswrite (SOCK, $s);

  foreach my $key (keys %$hashref) {
    $s = $key."==>".$hashref->{$key}."\n";
    syswrite (SOCK, $s);
  }
  $s = ".\n";
  syswrite (SOCK, $s);
}

# -----------------------------------------------------------------
sub recv_from_server () {
  my $hash = {};
  my ($key, $val, $buf);

  while (1) {
    $buf = readserverline ();
    if ($buf eq "") { return ""; }
	  chomp ($buf);

	  if ($buf eq ".") { last; }

	  ($key, $val) = split ("==>", $buf);
	  $hash->{$key} = $val;
  }

  return $hash;
}

# -----------------------------------------------------------------
sub readserverline () {
  my ($n, $c, $s);

  $s = "";
  while (1) {
    $n = sysread (SOCK, $c, 1);
    if ($n == 0) { return ""; }
    if ($c eq "\r") { next; }
    if ($c eq "\n") { last; }
    $s .= $c;
  }

  return $s;
}


#
# vim: ts=4 syntax=perl nowrap
#
