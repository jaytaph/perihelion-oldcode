package errors;

# Global errors
use constant ERR_TRUE                         => (1 == 1);      # This is a true condition
use constant ERR_FALSE                        => (1 == 0);      # This is a false condition

use constant ERR_OK                           => -1;
use constant ERR_UNKNOWN                      => -65535;

# Sql
use constant ERR_ALREADY_CONNECTED            => -100;

# Sector
use constant ERR_ALREADY_EXISTS               => -1000;
use constant ERR_NOTHING_FOUND                => -1001;

# Planets
use constant ERR_INCORRECT_PLANET_TYPE        => -1100;

# Vessels
use constant ERR_CANT_MOVE                    => -1200;
use constant ERR_VESSEL_NOT_MOVING            => -1201;
use constant ERR_NOT_A_BATTLESHIP             => -1202;

# Wormhole
use constant ERR_NO_WORMHOLE                  => -1300;
use constant ERR_ALREADY_STABILIZED           => -1301;
use constant ERR_STILL_STABILIZED             => -1302;

# Preset
use constant ERR_ENTRY_ALREADY_EXISTS         => -1400;

# Messages
use constant ERR_CANT_DELETE_ALLIANCE_MESSAGE => -1500;
use constant ERR_CANT_DELETE_GALAXY_MESSAGE   => -1501;

# Alliance
use constant ERR_NO_SUCH_ALLIANCE             => -1600;

# ===========================================================================================================
# Is_Error()
#
# Description:
#   Returns 1 if the error is an error (ERR_* value except ERR_OK)
#
# ParamList:
#     error     Error code to check
#
# Returns:
#     1      error is an error
#     0      error is not an error
#
sub is_error ($) {
  my $error = shift;

  if ($error < 0 and $error != ERR_OK) { return 1; }
  return 0;
}

# ===========================================================================================================
# Is_Boolean()
#
# Description:
#   Returns 1 if the error is a boolean error (ERR_TRUE or ERR_FALSE)
#
# ParamList:
#     error     Error code to check
#
# Returns:
#     1      error is a boolean
#     0      error is not a boolean
#
sub is_boolean ($) {
  my $error = shift;

  if ($error == ERR_TRUE or $error == ERR_FALSE) { return 1; }
  return 0;
}

# ===========================================================================================================
# Assert ()
#
# Description:
#   Exists when the $expr is not true. If so, it will return stack information for debugging purposes.
#
# ParamList:
#   expr        Expression to assert
#
# Returns:
#   nothing
#
sub assert ($) {
  $expr = shift;
  if ($expr == 1) { return; }

  # We want to know the calling function, not the assert() function, save it in $func
  my ($pkg, $file, $line, $func, $hasargs, $wa, $evaltext, $is_require, $hints, $bitmask) = caller(1);
  my ($pkg, $file, $line, $tmp, $hasargs, $wa, $evaltext, $is_require, $hints, $bitmask) = caller(0);
  print "Assert()\n";
  print "  Package    : $pkg\n";
  print "  File       : $file\n";
  print "  Line       : $line\n";
  print "  Func       : $func\n";
  print "\n";


  print "StackFrame:\n";
  for (my $i=1; $i!=10; $i++) {
    my ($pkg, $file, $line, $func, $hasargs, $wa, $evaltext, $is_require, $hints, $bitmask) = caller($i);

    # If no function name is present, it means the stracktrace has ended, but let's do all steps anyway
    if ($func eq "") { next; }

    # Strip down pathnames
    $file =~ s/.*\\//g;

    # Show it in a decent (padded) way
    $fileplusline = pack("A20", $file." (Line ".$line.")");
    print "  $i: $fileplusline $func \n";
  }
  print "\n";
  exit;
}

# ===========================================================================================================
# Is_CSL ()
#
# Description:
#   Returns 1 if the $value is an comma seperated list
#
# ParamList:
#   value   Value to check
#
# Returns:
#     1     is a csl
#     0     is not a csl
#
sub is_csl ($) {
  my $value = shift;

  # Basicly, everything is a CSL.
  return 1;
}

# ===========================================================================================================
# Is_Value ()
#
# Description:
#   Returns 1 if the $value is an positive or negative integer
#
# ParamList:
#   value   Value to check
#
# Returns:
#     1     is a value
#     0     is not a value
#
sub is_value ($) {
  my $value = shift;

  if ($value eq "") { return 0; }

  return ($value =~ /^-?\d+$/);
}

# ===========================================================================================================
# Is_Empty ()
#
# Description:
#   Returns 1 if the $value is empty. Whitespaces does not count.
#
# ParamList:
#   value   Value to check
#
# Returns:
#     1     empty
#     0     not empty
#
sub is_empty ($) {
  my $value = shift;

  # Remove all spaces
  $value =~ s/ //g;

  if ($value eq "") { return 1; }
  return 0;
}

# ===========================================================================================================
# In_Range ()
#
# Description:
#   Returns 1 if the $value is in between $min and $max. It can do this for integers and strings/chars.
#
# ParamList:
#   value   Value to check
#   min     Minimum value
#   max     Maximum value
#
# Returns:
#     1     in range
#     0     not in range
#
sub in_range ($$$) {
  my $value = shift;
  my $min = shift;
  my $max = shift;

  if (is_empty ($value)) { return 0; }

  # Do a string-check or a integer-check (<= or le)
  if (is_value ($value)) { return ($value >= $min and $value <= $max); }
  return ($value ge $min and $value le $max);
}


# ----------------------------------------------------------------------------------------------------
# Return OK status to calling program
return 1;

#
# vim: ts=4 syntax=perl nowrap
#