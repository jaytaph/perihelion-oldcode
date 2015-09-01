<!-- conview.tpl -->

   <table class='standard' align='center' border='0'>
     <tr><th colspan=6>Sector {$sector_id}: {$sector_name}</th></tr>

   {section name='row' loop=$planets}
     <tr class='{cycle values="odd,even"}'>
       <td>&nbsp;{$planets[row].name}&nbsp;</td>
       <td>&nbsp;<a href='{$planets[row].href}'>{$planets[row].viewstring}</a>&nbsp;</td>


     {assign var="cnt" value=$planets[row].href_array|@count}
     {if $cnt > 0}
       {section name='row2' loop=$planets[row].href_array}
          {if $planets[row].href_array[row2].str == ""}
            <td>&nbsp;</td>
          {else}
            <td>&nbsp;<a href='{$planets[row].href_array[row2].href}'>{$planets[row].href_array[row2].str}</a>&nbsp;</td>
          {/if}
       {/section}
     {/if}

     </tr>
   {/section}


  </table>
  <br>
  <br>

<!-- End conview.tpl -->
