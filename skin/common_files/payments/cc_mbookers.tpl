{*
$Id: cc_mbookers.tpl,v 1.2 2010/06/21 12:41:37 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
<p>
  <a href="http://www.moneybookers.com/partners/x-cart/" target="_blank"><img style="padding-left: 10px;" align="right" src="http://www.moneybookers.com/images/logos/checkout_logos/checkout_en_120x40px.gif" border="0" alt="Moneybookers logo" /></a>
  {$lng.txt_cc_configure_top_text}
</p>
<p>{$lng.lbl_cc_mbookers_register_txt}</p>
<br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10" width="100%">

<tr>
<td>{$lng.lbl_cc_mbookers_pay_to_email}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

{if $module_data.param02 ne ""}
{assign var="mb_currency" value=$module_data.param02}
{else}
{assign var="mb_currency" value="GBP"}
{/if}

{include file="payments/currencies.tpl" param_name='param02' current=$mb_currency}

<tr>
<td>{$lng.lbl_cc_mbookers_language}:</td>
<td>
<select name="param03">
<option value="EN"{if $module_data.param03 eq "EN"} selected="selected"{/if}>English</option>
<option value="DE"{if $module_data.param03 eq "DE"} selected="selected"{/if}>German</option>
<option value="ES"{if $module_data.param03 eq "ES"} selected="selected"{/if}>Spanish</option>
<option value="FR"{if $module_data.param03 eq "FR"} selected="selected"{/if}>French</option>
<option value="IT"{if $module_data.param03 eq "IT"} selected="selected"{/if}>Italian</option>
<option value="PL"{if $module_data.param03 eq "PL"} selected="selected"{/if}>Polish</option>
<option value="GR"{if $module_data.param03 eq "GR"} selected="selected"{/if}>Greek</option>
<option value="RO"{if $module_data.param03 eq "RO"} selected="selected"{/if}>Romanian</option>
<option value="RU"{if $module_data.param03 eq "RU"} selected="selected"{/if}>Russian</option>
<option value="TR"{if $module_data.param03 eq "TR"} selected="selected"{/if}>Turkese</option>
<option value="CN"{if $module_data.param03 eq "CN"} selected="selected"{/if}>Chinese</option>
<option value="CZ"{if $module_data.param03 eq "CZ"} selected="selected"{/if}>Chech</option>
<option value="NL"{if $module_data.param03 eq "NL"} selected="selected"{/if}>Dutch</option>
<option value="DA"{if $module_data.param03 eq "DA"} selected="selected"{/if}>Danish</option>
<option value="SV"{if $module_data.param03 eq "SV"} selected="selected"{/if}>Swedish</option>
<option value="FI"{if $module_data.param03 eq "FI"} selected="selected"{/if}>Finnish</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_mbookers_secret_key}:</td>
<td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
<td width="60%">{$lng.lbl_cc_mbookers_logo_url}:</td>
<td width="40%"><input type="text" name="param06" size="32" value="{$module_data.param06|default:"`$https_location`/skin/common_files/images/xlogo.gif"|escape}" /></td>
</tr>

<tr>
<td width="60%">{$lng.lbl_cc_mbookers_status_email}:</td>
<td width="40%"><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
