<!-- help -->

  {if $help eq ""} 
    <table class=standard align=center width=80%>
  	  <tr><td>Sorry but we cannot find any information about this help topic.</td></tr>
    </table>
  {else}
    <table class=standard align=center width=80%>
  	  <tr><th>{$topic}</th></tr>
  	  <tr><td>{$help}</td></tr>
    </table>
  {/if}    
  <br>
  <br>
  
  <table align=center border=0>
    <form><input type='button' value='Close Window' onClick='window.close()'></form>
  </table>
  <br>
  <br>
  
<!-- End help -->
