{*
$Id: cc_eproc.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>eProcessingNetwork. Transparent Database Engine Template.</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_eproc_account}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_eproc_restrict_key}:</td>
<td><input type="password" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_eproc_cvv}:</td>
<td>
<select name="param03">
<option value="9"{if $module_data.param03 eq "9"} selected="selected"{/if}>{$lng.lbl_cc_eproc_cvv_9}</option>
<option value="2"{if $module_data.param03 eq "2"} selected="selected"{/if}>{$lng.lbl_cc_eproc_cvv_2}</option>
<option value="1"{if $module_data.param03 eq "1"} selected="selected"{/if}>{$lng.lbl_cc_eproc_cvv_1}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
