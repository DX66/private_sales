{*
$Id: cc_netbanx.tpl,v 1.1.2.1 2010/09/27 08:53:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>NetBanx</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_netbanx_note|substitute:http_location:$http_location:https_location:$https_location}
<br /><br />
{capture name=dialog}
<img src="{$ImagesDir}/netbanxlogo.gif" border="0" width="125" height="63" alt="" />
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
{*** not required in the current integration
<tr>
<td>{$lng.lbl_cc_netbanx_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
***}
<tr>
<td>{$lng.lbl_cc_netbanx_actionurl}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /><br />
{$lng.lbl_cc_netbanx_actionurl_note}
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param04">
<option value="AUD"{if $module_data.param04 eq "AUD"} selected="selected"{/if}>Australian Dollar</option>
<option value="BRL"{if $module_data.param04 eq "BRL"} selected="selected"{/if}>Brazil Reais</option>
<option value="CAD"{if $module_data.param04 eq "CAD"} selected="selected"{/if}>Canadian Dollar</option>
<option value="CHF"{if $module_data.param04 eq "CHF"} selected="selected"{/if}>Swiss Franc</option>
<option value="CNY"{if $module_data.param04 eq "CNY"} selected="selected"{/if}>China Yuan Renminbi</option>
<option value="DKK"{if $module_data.param04 eq "DKK"} selected="selected"{/if}>Denmark Kroner</option>
<option value="EUR"{if $module_data.param04 eq "EUR"} selected="selected"{/if}>Euro</option>
<option value="GBP"{if $module_data.param04 eq "GBP"} selected="selected"{/if}>Pound Sterling</option>
<option value="HKD"{if $module_data.param04 eq "HKD"} selected="selected"{/if}>Hong Kong Dollar</option>
<option value="HUF"{if $module_data.param04 eq "HUF"} selected="selected"{/if}>Hungarian Forint</option>
<option value="INR"{if $module_data.param04 eq "INR"} selected="selected"{/if}>India Rupees</option>
<option value="JPY"{if $module_data.param04 eq "JPY"} selected="selected"{/if}>Japanese Yen</option>
<option value="MXN"{if $module_data.param04 eq "MXN"} selected="selected"{/if}>Mexico Peso</option>
<option value="NOK"{if $module_data.param04 eq "NOK"} selected="selected"{/if}>Norway Kroner</option>
<option value="NZD"{if $module_data.param04 eq "NZD"} selected="selected"{/if}>New Zealand Dollar</option>
<option value="PLN"{if $module_data.param04 eq "PLN"} selected="selected"{/if}>Poland Zlotych</option>
<option value="SEK"{if $module_data.param04 eq "SEK"} selected="selected"{/if}>Sweden Kronor</option>
<option value="SGD"{if $module_data.param04 eq "SGD"} selected="selected"{/if}>Singapore Dollar</option>
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US Dollar</option>
<option value="ZAR"{if $module_data.param04 eq "ZAR"} selected="selected"{/if}>South Africa Rand</option>   
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_netbanx_ptypes}:</td>
<td>
<select name="param05[]" multiple="multiple">
<option value="card"{if $netbanx_ptypes.card} selected="selected"{/if}>Credit/Debit Card</option>
<option value="directpay24"{if $netbanx_ptypes.directpay24} selected="selected"{/if}>DirectPay24</option>
<option value="neteller"{if $netbanx_ptypes.neteller} selected="selected"{/if}>NETELLER wallet</option>
<option value="ukash"{if $netbanx_ptypes.ukash} selected="selected"{/if}>Ukash</option>
<option value="paypal"{if $netbanx_ptypes.paypal} selected="selected"{/if}>PayPal</option>
<option value="poli"{if $netbanx_ptypes.poli} selected="selected"{/if}>POLi</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_netbanx_secret_key}:</td>
<td><input type="password" name="param06" size="32" value="{$module_data.param06}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
</table>
<br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
