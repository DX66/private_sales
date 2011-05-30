{*
$Id: Password_Recovery.tpl,v 1.2 2010/06/30 13:22:03 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_forgot_password}</h1>

<p class="text-block">{$lng.txt_password_recover}</p>

{capture name=dialog}

  <form action="help.php" method="post" name="processform">
    <input type="hidden" name="action" value="recover_password" />

    <table cellspacing="0" class="data-table">

      <tr> 
        <td class="data-name">{$login_field_name}</td>
        <td class="data-required">*</td>
        <td>
          <input type="text" name="username" size="30" value="{$username|escape:"html"}" />
          {if $smarty.get.section eq "Password_Recovery_error"}
            <div class="error-message">{$lng.txt_email_not_match|substitute:"login_field":$login_field_name}</div>
          {/if}
        </td>
      </tr>

      <tr> 
        <td colspan="2">&nbsp;</td>
        <td class="button-row">{include file="customer/buttons/submit.tpl" type="input"}</td>
      </tr>

    </table>

  </form>
{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_forgot_password content=$smarty.capture.dialog noborder=true}
