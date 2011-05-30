{*
$Id: Password_Recovery.tpl,v 1.3 2010/07/05 05:51:27 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%">
<tr>
  <td align="center" width="100%">

    <form action="help.php" method="post" name="processform">
    <input type="hidden" name="action" value="recover_password" />

      <table class="login-table">
      <tr>
        <td colspan="3" class="login-title">{$lng.lbl_forgot_password}</td>
      </tr>

      <tr> 
        <td>{$login_field_name}</td>
        <td><font class="CustomerMessage">*</font></td>
        <td><input type="text" name="username" size="30" value="{$username|escape:"html"}" /></td>
      </tr>

{if $smarty.get.section eq "Password_Recovery_error"}
      <tr>
        <td colspan="3" class="ErrorMessage">{$lng.txt_email_not_match|substitute:"login_field":$login_field_name}</td>
      </tr>
{/if}

      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>

      <tr> 
        <td colspan="2">&nbsp;</td>
        <td class="main-button"><button class="big-main-button" type="submit">{$lng.lbl_submit}</button></td>
      </tr>
      </table>

    </form>

  </td>
</tr>
</table>
