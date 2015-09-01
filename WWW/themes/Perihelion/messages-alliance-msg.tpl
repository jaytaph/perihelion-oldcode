<!-- messages-alliance-msg -->  

{comment}
<!--
 ==== Description ===================================================================================
 
 ==== Remarks =======================================================================================
 
 ==== Smarty Variables ==============================================================================

 from									string			Name 'from' user
 datetime							string			Date and time of the message
 image								url					URL to user image
 subject							string			Subject of the message
 body									string			Message body
 
-->
{/comment}     
 
  <table class='standard' align='center'>
    <tr><td colspan=2><b>Message from: {$from} ({$datetime})</b></td></tr>
    <tr>
      <td rowspan=2 valign=top width=150><img width=100 height=100 src={$image}></td>
      <td width=100%<b>Subject: </b>{$subject}</td>
    </tr>
    <tr><td colspan=2><font color=#cccc33>{$body}</font></td></tr>
  </table>
  <br>
  <br>
  
<!-- End messages-alliance-msg -->
