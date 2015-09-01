<!-- Inventions -->
 
  <table class="standard" align="center" border="0"> 
  <tr><th colspan=5>Building Discoveries</th></tr>
  {section name=tr loop=$building_text step=5}
  <tr> 
    {section name=td start=$smarty.section.tr.index loop=$smarty.section.tr.index+5} 
      {if $smarty.section.td.index < $building_count } 
        <td>&nbsp;{"<a href='$building_href[td]'><img alt='$building_text[td]' src='$building_img[td]' border='0' width='100' height='100'></a>"}&nbsp;</td>      
      {else}
        <td>&nbsp;</td>      
      {/if}
    {/section} 
  </tr> 
  {/section} 
  <tr><th colspan=5>Discoveries Completed: {$building_discovery_percentage}%</th></tr>
  {if isset($building_next_discovery_percentage) }
  <tr><th colspan=5>Next discovery completion: {$building_next_discovery_percentage}%</th></tr>
  {/if}
  </table>
  <br>
  <br>

  
  <table class="standard" align="center" border="0"> 
  <tr><th colspan=5>Vessel Discoveries</th></tr>
  {section name=tr loop=$vessel_text step=5}
  <tr> 
    {section name=td start=$smarty.section.tr.index loop=$smarty.section.tr.index+5} 
      {if $smarty.section.td.index < $vessel_count } 
        <td>&nbsp;{"<a href='$vessel_href[td]'><img alt='$vessel_text[td]' src='$vessel_img[td]' border='0' width='100' height='100'></a>"}&nbsp;</td>      
      {else}
        <td>&nbsp;</td>      
      {/if}
    {/section} 
  </tr> 
  {/section} 
  <tr><th colspan=5>Discovery Completed: {$vessel_discovery_percentage}%</th></tr>
  {if isset($vessel_next_discovery_percentage) }
  <tr><th colspan=5>Next discovery completion: {$vessel_next_discovery_percentage}%</th></tr>
  {/if}
  </table>
  <br>
  <br>

  
  <table class="standard" align="center" border="0"> 
  <tr><th colspan=5>Item Discoveries</th></tr>
  {section name=tr loop=$item_text step=5}
  <tr> 
    {section name=td start=$smarty.section.tr.index loop=$smarty.section.tr.index+5} 
      {if $smarty.section.td.index < $item_count } 
        <td>&nbsp;{"<a href='$item_href[td]'><img alt='$item_text[td]' src='$item_img[td]' border='0' width='100' height='100'></a>"}&nbsp;</td>
      {else}
        <td>&nbsp;</td>      
      {/if}
    {/section} 
  </tr> 
  {/section} 
  <tr><th colspan=5>Discovery Completed: {$item_discovery_percentage}%</th></tr>
  {if isset($item_next_discovery_percentage) }
  <tr><th colspan=5>Next discovery completion: {$item_next_discovery_percentage}%</th></tr>
  {/if}
  </table>
  


<!-- End Inventions -->
