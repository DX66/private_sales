{*
$Id: cc_egold.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>E-Gold</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_egold_account}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_egold_name}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option value="1"{if $module_data.param03 eq "1"} selected="selected"{/if}>US Dollar (USD)</option>
<option value="2"{if $module_data.param03 eq "2"} selected="selected"{/if}>Canadian Dollar (CAD)</option>
<option value="33"{if $module_data.param03 eq "33"} selected="selected"{/if}>French Franc (FRF)</option>
<option value="41"{if $module_data.param03 eq "41"} selected="selected"{/if}>Swiss Francs (CHF)</option>
<option value="44"{if $module_data.param03 eq "44"} selected="selected"{/if}>Gt. Britain Pound (GPB)</option>
<option value="49"{if $module_data.param03 eq "49"} selected="selected"{/if}>Deutschemark (DEM)</option>
<option value="61"{if $module_data.param03 eq "61"} selected="selected"{/if}>Australian Dollar (AUD)</option>
<option value="81"{if $module_data.param03 eq "81"} selected="selected"{/if}>Japanese Yen (JPY)</option>
<option value="85"{if $module_data.param03 eq "85"} selected="selected"{/if}>Euro (EUR)</option>
<option value="86"{if $module_data.param03 eq "86"} selected="selected"{/if}>Belgian Franc (BEF)</option>
<option value="87"{if $module_data.param03 eq "87"} selected="selected"{/if}>Austrian Schilling (ATS)</option>
<option value="88"{if $module_data.param03 eq "88"} selected="selected"{/if}>Greek Drachma (GRD)</option>
<option value="89"{if $module_data.param03 eq "89"} selected="selected"{/if}>Spanish Peseta (ESP)</option>
<option value="90"{if $module_data.param03 eq "90"} selected="selected"{/if}>Irish Pound (IEP)</option>
<option value="91"{if $module_data.param03 eq "91"} selected="selected"{/if}>Italian Lira (ITL)</option>
<option value="92"{if $module_data.param03 eq "92"} selected="selected"{/if}>Luxembourg Franc (LUF)</option>
<option value="93"{if $module_data.param03 eq "93"} selected="selected"{/if}>Dutch Guilder (NLG)</option>
<option value="94"{if $module_data.param03 eq "94"} selected="selected"{/if}>Portuguese Escudo (PTE)</option>
<option value="95"{if $module_data.param03 eq "95"} selected="selected"{/if}>Finnish Markka (FIM)</option>
<option value="96"{if $module_data.param03 eq "96"} selected="selected"{/if}>Estonian Kroon (EEK)</option>
<option value="97"{if $module_data.param03 eq "97"} selected="selected"{/if}>Lithuanian Litas (LTL)</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
