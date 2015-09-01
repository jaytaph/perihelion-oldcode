<!-- building details -->

  <table border=1 cellpadding=0 cellspacing=0 align=center width=50%>
    <tr><th colspan=2>{name}</th></tr>

    <tr>
      <td align=center valign=top bgcolor=black>
        <table border=0 cellpadding=0 cellspacing=0>
          <tr>
            <td>
              <table align=left border=0 cellpadding=0 cellspacing=0 width=100%>
                <tr><td width=100%><img src="{image}" width=150 height=150></td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
      <td align=left valign=top>        
        <table border=0 cellpadding=0 cellspacing=0 width=100%>
           <tr>
             <td class={class}>&nbsp;<strong>Power Needed</strong>&nbsp;</td>
             <td class={class}>&nbsp;<strong>:</strong>&nbsp;</td>
             <td class={class}>&nbsp;{power_needed} uts&nbsp;</td>
           </tr>
           <tr>
             <td class={class}>&nbsp;<strong>Power Output</strong>&nbsp;</td>
             <td class={class}>&nbsp;<strong>:</strong>&nbsp;</td>
             <td class={class}>&nbsp;{power_output} uts&nbsp;</td>
           </tr>
           <tr><td colspan=3><hr></td></tr>
           <tr>
             <td class={class}>&nbsp;<strong>Attack</strong>&nbsp;</td>
             <td class={class}>&nbsp;<strong>:</strong>&nbsp;</td>
             <td class={class}>&nbsp;{attack} pts&nbsp;</td>
           </tr>
           <tr>
             <td class={class}>&nbsp;<strong>Defense</strong>&nbsp;</td>
             <td class={class}>&nbsp;<strong>:</strong>&nbsp;</td>
             <td class={class}>&nbsp;{defense} pts&nbsp;</td>
           </tr>
           <tr>
             <td class={class}>&nbsp;<strong>Strength</strong>&nbsp;</td>
             <td class={class}>&nbsp;<strong>:</strong>&nbsp;</td>
             <td class={class}>&nbsp;{strength} pts&nbsp;</td>
           </tr> 
         </table>
       </td>
     </tr>


  [BLOCK block_build]
    <tr><td colspan=2>&nbsp;</td></tr>
    <tr><td>
      <!--INCLUDE SBT]-->
      </td><td>
      <!--INCLUDE SBT]-->
      </td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
  [END block_build]    
  [BLOCK block_nobuild]
    <tr><td colspan=2>&nbsp;</td></tr>
    <tr><td>
      <!--INCLUDE SBT]-->
      </td><td>
      <!--INCLUDE SBT]-->
    </td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
  [END block_nobuild]

  [BLOCK block_rule]
    <tr><td colspan=2><table border=0 cellspacing=5><tr><td>&nbsp;Effect: {rule}&nbsp;</td></tr></table></td></tr>
  [END block_rule]
  
  [BLOCK block_description]
    <tr><td colspan=2><table border=0 cellspacing=5><tr><td>&nbsp;{description}&nbsp;</td></tr></table></td></tr>
  [END block_description]

  [BLOCK block_build2]
    <tr><th colspan=2><a href="{construction_href}">BUILD IT</a></th></tr>
  [END block_build2]
  [BLOCK block_nobuild2] 
    <tr><th colspan=2>CANNOT BUILD</th></tr>   
  [END block_nobuild2]
  
  </table>
  <br>
  <br>

<!-- End building details -->
