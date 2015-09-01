<!-- messages-normal-msg -->  

{comment}
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 priority_img					url         URL to priority image
 id										int         ID of the message
 priority_str         string      Priority
 from									string			Name 'from' user
 datetime							string			Date and time of the message 
 delete_href          url					URL to delete this message 
 subject							string			Subject of the message
 body									string			Message body
 
-->
{/comment}     
 
  <table class='standard' align='center' width='80%'>
    <tr>
      <td><img src={$priority_img}> <b>({$id}) Message from: {$from} ({$datetime})</b></td>
      <td align=right>[ <a href={$delete_href}></b>X</b></a> ]</td>
    </tr>
    <tr><td colspan=2>Subject: {$subject}</td></tr>
    <tr><td colspan=2>{$body}</td></tr>
  </table>
  <br>
  <br>
  
<!-- End messages-normal-msg -->