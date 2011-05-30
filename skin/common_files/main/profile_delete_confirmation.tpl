{*
$Id: profile_delete_confirmation.tpl,v 1.2 2010/07/30 12:40:25 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

<form action="register.php" method="post" name="processform">

<input type="hidden" name="confirmed" value="Y" />
<input type="hidden" name="mode" value="delete" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
  <td>
<div class="Text">
{$lng.txt_profile_delete_confirmation}<br />

{if $provider_counters.total gt 0}
<br />
{if $is_provider_profile}
<font class="ErrorMessage">{$lng.lbl_warning}:</font> {$lng.txt_remove_provider_with_data}
{elseif $move_to_providers}
{$lng.txt_remove_provider_and_move_data}<br /><br />
<label for="next_provider">{$lng.txt_please_specify_user_to_whom_you_information_to_assigned}</label>
<select name="next_provider" id="next_provider">
{foreach from=$move_to_providers item=v}
  <option value="{$v.id|escape}">{$v.firstname} {$v.lastname} ({$v.login})</option>
{/foreach}
</select>
{else}
{$lng.txt_remove_provider_and_anonymyze_data}
{/if}
{/if}
</div>
<br /><br />
<table cellspacing="0" cellpadding="2">
<tr>
  <td>{include file="buttons/yes.tpl" href="javascript:document.processform.mode.value='delete';document.processform.submit()" js_to_href="Y"}</td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/no.tpl" href="register.php?mode=notdelete"}</td>
</tr>
</table>

  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
