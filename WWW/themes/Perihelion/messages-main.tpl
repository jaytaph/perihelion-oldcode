<!-- messages-main -->  

{comment}
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 global|alien|planet|exploration|invention|fleet
   .href							url					link to the message box
   .low								int					number of low priority messages inside the mailbox
   .high							int					number of high priority messages inside the mailbox
   .lasttopic					string			subject of the last message inside the mailbox
              
 show_galaxy  				0|1					wether or not galaxy messages are available
 galaxy
       .href					url					link to the messagebox
       .count					int					number of messages inside the mailbox       
       .hrefsend			url					link to send an item 
 
 show_alliance				0|1					wether or not alliance messages are available
 alliance
       .href					url					link to the messagebox
       .count					int					number of messages inside the mailbox       
       .hrefsend			url					link to send an item  
-->
{/comment}     
 
  <table class='standard' align='center'>
    <tr><th>&nbsp;</th>      <th>&nbsp;Low Priority&nbsp;</th><th>&nbsp;High Priority&nbsp;</th><th>&nbsp;Last Topic In Message Box&nbsp;</th></tr>
    <tr>
      <td>&nbsp;Box: <a href={$global.href}>Global Messages</a>&nbsp;</td>
      <td>&nbsp;{$global.low}&nbsp;</td>
      <td>&nbsp;{$global.high}&nbsp;</td>
      <td>&nbsp;{$global.lasttopic|truncate:40}&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href={$alien.href}>Alien Communication</a>&nbsp;</td>
      <td>&nbsp;{$alien.low}&nbsp;</td>
      <td>&nbsp;{$alien.high}&nbsp;</td>
      <td>&nbsp;{$alien.lasttopic|truncate:40}&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href={$planet.href}>Planet Affairs</a>&nbsp;</td>
      <td>&nbsp;{$planet.low}&nbsp;</td>
      <td>&nbsp;{$planet.high}&nbsp;</td>
      <td>&nbsp;{$planet.lasttopic|truncate:40}&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href={$exploration.href}>Exploration Messages</a>&nbsp;</td>
      <td>&nbsp;{$exploration.low}&nbsp;</td>
      <td>&nbsp;{$exploration.high}&nbsp;</td>
      <td>&nbsp;{$exploration.lasttopic|truncate:40}&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href={$invention.href}>Invention Messages</a>&nbsp;</td>
      <td>&nbsp;{$invention.low}&nbsp;</td>
      <td>&nbsp;{$invention.high}&nbsp;</td>
      <td>&nbsp;{$invention.lasttopic|truncate:40}&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;Box: <a href={$fleet.href}>Fleet Messages</a>&nbsp;</td>
      <td>&nbsp;{$fleet.low}&nbsp;</td>
      <td>&nbsp;{$fleet.high}&nbsp;</td>
      <td>&nbsp;{$fleet.lasttopic|truncate:40}&nbsp;</td>
    </tr>
    <tr><td colspan=4>&nbsp;</td></tr>

  {if $show_galaxy eq "1"}    
    <tr>
      <td colspan=2>&nbsp;Box: <a href={$galaxy.href}>Intercepted Galaxy Messages</a>&nbsp;</td>
      <td>&nbsp;{$galaxy.count}&nbsp;</td>
      <td>&nbsp;<a href={$galaxy.hrefsend}>Send message into outer space</a>&nbsp;</td>
    </tr>
  {/if}

  {if $show_alliance eq "1"}    
    <tr>
      <td colspan=2>&nbsp;Box: <a href={$alliance.href}>Alliance Messages</a>&nbsp;</td>
      <td>&nbsp;{$alliance.count}&nbsp;</td>
      <td>&nbsp;<a href={$alliance.hrefsend}>Send message to alliance</a>&nbsp;</td>
    </tr>
  {/if}
    
  </table>
  
<!-- End sectors-item -->
