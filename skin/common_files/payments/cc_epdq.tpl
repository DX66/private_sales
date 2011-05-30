{*
$Id: cc_epdq.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>ePDQ</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_epdq_note|substitute:"http_location":$http_location:"https_location":$https_location}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_epdq_merchant_name}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_epdq_clientid}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_epdq_passphrase}:</td>
<td><input type="password" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_epdq_chargetype}:</td>
<td><select name="param05">
<option value="Auth"{if $module_data.param05 eq "Auth"} selected="selected"{/if}>Auth / immediate</option>
<option value="PreAuth"{if $module_data.param05 eq "PreAuth"} selected="selected"{/if}>PreAuth / delayed shipment</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="036"{if $module_data.param04 eq "036"} selected="selected"{/if}>Australian Dollar</option>
<option value="124"{if $module_data.param04 eq "124"} selected="selected"{/if}>Canadian Dollar</option>
<option value="208"{if $module_data.param04 eq "208"} selected="selected"{/if}>Danish Krone</option>
<option value="344"{if $module_data.param04 eq "344"} selected="selected"{/if}>Hong Kong Dollar</option>
<option value="392"{if $module_data.param04 eq "392"} selected="selected"{/if}>Japanese Yen</option>
<option value="578"{if $module_data.param04 eq "578"} selected="selected"{/if}>Norwegian Krone</option>
<option value="752"{if $module_data.param04 eq "752"} selected="selected"{/if}>Swedish Krona</option>
<option value="756"{if $module_data.param04 eq "756"} selected="selected"{/if}>Swiss Francs</option>
<option value="826"{if $module_data.param04 eq "826"} selected="selected"{/if}>Sterling</option>
<option value="840"{if $module_data.param04 eq "840"} selected="selected"{/if}>US Dollars</option>
<option value="978"{if $module_data.param04 eq "978"} selected="selected"{/if}>Euro</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_epdq_logo}:<br />
{$lng.lbl_cc_epdq_logo_note}
</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
