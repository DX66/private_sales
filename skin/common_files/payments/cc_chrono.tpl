{*
$Id: cc_chrono.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_chronopay_note|substitute:"http_location":$current_location:"processor":$module_data.processor}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table border="0" cellspacing="10">
<tr>
<td>{$lng.txt_cc_chronopay_pid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01}" /></td>
</tr>
<tr>
<td>{$lng.txt_cc_chronopay_key}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02}" /></td>
</tr>
<tr>
<td>{$lng.txt_cc_chronopay_currency}:</td>
<td>
<select name="param03">
<option value="USD"{if $module_data.param03 eq "USD"} selected{/if}>USD</option>
<option value="EUR"{if $module_data.param03 eq "EUR"} selected{/if}>EUR</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_language}:</td>
<td>
<select name="param04">
<option value="EN"{if $module_data.param04 eq "EN"} selected{/if}>English</option>
<option value="RU"{if $module_data.param04 eq "RU"} selected{/if}>Russian</option>
<option value="NL"{if $module_data.param04 eq "NL"} selected{/if}>Dutch</option>
<option value="ES"{if $module_data.param04 eq "ES"} selected{/if}>Spanish</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param05" size="32" value="{$module_data.param05}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
