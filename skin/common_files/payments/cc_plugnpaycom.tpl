{*
$Id: cc_plugnpaycom.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>Plug'n'Pay</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_plugnpaycom_publisher}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_plugnpaycom_host}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /><br />
{$lng.lbl_cc_plugnpaycom_host_note}
</td>
</tr>

<tr>
<td valign="top">{$lng.lbl_cc_plugnpaycom_avs}:</td>
<td>
<input type="radio" name="param05" value="0"{if $module_data.param05 eq "0"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_0}<br />
<input type="radio" name="param05" value="1"{if $module_data.param05 eq "1"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_1}<br />
<input type="radio" name="param05" value="3"{if $module_data.param05 eq "3"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_3}<br />
<input type="radio" name="param05" value="4"{if $module_data.param05 eq "4"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_4}<br />
<input type="radio" name="param05" value="5"{if $module_data.param05 eq "5"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_5}<br />
<input type="radio" name="param05" value="6"{if $module_data.param05 eq "6"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_6}<br />
</td><br />
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}{$lng.lbl_cc_plugnpaycom_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
</table>
<br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
