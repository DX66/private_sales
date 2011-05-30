{*
$Id: cc_quantum.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>Quantum Gateway. Transparent Database Engine Template.</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<b>{$lng.lbl_note}:</b>
{$lng.txt_cc_quantum_note}
<br /><br />
{$lng.txt_cc_quantum_acc_note}
<br /><br />

<table cellspacing="10">

<tr>
<td>{$lng.lbl_cc_quantum_gwlogin}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_quantum_rkey}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /><br />{$lng.txt_cc_quantum_rkey_note}</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
</tr>
</table>

<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
