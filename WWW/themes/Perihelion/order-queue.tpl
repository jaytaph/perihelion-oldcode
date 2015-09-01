<!-- order queue -->

{comment}
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 building|invention|vessel|flight   
   .count             int         Number of items in the queue
   .what[]            string      What is in the queue
   .ticks[]           int         How many ticks left
              
 itemcount  				  int					Total number of items in the queue's
 
-->
{/comment}

{if $itemcount == 0}
  <table class="standard" align=center width=60%>
    <tr><td>There are no current orders</td></tr>
  </table>
{else}

  {if $building.count != 0}
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Building Construction</th></tr>
      {section name='row' loop=$building.what}
      <tr class='{cycle values="odd,even"}'><td>{$building.what[row]}</td><td>{$building.ticks[row]} tick{if $building.ticks[row] != 1}s{/if} left.</td></tr>
      {/section}
    </table>
    <br>
    <br>
  {/if}

  {if $vessel.count != 0}
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Vessel Construction And Upgrading</th></tr>
      {section name='row' loop=$vessel.what}
      <tr class='{cycle values="odd,even"}'><td>{$building.what[row]}</td><td>{$vessel.ticks[row]} tick{if $vessel.ticks[row] != 1}s{/if} left.</td></tr>
      {/section}
    </table>
    <br>
    <br>
  {/if}
  
  {if $item.count != 0}
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Item Construction</th></tr>
      {section name='row' loop=$item.what}
      <tr class='{cycle values="odd,even"}'><td>{$item.what[row]}</td><td>{$item.ticks[row]} tick{if $item.ticks[row] != 1}s{/if} left.</td></tr>
      {/section}
    </table>
    <br>
    <br>
  {/if}  

  {if $flight.count != 0}
    <table class="standard" align=center width=60%>
      <tr><th colspan=2>Spaceship Flightplans</th></tr>
      {section name='row' loop=$flight.what}
      <tr class='{cycle values="odd,even"}'><td>{$flight.what[row]}</td><td>{$flight.ticks[row]} tick{if $flight.ticks[row] != 1}s{/if} left.</td></tr>
      {/section}
    </table>
    <br>
    <br>
  {/if}


{/if}


<!-- End order queue -->
