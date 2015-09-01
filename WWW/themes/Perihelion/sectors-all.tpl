<!-- sectors-all -->

   {include file="$theme_path/pager.tpl" rowcount=$sectors|@count}   
          
   <table class="standard" align=center>
     <tr>
       <th>{$help_sector_all} ID</th>
       <th>Sector Name</th>
       <th>Qty</th>
       <th>Owner</th>
       <th>Coordinate</th>
       <th>Distance</th>
     </tr>  

    {section name='row' start=$pager_pos max=25 loop=$sectors}
    <tr class="{cycle values="odd,even"}">
      <td>&nbsp;<a href="{$sectors[row].href}">{$sectors[row].id}</a>&nbsp;</td>
      <td>&nbsp;{$sectors[row].name}&nbsp;</td>
      <td>&nbsp;{$sectors[row].qty}&nbsp;</td>
      <td>&nbsp;{$sectors[row].owner}&nbsp;</td>
      <td>&nbsp;{$sectors[row].coordinate}&nbsp;</td>
      <td>&nbsp;{$sectors[row].distance} ly&nbsp;</td>
    </tr>
    {/section}
  </table>
  
  {include file="$theme_path/pager.tpl" rowcount=$sectors|@count}   
  
<!-- End sectors-all -->
