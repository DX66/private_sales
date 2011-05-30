{*
$Id: cc_secpay.tpl,v 1.2 2010/08/04 07:15:55 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_secpay_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td><select name="testmode">
<option value="A"{if $module_data.testmode eq "A"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D"{if $module_data.testmode eq "D"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_secpay_cvv2}:</td>
<td><select name="param03">
<option value="true"{if $module_data.param03 eq "true"} selected="selected"{/if}>true</option>
<option value="false"{if $module_data.param03 eq "false"} selected="selected"{/if}>false</option>
</select>
</td>
</tr>
 
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="NOK"{if $module_data.param04 eq "NOK"} selected="selected"{/if}>Norwegian kroners</option>
<option value="SEK"{if $module_data.param04 eq "SEK"} selected="selected"{/if}>Swedish kroners</option>
<option value="DKK"{if $module_data.param04 eq "DKK"} selected="selected"{/if}>Danish kroners</option>
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US dollars</option>
<option value="EUR"{if $module_data.param04 eq "EUR"} selected="selected"{/if}>Euros</option>
<option value="GBP"{if $module_data.param04 eq "GBP"} selected="selected"{/if}>British pounds</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_secpay_remotepass}:</td>
<td><input type="text" name="param06" value="{$module_data.param06|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_secpay_digest}:</td>
<td><input type="text" name="param07" value="{$module_data.param07|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
