package px_csl;
use strict;
use constants;
use lib '../globalperl/';
BEGIN { require ('constants.pm'); }

# Return OK status to calling program
return 1;


# =============================================================================
# Add $id to the end of Comma Seperated List $list
#
# Returns $list
#
sub add_to_list ($$) {
  my ($list, $id) = @_;
  if ($id eq "") { return $list; }
  return $list."$id,";
}

# =============================================================================
# Removes $id from the Comma Seperated List $list
#
# Returns $list
#
sub remove_from_list ($$) {
  my ($list, $id) = @_;
  if ($id eq "") { return $list; }
  $list =~ s/$id\,//;
  return $list;
}

# =============================================================================
# Checks if $id exists in the Comma Seperated List $list
#
# Returns 1 is exists, or 0 if not
#
sub in_list ($$) {
  my ($list, $id) = @_;
  if (grep (/,$id,/, $list) or
      grep (/^$id,/, $list)) {
    return 1;
  }
  return 0;
}

# =============================================================================
# Counts items in the Comma Seperated List $list
#
# Returns number of items in the list $list
#
sub count_list ($) {
  my $count = () = $_[0] =~ /(\d+),/g;
  return $count;
}

# =============================================================================
# Counts how many times $id exists in the Comma Seperated List $list
#
# Returns the count of the items
#
sub count_items ($$) {
  my ($list, $id) = @_;

  # We need to put an comma at the end and the start of the CSL otherwise we
  # can't find the first and the last item of the CSL.
  #
  # Regex counts does not do ,11,11, because it matches the first ,11,
  # but not the second ,11, because the first comma in the second match
  # is basicly the second comma in the first match. We can prevent this
  # by changes all comma's into 2 comma's
  $list =~ s /,/,,/g;
  $list = "," . $list , ",";

  # We need to find ,1,  not 1,  or just 1  because we could match 11, as well
  my $tmp = ",".$id.",";

  #
  # A list like 1,2,2,3, would now look like: ,1,,2,,2,,3,
  #
  # Now we can look without worrying for example the ,2, which matches 2 times
  #
  my $count = () = $list =~ /$tmp/g;

  return $count;
}


# =============================================================================
# Returns random $item in the Comma Seperated List $list
#
# Returns $item
#
sub get_random_item ($) {
  my ($list, $id) = @_;

  my $count = count_list ($list);
  my $idx = rand ($count);
  return get_item ($list, $idx);
}


# =============================================================================
# Returns item $idx from the Comma Seperated List $list
#
# Returns item
#
sub get_item ($$) {
	my ($list, $idx) = @_;
	my @csl_array = split (",", $list);

  # Todo: check for idx in range
  return $csl_array[$idx];
}


return 1;
#
# vim: ts=4 syntax=perl nowrap
#