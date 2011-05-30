{*
$Id: change_mpassword.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<br />

{capture name=dialog}
<form action="change_mpassword.php" method="post">
{if $from_config ne ''}
<input type="hidden" name="from_config" value="{$from_config|escape}" />
{/if}

<table>
<tr>
  <td colspan="3">{$lng.txt_change_mpassword}</td>
</tr>
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
{if $config.mpassword ne ''}
<tr>
  <td>{$lng.lbl_old_merchant_password}:</td>
  <td><font class="Star">*</font></td>
  <td><input type="password" size="30" maxlength="64" name="old_password" value="{$old_password|escape}" /></td>
</tr>
{/if}
<tr>
  <td>{if $config.mpassword eq ''}{$lng.lbl_merchant_password}{else}{$lng.lbl_new_merchant_password}{/if}:</td>
  <td><font class="Star">*</font></td>
  <td><input type="password" size="30" name="new_password" value="{$new_password|escape}" /></td>
</tr>
<tr>
  <td>{$lng.lbl_confirm_merchant_password}:</td>
  <td><font class="Star">*</font></td>
  <td><input type="password" size="30" name="confirm_password" value="{$confirm_password|escape}" /></td>
</tr>
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3" align="center"><input type="submit" value="{$lng.lbl_submit}" /></td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"'}
