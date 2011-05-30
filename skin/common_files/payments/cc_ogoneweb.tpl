{*
$Id: cc_ogoneweb.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>Ogone</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_ogoneweb_callback_url}:</td>
<td>{if $module_data.protocol eq 'https'}{$https_location}{else}{$http_location}{/if}/payment/cc_ogoneweb_result.php</td>
</tr>
<tr>
<td>{$lng.lbl_cc_ogw_id}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_ogw_sig}:</td>
<td><input type="password" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param04">
<option value="ATS"{if $module_data.param04 eq "ATS"} selected="selected"{/if}>Austrian Shilling</option>
<option value="AUD"{if $module_data.param04 eq "AUD"} selected="selected"{/if}>Australian Dollar</option>
<option value="BEF"{if $module_data.param04 eq "BEF"} selected="selected"{/if}>Belgian franc</option>
<option value="CAD"{if $module_data.param04 eq "CAD"} selected="selected"{/if}>Canadian Dollar</option>
<option value="CHF"{if $module_data.param04 eq "CHF"} selected="selected"{/if}>Swiss Franc</option>
<option value="CZK"{if $module_data.param04 eq "CZK"} selected="selected"{/if}>Czech Koruna</option>
<option value="DEM"{if $module_data.param04 eq "DEM"} selected="selected"{/if}>German mark</option>
<option value="DKK"{if $module_data.param04 eq "DKK"} selected="selected"{/if}>Danish Kroner</option>
<option value="ESP"{if $module_data.param04 eq "ESP"} selected="selected"{/if}>Spanish Peseta</option>
<option value="EUR"{if $module_data.param04 eq "EUR"} selected="selected"{/if}>EURO</option>
<option value="FIM"{if $module_data.param04 eq "FIM"} selected="selected"{/if}>Finnish Markka</option>
<option value="FRF"{if $module_data.param04 eq "FRF"} selected="selected"{/if}>French franc</option>
<option value="GBP"{if $module_data.param04 eq "GBP"} selected="selected"{/if}>British pound</option>
<option value="HKD"{if $module_data.param04 eq "HKD"} selected="selected"{/if}>Hong Kong Dollar</option>
<option value="HUF"{if $module_data.param04 eq "HUF"} selected="selected"{/if}>Hungarian Forint</option>
<option value="IEP"{if $module_data.param04 eq "IEP"} selected="selected"{/if}>Irish Punt</option>
<option value="ILS"{if $module_data.param04 eq "ILS"} selected="selected"{/if}>New Shekel</option>
<option value="ITL"{if $module_data.param04 eq "ITL"} selected="selected"{/if}>Italian Lira</option>
<option value="JPY"{if $module_data.param04 eq "JPY"} selected="selected"{/if}>Japanese Yen</option>
<option value="LTL"{if $module_data.param04 eq "LTL"} selected="selected"{/if}>Litas</option>
<option value="LUF"{if $module_data.param04 eq "LUF"} selected="selected"{/if}>Luxembourg franc</option>
<option value="LVL"{if $module_data.param04 eq "LVL"} selected="selected"{/if}>Lats Letton</option>
<option value="MXN"{if $module_data.param04 eq "MXN"} selected="selected"{/if}>Peso</option>
<option value="NLG"{if $module_data.param04 eq "NLG"} selected="selected"{/if}>Dutch Guilders</option>
<option value="NOK"{if $module_data.param04 eq "NOK"} selected="selected"{/if}>Norwegian Kroner</option>
<option value="NZD"{if $module_data.param04 eq "NZD"} selected="selected"{/if}>New Zealand Dollar</option>
<option value="PLN"{if $module_data.param04 eq "PLN"} selected="selected"{/if}>Polish Zloty</option>
<option value="PTE"{if $module_data.param04 eq "PTE"} selected="selected"{/if}>Portuguese Escudo</option>
<option value="RUR"{if $module_data.param04 eq "RUR"} selected="selected"{/if}>Rouble</option>
<option value="SEK"{if $module_data.param04 eq "SEK"} selected="selected"{/if}>Swedish Krone</option>
<option value="SGD"{if $module_data.param04 eq "SGD"} selected="selected"{/if}>Singapore Dollar</option>
<option value="SKK"{if $module_data.param04 eq "SKK"} selected="selected"{/if}>Couronne Slovaque</option>
<option value="THB"{if $module_data.param04 eq "THB"} selected="selected"{/if}>Thai Bath</option>
<option value="TRL"{if $module_data.param04 eq "TRL"} selected="selected"{/if}>Lire Turque</option>
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US Dollar</option>
<option value="ZAR"{if $module_data.param04 eq "ZAR"} selected="selected"{/if}>South African Rand</option>
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
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
