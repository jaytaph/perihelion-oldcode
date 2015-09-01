use strict;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

$main::id_privilege = NONE;

# -------------------------------------------------------------------------
# Description:
#   Sends the ID of the user. This function sets the current database to
#   the correct galaxy. Should do some housekeeping as well like checking
#   if it's authorized or something.
#
# Returns:
#
# -------------------------------------------------------------------------

# -------------------------------------------------------------------------
sub id () {
  my ($rx, $tx) = @_;
  my ($user_id);

  item_add ($tx, "pkg_cmd", "ID");

  # Get parameters from the receive hash
  $user_id = $rx->{uid};

  # Get the user galaxy
  if ($user_id ne "") {
    my $user = px_user::get_perihelion_user ($user_id);
    my $db   = $user->{galaxy_db};
    if ($db eq "") { $db = $px_config::config->{default_db} }
    px_mysql::select_db_by_name ($db);

    # Get the galaxy configuration
    my $sth = px_mysql::query (constants->QUERY_KEEP, "SELECT * FROM ".$db.".c_config ORDER BY priority DESC LIMIT 1");
    $px_config::galaxy = px_mysql::fetchhash ($sth);
    px_mysql::query_finish ($sth);

    # TODO: We must set a flag so we can issue all commands, flag depends on the user off course...
    $main::privilege = NORMAL;
    print "* Setting privilege to NORMAL\n";

    # Check if we got an admin flag. If so, set our privilege a wee bit higher
    my @flags = split (",", $user->{flags});
    foreach my $flag (@flags) {
      if ($flag eq "admin") {
        $main::privilege = UNRESTRICTED;
        print "* Setting privilege to UNRESTRICTED\n";
      }
    }

  } else {
    # Set a flag so we can only issue restricted commands in this session (like: login, eos etc)
    $main::privilege = RESTRICTED;
    print "* Setting privilege to RESTRICTED\n";

  }

  item_add ($tx, "status", "STATUS_OK");
}

return 1;

#
# vim: ts=4 syntax=perl nowrap
#

