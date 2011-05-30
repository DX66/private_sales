{*
$Id: cc_pp3.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h3>ProxyPay<SUP>3</SUP></h3>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">

<tr>
<td>{$lng.lbl_pp3_validation_script}:</td>
<td>{$http_location}/payment/ebank_validation.php</td>
</tr>
<tr>
<td>{$lng.lbl_pp3_confirmation_script}:</td>
<td>{$http_location}/payment/ebank_confirmation.php</td>
</tr>

<tr>
<td>{$lng.lbl_cc_pp3_serverurl}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /><br />
{$lng.lbl_cc_pp3_serverurl_note}
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_pp3_merchantid}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param03">
<option value="0040"{if $module_data.param03 eq "0040"} selected="selected"{/if}>Austrian Shilling</option>
<option value="0056"{if $module_data.param03 eq "0056"} selected="selected"{/if}>Belgian Franc</option>
<option value="0250"{if $module_data.param03 eq "0250"} selected="selected"{/if}>French Franc</option>
<option value="0300"{if $module_data.param03 eq "0300"} selected="selected"{/if}>Greek Dragmen</option>
<option value="0280"{if $module_data.param03 eq "0280"} selected="selected"{/if}>Deutsche Mark</option>
<option value="0380"{if $module_data.param03 eq "0380"} selected="selected"{/if}>Italian Lira</option>
<option value="0442"{if $module_data.param03 eq "0442"} selected="selected"{/if}>Luxembourg Franc</option>
<option value="0528"{if $module_data.param03 eq "0528"} selected="selected"{/if}>Netherlands Guilder</option>
<option value="0724"{if $module_data.param03 eq "0724"} selected="selected"{/if}>Spanish Peseta</option>
<option value="0756"{if $module_data.param03 eq "0756"} selected="selected"{/if}>Swiss Francs</option>
<option value="0826"{if $module_data.param03 eq "0826"} selected="selected"{/if}>Sterling</option>
<option value="0840"{if $module_data.param03 eq "0840"} selected="selected"{/if}>US Dollars</option>
<option value="0978"{if $module_data.param03 eq "0978"} selected="selected"{/if}>Euro</option>
<option value="0392"{if $module_data.param03 eq "0392"} selected="selected"{/if}>Japanese Yen</option>
</select>
</td>
</tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
