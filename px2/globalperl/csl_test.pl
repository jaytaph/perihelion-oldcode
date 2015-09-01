use errors;


$csl = "1,2,3,4,5,6,7,8,9,10,";
print "Is_csl   (".pack ("A20",$csl)."):    ";
print errors::is_csl ($csl)? "Yes": "No";
print "\n";

$csl = "1AFAFASFAS,,,2,3,4,5,6,7,8,9,10,";
print "Is_csl   (".pack ("A20",$csl)."):    ";
print errors::is_csl ($csl)? "Yes": "No";
print "\n";


$csl = "1,2,3,4,5,6,7,8,9,10,";
print "Is_empty (".pack ("A20",$csl)."):    ";
print errors::is_empty ($csl)? "Yes": "No";
print "\n";

$csl = "   ";
print "Is_empty (".pack ("A20",$csl)."):    ";
print errors::is_empty ($csl)? "Yes": "No";
print "\n";

$csl = "   ";
print "Is_value (".pack ("A20",$csl)."):    ";
print errors::is_value ($csl)? "Yes": "No";
print "\n";

$csl = "13";
print "Is_value (".pack ("A20",$csl)."):    ";
print errors::is_value ($csl)? "Yes": "No";
print "\n";

$csl = "-13";
print "Is_value (".pack ("A20",$csl)."):    ";
print errors::is_value ($csl)? "Yes": "No";
print "\n";
