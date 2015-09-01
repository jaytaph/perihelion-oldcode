<!-- whoisonline -->

  <table class="standard" align="center" width="60%">
    <tr>
      <th>Full Name</th>
      <th>Last action</th>
    </tr>

    {section name='row' loop=$onlineusers}
    <tr class="{cycle values="odd,even"}">
      <td>&nbsp;<a href='{$onlineusers[row].href}'>{$onlineusers[row].user}</a>&nbsp;</td>
      <td>&nbsp;{$onlineusers[row].idle}&nbsp;</td>
    </tr>
    {/section}
  </table>
  
<!-- End whoisonline -->
