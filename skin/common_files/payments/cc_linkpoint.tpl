{*
$Id: cc_linkpoint.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>LinkPoint (CardService International)</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_linkpoint_desc}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td nowrap="nowrap">{$lng.lbl_cc_linkpoint_storename}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_cc_linkpoint_hostport}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" />:<input type="text" name="param07" size="4" value="{$module_data.param07|default:"1129"}" /><br />
{$lng.lbl_cc_linkpoint_hostport_note}</td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_cc_linkpoint_certpath}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_cc_linkpoint_avs}:</td>
<td>
<select name="param08">
<option value="Y"{if $module_data.param08 eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
<option value=""{if $module_data.param08 ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
</select>
</td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="A"{if $module_data.testmode eq "A"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D"{if $module_data.testmode eq "D"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_cc_linkpoint_cvm}:</td>
<td>
<select name="param04">
<option value="not_provided"{if $module_data.param04 eq "not_provided"} selected="selected"{/if}>{$lng.lbl_cc_linkpoint_cvm_not_provided}</option>
<option value="provided"{if $module_data.param04 eq "provided"} selected="selected"{/if}>{$lng.lbl_cc_linkpoint_cvm_provided}</option>
<option value="illegible"{if $module_data.param04 eq "illegible"} selected="selected"{/if}>{$lng.lbl_cc_linkpoint_cvm_illegible}</option>
<option value="not_present"{if $module_data.param04 eq "not_present"} selected="selected"{/if}>{$lng.lbl_cc_linkpoint_cvm_not_present}</option>
</select>
</td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_use_preauth_method}:</td>
<td>
  <select name="use_preauth">
    <option value="">{$lng.lbl_auth_and_capture_method}</option>
    <option value="Y"{if $module_data.use_preauth eq 'Y'} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
  </select>
  <br /><br />
  {$lng.txt_ccdata_save_warning}
</td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
