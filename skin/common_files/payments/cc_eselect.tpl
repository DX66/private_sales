{*
$Id: cc_eselect.tpl,v 1.4 2010/07/19 12:42:54 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>eSelect plus</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}

<b>{$lng.lbl_note}:</b>
{$lng.txt_cc_eselect_note}
<br /><br />

<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_eselect_storeid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_eselect_apitoken}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_eselect_account_type}:</td>
<td>
<select name="param03">
  <option value="US">{$lng.lbl_eselect_us_acc}</option>
  <option value="CA"{if $module_data.param03 eq 'CA'} selected="selected"{/if}>{$lng.lbl_eselect_ca_acc}</option>
</select>
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
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
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
<td>{$lng.lbl_cc_eselect_cvd_avs_efraud}</td>
<td>
<select name="param05">
<option value="Y"{if $module_data.param05 eq "Y"} selected="selected"{/if}>{$lng.lbl_on}</option>
<option value="N"{if $module_data.param05 eq "N"} selected="selected"{/if}>{$lng.lbl_off}</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_3dsecure}</td>
<td>
<select name="cmpi">
<option value="B"{if $module_data.cmpi eq "B"} selected="selected"{/if}>{$lng.lbl_on}</option>
<option value=""{if $module_data.cmpi eq ""} selected="selected"{/if}>{$lng.lbl_off}</option>
</select>
</td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

<br />
<center><b>{$lng.lbl_note}:</b> {$lng.txt_cc_eselect_note_2}</center>
<br /><br />
<center>{$lng.txt_vbv_embedded_admin_note|substitute:"ImagesDir":$ImagesDir}</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
