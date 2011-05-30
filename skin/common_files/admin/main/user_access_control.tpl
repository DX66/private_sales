{*
$Id: user_access_control.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_user_access_control}

<br />

{$lng.txt_user_access_control_descr}

<br /><br />

{if not $func_is_enabled}

{capture name=dialog}
{$lng.txt_user_access_control_disabled}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_user_access_control content=$smarty.capture.dialog extra='width="100%"'}

{else}

{include file="check_ip_address.tpl"}

{capture name=dialog}

<form action="user_access_control.php" method="post" name="ipform" onsubmit="javascript: return (this.mode.value != 'add' || checkIPAddress(this.ip));">
<input type="hidden" name="mode" value="add" />

<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
  <th>&nbsp;</th>
  <th>{$lng.lbl_ip_address}</th>
</tr>
{foreach from=$allowed_ips item=ip}
<tr{cycle values=', class="TableSubHead"' name="allowed_ips"}>
  <td><input type="checkbox" name="ips[]" id="{$ip}" value="{$ip}"{if $current_ip eq $ip} disabled="disabled"{/if} /></td>
  <td><label for="{$ip}">{$ip}</label></td>
</tr>
{/foreach}
<tr>
  <td colspan="2" class="SubmitBox"><input type="button" value="{$lng.lbl_delete_selected|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('ips', 'ig'))) {ldelim}this.form.mode.value = 'delete'; this.form.submit();{rdelim}" /></td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
</tr>
<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_add_ip_address}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="text" size="15" maxlength="15" name="ip" />&nbsp;<input type="submit" value="{$lng.lbl_add|escape}" /></td>
</tr>
</table>

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_allowed_ip_addresses content=$smarty.capture.dialog extra='width="100%"'}

{if $suspended_ips}
<br />
{capture name=dialog}

<form action="user_access_control.php" method="post" name="ipregform">
<input type="hidden" name="mode" value="register" />

<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
    <th>&nbsp;</th>
    <th>{$lng.lbl_ip_address}</th>
  <th>{$lng.lbl_request_expiration_time}</th>
</tr>
{foreach from=$suspended_ips item=v key=k}
<tr{cycle values=', class="TableSubHead"' name="suspended_ips"}>
    <td><input type="checkbox" name="ids[]" id="{$k}" value="{$k}" /></td>
    <td><label for="{$k}">{$v.ip}</label></td>
  <td align="center">{$v.expiry|date_format:$config.Appearance.datetime_format}</td>
</tr>
{/foreach}
</table>

<br />
<input type="button" value="{$lng.lbl_register_selected|escape}" onclick="javascript: this.form.submit();" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_selected|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('ids', 'ig'))) {ldelim}this.form.mode.value = 'delete_reg'; this.form.submit();{rdelim}" />

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_ip_registration_requests content=$smarty.capture.dialog extra='width="100%"'}
{/if}

{/if}
