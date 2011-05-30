{*
$Id: cc_datatrans_std.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<center>
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_datatrans_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option value="CHF"{if $module_data.param03 eq "CHF"} selected="selected"{/if}>Swiss Franc</option>
<option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro</option>
<option value="USD"{if $module_data.param03 eq "USD"} selected="selected"{/if}>US Dollar</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra="width='100%'"}
