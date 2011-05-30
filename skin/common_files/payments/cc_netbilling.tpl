{*
$Id: cc_netbilling.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>Netbilling</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_netbilling_account}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_netbilling_sitetag}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

<br />
<center>{$lng.txt_vbv_admin_note|substitute:"ImagesDir":$ImagesDir}
<br />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
