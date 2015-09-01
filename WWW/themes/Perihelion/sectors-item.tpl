<!-- sectors-item -->       
 
  <table class='standard' align='center' border='0'>
{if $rename_form_visible == "true"}
    <form method='post' action='{$SCRIPT_NAME}'>
    <input type='hidden' name='cmd' value='{$cmd}'>
    <input type='hidden' name='frmid' value='{$formid}'>
    <input type='hidden' name='sid' value='{$sid}'>    
    <tr>
      <th colspan='7'>Sector {$sector_id}:         
        <input type='text' size='15' maxlength='30' name='ne_name'>
        <input name='submit' type='submit' value='Claim'>
        ({$sector_coordinate})
      </th>     
    </tr>
    </form>  
{else}
    <tr>
      <th colspan='7'>Sector {$sector_id}: {$sector_name} ({$sector_coordinate})</th>
    </tr>   
{/if}
    <tr>
      <th>Name</th>
      <th>Class</th>
      <th>Population</th>
      <th>Owned By</th>
      <th>Current Status</th>
      <th>Radius<sup><small>(km)</small></sup></th>
      <th>Distance<sup><small>(*10^6km)</small></sup></th>
    </tr>
    
      {section name=row loop=$anomalies}
      <tr class='{cycle values="odd, even"}'>
        {if $anomalies[row].name == "Unknown"}
          <td>&nbsp;{$anomalies[row].name}&nbsp;</td>
        {else}
          <td>&nbsp;<a href="{$anomalies[row].name_href}">{$anomalies[row].name}</a>&nbsp;</td>
        {/if}
        <td class='{$anomalies[row].class_class}'>&nbsp;{$anomalies[row].class}&nbsp;</td>
        <td class='{$anomalies[row].population_class}'>&nbsp;{$anomalies[row].population}&nbsp;</td>
        <td>&nbsp;{$anomalies[row].owner}&nbsp;</td>
        <td>&nbsp;{$anomalies[row].status}&nbsp;</td>
        <td>&nbsp;{$anomalies[row].radius}&nbsp;</td>
        <td>&nbsp;{$anomalies[row].distance}&nbsp;</td>
      </tr>
      {/section}
    </table>
  <br>
  <br>
  
<!-- End sectors-item -->
