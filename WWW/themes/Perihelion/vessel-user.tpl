<!-- vessel-user.tpl -->

{comment}
<!--

 use 'status_nohref' for no hyperlinks inside the status
-->
{/comment}

  <table class='standard' align=center border=0 width=75%>
    <tr>
      <th>Name</th>
      <th>Type</th>
      <th>Status</th>
      <th>Coords</th>
    </tr>
    
    
    {section name=row loop=$vessels}
      <tr class={cycle values="odd,even"}>
      <td><img src='{$vessels[row].image}'><a href='{$vessels[row].href}'>{$vessels[row].name}</a></td>
      <td>{$vessels[row].type}</td>
      <td>{$vessels[row].status_nohref}</td>
      <td>{$vessels[row].distance} / {$vessels[row].angle}</td>
    {/section}
    
  </table>
  <br>
  <br>

<!-- end vessel-user.tpl -->