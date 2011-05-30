{*
$Id: cc_delta.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h3>DeltaPAY</h3>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_delptapay_request_page}:</td>
<td>{$http_location}/payment/{$module_data.processor}</td>
</tr>
<tr>
<td>{$lng.lbl_cc_delta_merchant}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param03">
<option value="040"{if $module_data.param03 eq "040"} selected="selected"{/if}>Austrian Shilling</option>
<option value="056"{if $module_data.param03 eq "056"} selected="selected"{/if}>Belgian Franc</option>
<option value="250"{if $module_data.param03 eq "250"} selected="selected"{/if}>French Franc</option>
<option value="300"{if $module_data.param03 eq "300"} selected="selected"{/if}>Greek Dragmen</option>
<option value="280"{if $module_data.param03 eq "280"} selected="selected"{/if}>Deutsche Mark</option>
<option value="380"{if $module_data.param03 eq "380"} selected="selected"{/if}>Italian Lira</option>
<option value="442"{if $module_data.param03 eq "442"} selected="selected"{/if}>Luxembourg Franc</option>
<option value="528"{if $module_data.param03 eq "528"} selected="selected"{/if}>Netherlands Guilder</option>
<option value="724"{if $module_data.param03 eq "724"} selected="selected"{/if}>Spanish Peseta</option>
<option value="756"{if $module_data.param03 eq "756"} selected="selected"{/if}>Swiss Francs</option>
<option value="826"{if $module_data.param03 eq "826"} selected="selected"{/if}>Sterling</option>
<option value="840"{if $module_data.param03 eq "840"} selected="selected"{/if}>US Dollars</option>
<option value="978"{if $module_data.param03 eq "978"} selected="selected"{/if}>Euro</option>
<option value="392"{if $module_data.param03 eq "392"} selected="selected"{/if}>Japanese Yen</option>
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
