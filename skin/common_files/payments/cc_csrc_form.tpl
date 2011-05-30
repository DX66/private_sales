{*
$Id: cc_csrc_form.tpl,v 1.2 2010/08/04 07:15:54 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>CyberSource (hosted page)</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_csrc_form_configure_note}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post" enctype="multipart/form-data">
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
{include file="main/select_currency.tpl" name="param03" current_currency=$module_data.param03}
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_use_preauth_method}:</td>
<td>
<select name="use_preauth">
<option value="">{$lng.lbl_auth_and_capture_method}</option>
<option value="Y"{if $module_data.use_preauth eq "Y"} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td colspan="2"><hr /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_csrc_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_csrc_serial_number}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_csrc_public_key}:</td>
<td><textarea name="param05" cols="32" rows="5">{$module_data.param05|escape}</textarea></td>
</tr>
<tr>
<td>{$lng.lbl_cc_csrc_secret_key}:</td>
<td><textarea name="param06" cols="32" rows="5">{$module_data.param06|escape}</textarea></td>
</tr>
<tr>
<td><hr /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_csrc_upload_security_script}:</td>
<td><input type="file" name="security_script" size="32" value="" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
