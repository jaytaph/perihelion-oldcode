<?php /* Smarty version 2.6.2, created on 2004-06-06 15:56:29
         compiled from /home/joshua/WWW/themes/Simple/info.tpl */ ?>
<!-- info -->

  <table class="standard" align=center width=60%>
    <tr><th colspan=3>User Information</th></tr>
    <tr><td>&nbsp;Stardate:&nbsp;</td>          <td colspan=2>&nbsp;<?php echo $this->_tpl_vars['stardate']; ?>
&nbsp;</td></tr>
    <tr><td>&nbsp;Credits:&nbsp;</td>           <td colspan=2>&nbsp;<?php echo $this->_tpl_vars['credits']; ?>
&nbsp;</td></tr>
    <tr><td>&nbsp;Population:&nbsp;</td>        <td colspan=2>&nbsp;<?php echo $this->_tpl_vars['population']; ?>
 habitants&nbsp;</td></tr>
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp;Sectors Owned:&nbsp;</td>     <td colspan=2>&nbsp;<?php echo $this->_tpl_vars['sectors_owned']; ?>
 sector(s)&nbsp;</td></tr>
    <tr><td>&nbsp;Planets Owned:&nbsp;</td>     <td colspan=2>&nbsp;<?php echo $this->_tpl_vars['planets_owned']; ?>
 planet(s)&nbsp;</td></tr>
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp;Minable planets:&nbsp;</td>   <td>&nbsp;<?php echo $this->_tpl_vars['minable_count']; ?>
 planet(s)&nbsp;</td>    <td>&nbsp;(<?php echo $this->_tpl_vars['minable_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Habitable planets:&nbsp;</td> <td>&nbsp;<?php echo $this->_tpl_vars['habitable_count']; ?>
 planet(s)&nbsp;</td>  <td>&nbsp;(<?php echo $this->_tpl_vars['habitable_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Unusable planets:&nbsp;</td>  <td>&nbsp;<?php echo $this->_tpl_vars['unusable_count']; ?>
 planet(s)&nbsp;</td>   <td>&nbsp;(<?php echo $this->_tpl_vars['unusable_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Wormholes:&nbsp;</td>         <td>&nbsp;<?php echo $this->_tpl_vars['wormhole_count']; ?>
 wormhole(s)&nbsp;</td> <td>&nbsp;(<?php echo $this->_tpl_vars['wormhole_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Starbases:&nbsp;</td>         <td>&nbsp;<?php echo $this->_tpl_vars['starbase_count']; ?>
 starbase(s)&nbsp;</td> <td>&nbsp;(<?php echo $this->_tpl_vars['starbase_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Other Anomalies:&nbsp;</td>   <td>&nbsp;<?php echo $this->_tpl_vars['anomalie_count']; ?>
 anomalies&nbsp;</td>   <td>&nbsp;(<?php echo $this->_tpl_vars['anomalie_percentage']; ?>
 %)&nbsp;</td></tr>
  </table>
  <br>
  <br>

  <table class="standard" align=center width=60%>
    <tr><th colspan=3>Tactical Information</th></tr>
    <tr><td>&nbsp;Fleet size:&nbsp;</td>        <td colspan=2>&nbsp;<?php echo $this->_tpl_vars['total_vessels']; ?>
 vessels&nbsp;</td></tr>  
    <tr><td>&nbsp;Battleships:&nbsp;</td>       <td>&nbsp;<?php echo $this->_tpl_vars['bvd']; ?>
 vessels&nbsp;</td>  <td>&nbsp;(<?php echo $this->_tpl_vars['bvd_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Tradeships:&nbsp;</td>        <td>&nbsp;<?php echo $this->_tpl_vars['tvd']; ?>
 vessels&nbsp;</td>  <td>&nbsp;(<?php echo $this->_tpl_vars['tvd_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;Exploration ships:&nbsp;</td> <td>&nbsp;<?php echo $this->_tpl_vars['evd']; ?>
 vessels&nbsp;</td>  <td>&nbsp;(<?php echo $this->_tpl_vars['evd_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp;<?php echo $this->_tpl_vars['help_info_weakstrong']; ?>
 Weakest ship:&nbsp;</td>      <td>&nbsp;<a href=<?php echo $this->_tpl_vars['weakship_href']; ?>
><?php echo $this->_tpl_vars['weakship_name']; ?>
</a>&nbsp;</td>         <td>&nbsp;(<?php echo $this->_tpl_vars['weakship_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;<?php echo $this->_tpl_vars['help_info_weakstrong']; ?>
 Strongest ship:&nbsp;</td>    <td>&nbsp;<a href=<?php echo $this->_tpl_vars['strongship_href']; ?>
><?php echo $this->_tpl_vars['strongship_name']; ?>
</a>&nbsp;</td>     <td>&nbsp;(<?php echo $this->_tpl_vars['strongship_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;<?php echo $this->_tpl_vars['help_info_weakstrong']; ?>
 Weakest planet:&nbsp;</td>    <td>&nbsp;<a href=<?php echo $this->_tpl_vars['weakplanet_href']; ?>
><?php echo $this->_tpl_vars['weakplanet_name']; ?>
</a>&nbsp;</td>     <td>&nbsp;(<?php echo $this->_tpl_vars['weakplanet_percentage']; ?>
 %)&nbsp;</td></tr>
    <tr><td>&nbsp;<?php echo $this->_tpl_vars['help_info_weakstrong']; ?>
 Strongest planet:&nbsp;</td>  <td>&nbsp;<a href=<?php echo $this->_tpl_vars['strongplanet_href']; ?>
><?php echo $this->_tpl_vars['strongplanet_name']; ?>
</a>&nbsp;</td> <td>&nbsp;(<?php echo $this->_tpl_vars['strongplanet_percentage']; ?>
 %)&nbsp;</td></tr>
  <table>
  <br>
  <br>

  <table class="standard" align=center width=60%>
    <tr><th colspan=2>Other Information</th></tr>
    <tr><td>&nbsp;Buildings discovered:&nbsp;</td>  <td>&nbsp;<?php echo $this->_tpl_vars['buildings_discovered_percentage']; ?>
 %&nbsp;</td></tr>
    <tr><td>&nbsp;Vessels discovered:&nbsp;</td>    <td>&nbsp;<?php echo $this->_tpl_vars['vessels_discovered_percentage']; ?>
 %&nbsp;</td></tr>
    <tr><td>&nbsp;Inventions discovered:&nbsp;</td> <td>&nbsp;<?php echo $this->_tpl_vars['inventions_discovered_percentage']; ?>
 %&nbsp;</td></tr>
    <tr><td>&nbsp;Maximum impulse speed:&nbsp;</td> <td>&nbsp;<?php echo $this->_tpl_vars['impulse_discovered']; ?>
 %&nbsp;</td></tr>
    <tr><td>&nbsp;Maximum warp speed:&nbsp;</td>    <td>&nbsp;Warp <?php echo $this->_tpl_vars['warp_discovered']; ?>
&nbsp;</td></tr>
  </table>
  <br>
  <br>

  <table class="standard" align=center width=60%>
    <tr><th colspan=2>Global Game Information</th></tr>    
  <?php if ($this->_tpl_vars['heartbeat_status'] == 'offline'): ?>
    <tr><td>&nbsp;Heartbeat server:&nbsp;</td>      <td>&nbsp;<font color=red>System Offline</font>&nbsp;</td></tr>
  <?php else: ?>  
    <tr><td>&nbsp;Heartbeat server:&nbsp;</td>      <td>&nbsp;<font color=green>System Online</font>&nbsp;</td></tr>
    <tr><td>&nbsp;Uptime:&nbsp;</td>                <td>&nbsp;<?php echo $this->_tpl_vars['heartbeat_uptime']; ?>
&nbsp;</td></tr>
    <tr><td>&nbsp;Pulses:&nbsp;</td>                <td>&nbsp;<?php echo $this->_tpl_vars['heartbeat_ticks']; ?>
 ticks&nbsp;</td></tr>
    <tr><td>&nbsp;Pulse time:&nbsp;</td>            <td>&nbsp;<?php echo $this->_tpl_vars['heartbeat_rest']; ?>
 minuts&nbsp;</td></tr>
    <tr><td>&nbsp;Average beat time:&nbsp;</td>     <td>&nbsp;<?php echo $this->_tpl_vars['heartbeat_average']; ?>
 seconds&nbsp;</td></tr>
    <tr><td>&nbsp;Minimum beat time:&nbsp;</td>     <td>&nbsp;<?php echo $this->_tpl_vars['heartbeat_min']; ?>
 seconds&nbsp;</td></tr>
    <tr><td>&nbsp;Maximum beat time:&nbsp;</td>     <td>&nbsp;<?php echo $this->_tpl_vars['heartbeat_max']; ?>
 seconds&nbsp;</td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
  <?php endif; ?>
  <?php if ($this->_tpl_vars['commserver_status'] == 'offline'): ?>
    <tr><td>&nbsp;Communication server:&nbsp;</td>  <td>&nbsp;<font color=red>System Offline</font>&nbsp;</td></tr>    
  <?php else: ?>
    <tr><td>&nbsp;Communication server:&nbsp;</td>  <td>&nbsp;<font color=green>System Online</font>&nbsp;</td></tr>    
    <tr><td>&nbsp;Uptime:&nbsp;</td>                <td>&nbsp;<?php echo $this->_tpl_vars['commserver_uptime']; ?>
&nbsp;</td></tr>
    <tr><td>&nbsp;Processes spawned:&nbsp;</td>     <td>&nbsp;<?php echo $this->_tpl_vars['commserver_spawns']; ?>
 processes&nbsp;</td></tr>
    <tr><td>&nbsp;Commands served:&nbsp;</td>       <td>&nbsp;<?php echo $this->_tpl_vars['commserver_commands']; ?>
 commands&nbsp;</td></tr>
    <tr><td>&nbsp;Status Ok:&nbsp;</td>             <td>&nbsp;<?php echo $this->_tpl_vars['commserver_status_ok']; ?>
 commands&nbsp;</td></tr>
    <tr><td>&nbsp;Status Err:&nbsp;</td>            <td>&nbsp;<?php echo $this->_tpl_vars['commserver_status_err']; ?>
 commands&nbsp;</td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
  <?php endif; ?>
  <?php if ($this->_tpl_vars['mysql_status'] == 'offline'): ?>
    <tr><td>&nbsp;MySQL server:&nbsp;</td>          <td>&nbsp;<font color=red>System Offline</font>&nbsp;</td></tr>
  <?php else: ?>
    <tr><td>&nbsp;MySQL server:&nbsp;</td>          <td>&nbsp;<font color=green>System Online</font>&nbsp;</td></tr> 
    <tr><td>&nbsp;MySQL Uptime:&nbsp;</td>          <td>&nbsp;<?php echo $this->_tpl_vars['mysql_uptime']; ?>
&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Queries:&nbsp;</td>         <td>&nbsp;<?php echo $this->_tpl_vars['mysql_queries']; ?>
 queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Select Queries:&nbsp;</td>  <td>&nbsp;<?php echo $this->_tpl_vars['mysql_select']; ?>
 queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Insert Queries:&nbsp;</td>  <td>&nbsp;<?php echo $this->_tpl_vars['mysql_insert']; ?>
 queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Update Queries:&nbsp;</td>  <td>&nbsp;<?php echo $this->_tpl_vars['mysql_update']; ?>
 queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Bytes received:&nbsp;</td>  <td>&nbsp;<?php echo $this->_tpl_vars['mysql_bytes_received']; ?>
 bytes&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Bytes sent:&nbsp;</td>      <td>&nbsp;<?php echo $this->_tpl_vars['mysql_bytes_sent']; ?>
 bytes&nbsp;</td></tr>
  <?php endif; ?>
  </table>
  <br>
  <br>

<!-- End info -->