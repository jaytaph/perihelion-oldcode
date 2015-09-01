<!-- info -->

  <table class="standard" align=center width=60%>
    <tr><th colspan=3>User Information</th></tr>
    <tr><td>&nbsp;Stardate:&nbsp;</td>          <td colspan=2>&nbsp;{$stardate}&nbsp;</td></tr>
    <tr><td>&nbsp;Credits:&nbsp;</td>           <td colspan=2>&nbsp;{$credits}&nbsp;</td></tr>
    <tr><td>&nbsp;Population:&nbsp;</td>        <td colspan=2>&nbsp;{$population} habitants&nbsp;</td></tr>
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp;Sectors Owned:&nbsp;</td>     <td colspan=2>&nbsp;{$sectors_owned} sector(s)&nbsp;</td></tr>
    <tr><td>&nbsp;Planets Owned:&nbsp;</td>     <td colspan=2>&nbsp;{$planets_owned} planet(s)&nbsp;</td></tr>
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp;Minable planets:&nbsp;</td>   <td>&nbsp;{$minable_count} planet(s)&nbsp;</td>    <td>&nbsp;({$minable_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Habitable planets:&nbsp;</td> <td>&nbsp;{$habitable_count} planet(s)&nbsp;</td>  <td>&nbsp;({$habitable_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Unusable planets:&nbsp;</td>  <td>&nbsp;{$unusable_count} planet(s)&nbsp;</td>   <td>&nbsp;({$unusable_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Wormholes:&nbsp;</td>         <td>&nbsp;{$wormhole_count} wormhole(s)&nbsp;</td> <td>&nbsp;({$wormhole_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Starbases:&nbsp;</td>         <td>&nbsp;{$starbase_count} starbase(s)&nbsp;</td> <td>&nbsp;({$starbase_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Other Anomalies:&nbsp;</td>   <td>&nbsp;{$anomalie_count} anomalies&nbsp;</td>   <td>&nbsp;({$anomalie_percentage} %)&nbsp;</td></tr>
  </table>
  <br>
  <br>

  <table class="standard" align=center width=60%>
    <tr><th colspan=3>Tactical Information</th></tr>
    <tr><td>&nbsp;Fleet size:&nbsp;</td>        <td colspan=2>&nbsp;{$total_vessels} vessels&nbsp;</td></tr>  
    <tr><td>&nbsp;Battleships:&nbsp;</td>       <td>&nbsp;{$bvd} vessels&nbsp;</td>  <td>&nbsp;({$bvd_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Tradeships:&nbsp;</td>        <td>&nbsp;{$tvd} vessels&nbsp;</td>  <td>&nbsp;({$tvd_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;Exploration ships:&nbsp;</td> <td>&nbsp;{$evd} vessels&nbsp;</td>  <td>&nbsp;({$evd_percentage} %)&nbsp;</td></tr>
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp;{$help_info_weakstrong} Weakest ship:&nbsp;</td>      <td>&nbsp;<a href={$weakship_href}>{$weakship_name}</a>&nbsp;</td>         <td>&nbsp;({$weakship_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;{$help_info_weakstrong} Strongest ship:&nbsp;</td>    <td>&nbsp;<a href={$strongship_href}>{$strongship_name}</a>&nbsp;</td>     <td>&nbsp;({$strongship_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;{$help_info_weakstrong} Weakest planet:&nbsp;</td>    <td>&nbsp;<a href={$weakplanet_href}>{$weakplanet_name}</a>&nbsp;</td>     <td>&nbsp;({$weakplanet_percentage} %)&nbsp;</td></tr>
    <tr><td>&nbsp;{$help_info_weakstrong} Strongest planet:&nbsp;</td>  <td>&nbsp;<a href={$strongplanet_href}>{$strongplanet_name}</a>&nbsp;</td> <td>&nbsp;({$strongplanet_percentage} %)&nbsp;</td></tr>
  <table>
  <br>
  <br>

  <table class="standard" align=center width=60%>
    <tr><th colspan=2>Other Information</th></tr>
    <tr><td>&nbsp;Buildings discovered:&nbsp;</td>  <td>&nbsp;{$buildings_discovered_percentage} %&nbsp;</td></tr>
    <tr><td>&nbsp;Vessels discovered:&nbsp;</td>    <td>&nbsp;{$vessels_discovered_percentage} %&nbsp;</td></tr>
    <tr><td>&nbsp;Inventions discovered:&nbsp;</td> <td>&nbsp;{$inventions_discovered_percentage} %&nbsp;</td></tr>
    <tr><td>&nbsp;Maximum impulse speed:&nbsp;</td> <td>&nbsp;{$impulse_discovered} %&nbsp;</td></tr>
    <tr><td>&nbsp;Maximum warp speed:&nbsp;</td>    <td>&nbsp;Warp {$warp_discovered}&nbsp;</td></tr>
  </table>
  <br>
  <br>

  <table class="standard" align=center width=60%>
    <tr><th colspan=2>Global Game Information</th></tr>    
  {if $heartbeat_status eq "offline" }
    <tr><td>&nbsp;Heartbeat server:&nbsp;</td>      <td>&nbsp;<font color=red>System Offline</font>&nbsp;</td></tr>
  {else}  
    <tr><td>&nbsp;Heartbeat server:&nbsp;</td>      <td>&nbsp;<font color=green>System Online</font>&nbsp;</td></tr>
    <tr><td>&nbsp;Uptime:&nbsp;</td>                <td>&nbsp;{$heartbeat_uptime}&nbsp;</td></tr>
    <tr><td>&nbsp;Pulses:&nbsp;</td>                <td>&nbsp;{$heartbeat_ticks} ticks&nbsp;</td></tr>
    <tr><td>&nbsp;Pulse time:&nbsp;</td>            <td>&nbsp;{$heartbeat_rest} minuts&nbsp;</td></tr>
    <tr><td>&nbsp;Average beat time:&nbsp;</td>     <td>&nbsp;{$heartbeat_average} seconds&nbsp;</td></tr>
    <tr><td>&nbsp;Minimum beat time:&nbsp;</td>     <td>&nbsp;{$heartbeat_min} seconds&nbsp;</td></tr>
    <tr><td>&nbsp;Maximum beat time:&nbsp;</td>     <td>&nbsp;{$heartbeat_max} seconds&nbsp;</td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
  {/if}
  {if $commserver_status eq "offline" }
    <tr><td>&nbsp;Communication server:&nbsp;</td>  <td>&nbsp;<font color=red>System Offline</font>&nbsp;</td></tr>    
  {else}
    <tr><td>&nbsp;Communication server:&nbsp;</td>  <td>&nbsp;<font color=green>System Online</font>&nbsp;</td></tr>    
    <tr><td>&nbsp;Uptime:&nbsp;</td>                <td>&nbsp;{$commserver_uptime}&nbsp;</td></tr>
    <tr><td>&nbsp;Processes spawned:&nbsp;</td>     <td>&nbsp;{$commserver_spawns} processes&nbsp;</td></tr>
    <tr><td>&nbsp;Commands served:&nbsp;</td>       <td>&nbsp;{$commserver_commands} commands&nbsp;</td></tr>
    <tr><td>&nbsp;Status Ok:&nbsp;</td>             <td>&nbsp;{$commserver_status_ok} commands&nbsp;</td></tr>
    <tr><td>&nbsp;Status Err:&nbsp;</td>            <td>&nbsp;{$commserver_status_err} commands&nbsp;</td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
  {/if}
  {if $mysql_status eq "offline" }
    <tr><td>&nbsp;MySQL server:&nbsp;</td>          <td>&nbsp;<font color=red>System Offline</font>&nbsp;</td></tr>
  {else}
    <tr><td>&nbsp;MySQL server:&nbsp;</td>          <td>&nbsp;<font color=green>System Online</font>&nbsp;</td></tr> 
    <tr><td>&nbsp;MySQL Uptime:&nbsp;</td>          <td>&nbsp;{$mysql_uptime}&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Queries:&nbsp;</td>         <td>&nbsp;{$mysql_queries} queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Select Queries:&nbsp;</td>  <td>&nbsp;{$mysql_select} queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Insert Queries:&nbsp;</td>  <td>&nbsp;{$mysql_insert} queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Update Queries:&nbsp;</td>  <td>&nbsp;{$mysql_update} queries&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Bytes received:&nbsp;</td>  <td>&nbsp;{$mysql_bytes_received} bytes&nbsp;</td></tr>
    <tr><td>&nbsp;MySQL Bytes sent:&nbsp;</td>      <td>&nbsp;{$mysql_bytes_sent} bytes&nbsp;</td></tr>
  {/if}
  </table>
  <br>
  <br>

<!-- End info -->
