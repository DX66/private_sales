{*
$Id: cc_epdq_xml.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>ePDQ (XML)</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_username}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_password}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_epdq_storeid}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="036"{if $module_data.param04 eq "036"} selected="selected"{/if}>Australian Dollar (AUD)</option>
<option value="124"{if $module_data.param04 eq "124"} selected="selected"{/if}>Canadian Dollar (CAD)</option>
<option value="206"{if $module_data.param04 eq "206"} selected="selected"{/if}>Danish Krone (DKK)</option>
<option value="978"{if $module_data.param04 eq "978"} selected="selected"{/if}>Euro (EUR)</option>
<option value="344"{if $module_data.param04 eq "344"} selected="selected"{/if}>Hong Kong Dollar (HKD)</option>
<option value="392"{if $module_data.param04 eq "392"} selected="selected"{/if}>Japanese Yen (JPY)</option>
<option value="554"{if $module_data.param04 eq "554"} selected="selected"{/if}>New Zealand Dollar (NZD)</option>
<option value="578"{if $module_data.param04 eq "578"} selected="selected"{/if}>Norwegian Krone (NOK)</option>
<option value="702"{if $module_data.param04 eq "702"} selected="selected"{/if}>Singapore Dollar (SGD)</option>
<option value="752"{if $module_data.param04 eq "752"} selected="selected"{/if}>Swedish Krone (SEK)</option>
<option value="756"{if $module_data.param04 eq "756"} selected="selected"{/if}>Swiss Francs (CHF)</option>
<option value="826"{if $module_data.param04 eq "826"} selected="selected"{/if}>Pounds Sterling (GBP)</option>
<option value="840"{if $module_data.param04 eq "840"} selected="selected"{/if}>US Dollar (USD)</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="P"{if $module_data.testmode eq "P"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_ch_settings content=$smarty.capture.dialog extra='width="100%"'}
