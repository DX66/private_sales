{*
$Id: cc_asiadeb.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>AsiaDebit</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_asiadeb_note|substitute:"http_location":$http_location}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_asiadeb_shopid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option value="SEK"{if $module_data.param02 eq "SEK"} selected="selected"{/if}>Swedish kroner</option>
<option value="DKK"{if $module_data.param02 eq "DKK"} selected="selected"{/if}>Danish kroner</option>
<option value="USD"{if $module_data.param02 eq "USD"} selected="selected"{/if}>US dollar</option>
<option value="EUR"{if $module_data.param02 eq "EUR"} selected="selected"{/if}>Euro</option>
<option value="GBP"{if $module_data.param02 eq "GBP"} selected="selected"{/if}>British pound</option>
<option value="ATS"{if $module_data.param02 eq "ATS"} selected="selected"{/if}>Austria Schilling</option>
<option value="BEF"{if $module_data.param02 eq "BEF"} selected="selected"{/if}>Belgium Franc</option>
<option value="FIM"{if $module_data.param02 eq "FIM"} selected="selected"{/if}>Finland Markka</option>
<option value="IEP"{if $module_data.param02 eq "IEP"} selected="selected"{/if}>Ireland Punt</option>
<option value="ITL"{if $module_data.param02 eq "ITL"} selected="selected"{/if}>Italy Lira</option>
<option value="LUF"{if $module_data.param02 eq "LUF"} selected="selected"{/if}>Luxembourg Francs</option>
<option value="NLG"{if $module_data.param02 eq "NLG"} selected="selected"{/if}>Netherlands (Dutch) Guilders</option>
<option value="NOK"{if $module_data.param02 eq "NOK"} selected="selected"{/if}>Norway Kroner</option>
<option value="PTE"{if $module_data.param02 eq "PTE"} selected="selected"{/if}>Portugal Escudo</option>
<option value="ESP"{if $module_data.param02 eq "ESP"} selected="selected"{/if}>Spain Pesetas</option>
<option value="CHF"{if $module_data.param02 eq "CHF"} selected="selected"{/if}>Switzerland Francs</option>
<option value="THB"{if $module_data.param02 eq "THB"} selected="selected"{/if}>Thailand Baht</option>
<option value="SGD"{if $module_data.param02 eq "SGD"} selected="selected"{/if}>Singapore Dollars</option>
<option value="HKD"{if $module_data.param02 eq "HKD"} selected="selected"{/if}>Hong Kong Dollars</option>
<option value="MYR"{if $module_data.param02 eq "MYR"} selected="selected"{/if}>Malaysia Ringgit</option>
<option value="JPY"{if $module_data.param02 eq "JPY"} selected="selected"{/if}>Japan Yen</option>
<option value="CAD"{if $module_data.param02 eq "CAD"} selected="selected"{/if}>Canada Dollars</option>
</select>
</td>
</tr>

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
