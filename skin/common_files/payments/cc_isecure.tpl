{*
$Id: cc_isecure.tpl,v 1.2 2010/08/04 07:15:55 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>InternetSecure</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
{$lng.txt_cc_isecure_info}
<br />
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_isecure_merchant}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td><select name="testmode">
<option value="A"{if $module_data.testmode eq "A"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D"{if $module_data.testmode eq "D"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td></tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="CURRENT"{if $module_data.param04 eq "CURRENT"} selected="selected"{/if}>Account current currency</option>
<option value="US"{if $module_data.param04 eq "US"} selected="selected"{/if}>US dollar</option>
</select>
</td></tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
