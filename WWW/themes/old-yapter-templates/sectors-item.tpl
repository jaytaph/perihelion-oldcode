<!-- sectors-item -->       
 
  <table class="standard" align=center border=1>
    [IF rename_form_visible EQ "true"]
    <form method=post action=/perihelion/sector.php>
    <input type=hidden name=frmid value=3x3fqHbdO%2BuykDBDeePhIq7BXr3vvs1TSh1SRQqgcGAlE13%2FtvOTYmzR%2B3RA1X4RXO8oJvMRr7wm3Hi39ixEobWwehFHcfTibUkEJeG%2BNcAOu%2FZn>
    <tr>
      <th colspan=7>Sector {sector_id}: 
        <input type=hidden name=cmd value=rx3OqGrdKutEkIoXQregIA%2FN9OnxirJo%2FGccPzLY>
        <input type=hidden name=sid value=mR2EqALdGOsYkN1CyexAEUH9YM80kWp%2F>
        <input type=text size=15 maxlength=30 name=ne_name>
        <input name=submit type=submit value="Claim">
        ({sector_coordinate})
      </th>     
    </tr>
    </form>  
    [ELSE]
    <tr>
      <th colspan=7>Sector {sector_id}: {sector_name} ({sector_coordinate})</th>
    </tr>   
    [FI]
    <tr>
      <th>Name</th>
      <th>Class</th>
      <th>Population</th>
      <th>Owned By</th>
      <th>Current Status</th>
      <th>Radius<sup><small>(km)</small></sup></th>
      <th>Distance<sup><small>(*10^6km)</small></sup></th>
    </tr>
    [BLOCK row]
      <tr class="{rowclass}">
        [IF name EQ "Unknown"]
          <td>&nbsp;NOREF{name}&nbsp;</td>
        [ELSE]
          <td>&nbsp;<a href="{name_href}">{name}</a>&nbsp;</td>
        [FI]
        <td>&nbsp;{class}&nbsp;</td>
        <td>&nbsp;{population}&nbsp;</td>
        <td>&nbsp;{owner}&nbsp;</td>
        <td>&nbsp;{status}&nbsp;</td>
        <td>&nbsp;{radius}&nbsp;</td>
        <td>&nbsp;{distance}&nbsp;</td>
      </tr>
    [END row]	
    </table>
  <br>
  <br>
  
<!-- End sectors-item -->
