{*
$Id: change_password.tpl,v 1.3 2010/07/19 14:13:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_password_script.tpl"}
{capture name=dialog}
<form action="change_password.php{if $password_reset_key ne ''}?password_reset_key={$password_reset_key}&amp;user={$userid}{/if}" method="post" name="change_password" {if $config.Security.use_complex_pwd eq 'Y'} onsubmit="javascript: return checkPasswordStrength(document.change_password.new_password,document.change_password.confirm_password);"{/if}>
<table cellspacing="1" cellpadding="1">
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr class="FormButton">
  <td>{$login_field_name}:</td>
  <td>&nbsp;</td>
  <td><b>{$username}</b><input type="hidden" name="uname" value="{$username|escape}" /></td>
</tr>
{if $mode ne 'recover_password'}
<tr class="FormButton">
  <td>{$lng.lbl_old_password}:</td>
  <td><font class="Star">*</font></td>
  <td><input type="password" size="30" name="old_password" maxlength="64" value="{$old_password|escape}" /></td>
</tr>
{/if}
<tr class="FormButton">
  <td>{$lng.lbl_new_password}:</td>
  <td><font class="Star">*</font></td>
  <td>
    <input type="password" size="30" name="new_password" maxlength="64" value="{$new_password|escape}"{if $config.Security.use_complex_pwd eq 'Y'} onblur="javascript: $('#passwd_note').hide();" onfocus="javascript: showNote('passwd_note', this);"{/if} />
    {if $config.Security.use_complex_pwd eq 'Y'}<div id="passwd_note" class="NoteBox" style="display: none;">{$lng.txt_password_strength}<br /></div>{/if}
  </td>
</tr>
<tr class="FormButton">
  <td>{$lng.lbl_confirm_password}:</td>
  <td><font class="Star">*</font></td>
  <td><input type="password" size="30" name="confirm_password" value="{$confirm_password|escape}"{if $config.Security.use_complex_pwd eq 'Y'} onblur="javascript: $('#passwd_note').hide();" onfocus="javascript: showNote('passwd_note', this.form.elements.namedItem('new_password'));"{/if} /></td>
</tr>
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr class="FormButton">
  <td colspan="2">&nbsp;</td>
  <td align="left"><input type="submit" value="{$lng.lbl_submit}" /></td>
</tr>
</table>
</form>
{if $config.Security.check_old_passwords eq 'Y'}
<br />
<div>
  {$lng.txt_ch_oldpass_info}
</div>
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_chpass content=$smarty.capture.dialog extra='width="100%"'}
