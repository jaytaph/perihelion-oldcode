package px_ore;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }


my @orenames = ("Xellium", "Vitrea", "Entrium", "Augon", "Marium", "Haligon");

# Return OK status to calling program
return 1;


# ===========================================================================================================
# set_ore ()
#
# Description:
#    Returns the ore $index
#
# ParamList
#    csl       comma seperated list of ores
#    index     index
#    value     new ore value
#
# Returns:
#    csl      new comma sepearated list of ores
sub set_ore {
  my $param = @_[0];

  my (@csl, $str, $index, $value);

  # if we don't use a hash parameterlist, get the first item
  if (ref ($param) ne "HASH") {
    $str   = $_[0];
    $index = $_[1];
    $value = $_[2];
  } else {
    $str   = $param->{csl};
    $index = $param->{index};
    $value = $param->{value};
  }

  if ($index < 0) { return ""; }
  if ($index > get_ore_count()) { return ""; }

  @csl = split (",", $str);
  $csl[$index] = $value;

  $str = join (",", @csl);
  return $str;
}


# ===========================================================================================================
# get_ore_count()
#
# Description:
#    Returns the number of ores used in this galaxy
#
# ParamList
#
# Returns:
#    int       Ore number
#
sub get_ore_count {
  my $param = @_[0];

  return ($#orenames)+1;
}

# ===========================================================================================================
# get_ore_name()
#
# Description:
#    Returns the name of ore $index used in this galaxy
#
# ParamList
#    index     index of ore we need
#
# Returns:
#    string    Ore name
#
sub get_ore_name {
  my $param = @_[0];
  my $index;

  # if we don't use a hash parameterlist, get the first item
  if (ref ($param) ne "HASH") {
    $index = $_[0];
  } else {
    $index = $param->{index};
  }

  if ($index < 0) { return ""; }
  if ($index > get_ore_count()) { return ""; }
  return $orenames[$index];
}

# ===========================================================================================================
# get_ore ()
#
# Description:
#    Returns the ore $index
#
# ParamList
#    csl       comma seperated list of ores
#    index     index
#
# Returns:
#    int    value
#
sub get_ore {
  my $param = @_[0];
  my (@csl, $str, $index);

  # if we don't use a hash parameterlist, get the first item
  if (ref ($param) ne "HASH") {
    $str   = $_[0];
    $index = $_[1];
  } else {
    $str = $param->{csl};
    $index = $param->{index};
  }

  if ($index < 0) { return ""; }
  if ($index > get_ore_count()) { return ""; }

  @csl = split (",", $str);
  return $csl[$index];
}

