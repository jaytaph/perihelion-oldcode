<?php

  $tmp = 1;
  $tmp = create_table ( 2, $tmp);
  $tmp = create_table ( 4, $tmp);
  $tmp = create_table ( 8, $tmp);




function create_table ($size, $offset) {
  echo "<center>\n";
  echo "<table border=1>\n";




#  for ($i = $offset; $i != ($size*$size)+$offset; $i++) {
#    $y = intval ($i / $size);
#    $x = intval ($i % $size);
#    $tmp[$x][$y] = $i;
#  }


  for ($dx=0; $dx!=($size); $dx++) {
    for ($dy=0; $dy!=($size); $dy++) {
      for ($ry=0; $ry!=($size); $ry++) {
        for ($rx=0; $rx!=($size); $rx++) {
          $x = ($dx * 2) + $rx;
          $y = ($rx * 2) + $ry;
          $tmp[$y][$x] = ($dy*8 + $dx*4 + $ry*2 + $rx + 1);
        }
      }
    }
  }


  $offset++;

  for ($y=$DY; $y!=$DY+2; $y++) {
    for ($x=$DX; $x!=$DX+2; $x++) {
      $tmp[$x][$y] = ($y*2)+$x + 1;
    }
  }


  for ($x=0; $x != $size; $x++) {
    echo "<tr>";
    for ($y=0; $y != $size; $y++) {
      echo "<td>&nbsp;".$tmp[$y][$x]."&nbsp;</td>";
    }
    echo "</tr>";
  }

  echo "</table>\n";
  echo "</center>\n";
  echo "<br><br>";
}


?>