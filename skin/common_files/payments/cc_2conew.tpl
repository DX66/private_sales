{*
$Id: cc_2conew.tpl,v 1.1.2.2 2011/02/10 08:24:10 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>2checkout.Com</h1>
{$lng.txt_cc_configure_top_text}
<br />
{$lng.txt_cc_2conew_desc}
{$lng.txt_cc_2conew_note|substitute:"http_location":$http_location}
<br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_2checkoutcom_account}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_2checkoutcom_secret}:</td>
<td><input type="password" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
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
<td>Purchase routine:</td>
<td>
<select name="param06">
<option value="https://www.2checkout.com/checkout/purchase"{if $module_data.param06 eq "https://www.2checkout.com/checkout/purchase"} selected="selected"{/if}>Multi-Page Checkout</option>
<option value="https://www.2checkout.com/checkout/spurchase"{if $module_data.param06 eq "https://www.2checkout.com/checkout/spurchase"} selected="selected"{/if}>Single Page Checkout</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_2conew_skip_landing}:</td>
<td><input type="checkbox" name="param04" value="Y"{if $module_data.param04 eq "Y"} checked="checked"{/if} /></td>
</tr>
<tr>
<td>{$lng.lbl_language}:</td>
<td>
<select name="param05">
<option value="en"{if $module_data.param05 eq "en"} selected="selected"{/if} >English</option>
<option value="zh"{if $module_data.param05 eq "zh"} selected="selected"{/if} >Chinese</option>
<option value="da"{if $module_data.param05 eq "da"} selected="selected"{/if} >Danish</option>
<option value="nl"{if $module_data.param05 eq "nl"} selected="selected"{/if} >Dutch</option>
<option value="fr"{if $module_data.param05 eq "fr"} selected="selected"{/if} >French</option>
<option value="gr"{if $module_data.param05 eq "gr"} selected="selected"{/if} >German</option>
<option value="el"{if $module_data.param05 eq "el"} selected="selected"{/if} >Greek</option>
<option value="it"{if $module_data.param05 eq "it"} selected="selected"{/if} >Italian</option>
<option value="jp"{if $module_data.param05 eq "jp"} selected="selected"{/if} >Japanese</option>
<option value="no"{if $module_data.param05 eq "no"} selected="selected"{/if} >Norwegian</option>
<option value="pt"{if $module_data.param05 eq "pt"} selected="selected"{/if} >Portuguese</option>
<option value="sl"{if $module_data.param05 eq "sl"} selected="selected"{/if} >Slovenian</option>
<option value="es_ib"{if $module_data.param05 eq "es_ib"} selected="selected"{/if} >European Spanish</option>
<option value="es_la"{if $module_data.param05 eq "es_la"} selected="selected"{/if} >Latin Spanish</option>
<option value="sv"{if $module_data.param05 eq "sv"} selected="selected"{/if} >Swedish</option>
</select>
</td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
