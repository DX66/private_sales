{*
$Id: shipping_rates.tpl,v 1.3 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $type eq "D"}
{include file="page_title.tpl" title=$lng.lbl_shipping_charges}
{$lng.txt_shipping_charges_note|substitute:"weight_symbol":$config.General.weight_symbol}
{else}
{include file="page_title.tpl" title=$lng.lbl_shipping_markups}
{$lng.txt_shipping_markups_note|substitute:"weight_symbol":$config.General.weight_symbol}
{/if}

<form action="shipping_rates.php" method="get" name="zoneform">

<input type="hidden" name="type" value="{$type|escape}" />

<b>{if $type eq "D"}{$lng.lbl_edit_charges_for}{else}{$lng.lbl_edit_markups_for}{/if}</b><br />

<select name="shippingid" onchange="document.zoneform.submit()">
  <option value="">{$lng.lbl_all_methods}</option>
{section name=ship_num loop=$shipping}
  <option value="{$shipping[ship_num].shippingid}"{if $smarty.get.shippingid ne "" and $smarty.get.shippingid eq $shipping[ship_num].shippingid} selected="selected"{/if}>{$shipping[ship_num].shipping|trademark} ({if $shipping[ship_num].destination eq "I"}{$lng.lbl_intl|wm_remove|escape}{else}{$lng.lbl_national|wm_remove|escape}{/if})</option>
{/section}
</select>

<select name="zoneid" onchange="document.zoneform.submit()">
  <option value="">{$lng.lbl_all_zones}</option>
{section name=zone loop=$zones}
  <option value="{$zones[zone].zoneid}"{if $smarty.get.zoneid ne "" and $smarty.get.zoneid eq $zones[zone].zoneid} selected="selected"{/if}>{$zones[zone].zone}</option>
{/section}
</select>

</form>

<br /><br />

{capture name=dialog}

{if $shipping_rates_avail gt 0}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'shippingratesform';
checkboxes = new Array({section name=zone loop=$zones_list}{foreach key=shipid item=shipping_method from=$zones_list[zone].shipping_methods}{if $comma ne ""},{else}{assign var="comma" value=1}{/if}'sm_{$zones_list[zone].zone.zoneid}_{$shipid}'{section name=rate loop=$shipping_method.rates},'posted_data[{$shipping_method.rates[rate].rateid}][to_delete]'{/section}{/foreach}{/section});
//]]>  
</script> 
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td><div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div></td>
  <td align="right">
{if $type eq "D"}{include file="buttons/button.tpl" button_title=$lng.lbl_add_shipping_charge_values href="#addrate"}{else}{include file="buttons/button.tpl" button_title=$lng.lbl_add_shipping_markup_values href="#addrate"}{/if}
  </td>
</tr>
</table>

<br /><br />

<form action="shipping_rates.php" method="post" name="shippingratesform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="zoneid" value="{$smarty.get.zoneid|escape:"html"}" />
<input type="hidden" name="shippingid" value="{$smarty.get.shippingid|escape:"html"}" />
<input type="hidden" name="type" value="{$type|escape}" />

<table cellpadding="0" cellspacing="1" width="100%">

{* $zones_list = array("zone"=>array(...), "shipping_methods"=>array(...)) *}
{section name=zone loop=$zones_list}

{if $zones_list[zone].shipping_methods}

<tr>
  <td>{include file="main/subheader.tpl" title=$zones_list[zone].zone.zone class="black"}</td>
</tr>

{capture name=rates_list}
{foreach key=shipid item=shipping_method from=$zones_list[zone].shipping_methods}
{* $shipping_method = array(array("shippingid"=>..., "shipping"=>..., "rates"=>array(...))) *}

<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr class="TableSubHead">
  <td>
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes{$zones_list[zone].zone.zoneid}_{$shipid} = new Array({section name=rate loop=$shipping_method.rates}{if not %rate.first%},{/if}'posted_data[{$shipping_method.rates[rate].rateid}][to_delete]'{/section});
//]]>  
</script> 
<table cellpadding="2" cellspacing="0" width="100%">
<tr>
  <td><input type="checkbox" id="sm_{$zones_list[zone].zone.zoneid}_{$shipid}" name="sm_{$zones_list[zone].zone.zoneid}_{$shipid}" onclick="javascript:change_all(this.checked, checkboxes_form, checkboxes{$zones_list[zone].zone.zoneid}_{$shipid});" /></td>
  <td><b><label for="sm_{$zones_list[zone].zone.zoneid}_{$shipid}">{$shipping_method.shipping|trademark} ({if $shipping_method.destination eq "I"}{$lng.lbl_intl|escape}{else}{$lng.lbl_national|escape}{/if})</label></b></td>
  <td align="right">
    {$lng.lbl_apply_rates_to}:
    <select name="apply_to[{$zones_list[zone].zone.zoneid}][{$shipid}]">
    <option value="DST"{if $shipping_method.apply_to eq "DST" or $shipping_method.apply_to eq ""} selected="selected"{/if}>DST ({$lng.lbl_discounted_subtotal|wm_remove|escape})</option>
    <option value="ST"{if $shipping_method.apply_to eq "ST"} selected="selected"{/if}>ST ({$lng.lbl_subtotal|wm_remove|escape})</option>
    </select>
  </td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
  <td>

<table cellpadding="0" cellspacing="3" width="100%">

{section name=rate loop=$shipping_method.rates}
{assign var="shipping_rate" value=$shipping_method.rates[rate]}

<tr>
  <td rowspan="2" nowrap="nowrap"><img src="{$ImagesDir}/spacer.gif" width="10" height="1" alt="" /><input type="checkbox" name="posted_data[{$shipping_rate.rateid}][to_delete]" /></td>
  <td>{$lng.lbl_weight_range}:</td>
  <td nowrap="nowrap">
<input type="text" name="posted_data[{$shipping_rate.rateid}][minweight]" size="9" value="{$shipping_rate.minweight|formatprice}" />
-
<input type="text" name="posted_data[{$shipping_rate.rateid}][maxweight]" size="9" value="{$shipping_rate.maxweight|formatprice}" />
  </td>
  <td>{$lng.lbl_flat_charge} ({$config.General.currency_symbol}):</td>
  <td nowrap="nowrap"><input type="text" name="posted_data[{$shipping_rate.rateid}][rate]" size="5" value="{$shipping_rate.rate|formatprice}" /></td>
  <td>{$lng.lbl_percent_charge}:</td>
  <td><input type="text" name="posted_data[{$shipping_rate.rateid}][rate_p]" size="5" value="{$shipping_rate.rate_p|formatprice}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_subtotal_range}:</td>
  <td nowrap="nowrap">
<input type="text" name="posted_data[{$shipping_rate.rateid}][mintotal]" size="9" value="{$shipping_rate.mintotal|default:0|formatprice}" />
-
<input type="text" name="posted_data[{$shipping_rate.rateid}][maxtotal]" size="9" value="{$shipping_rate.maxtotal|formatprice}" />
  </td>
  <td>{$lng.lbl_per_item_charge} ({$config.General.currency_symbol}):</td>
  <td nowrap="nowrap"><input type="text" name="posted_data[{$shipping_rate.rateid}][item_rate]" size="5" value="{$shipping_rate.item_rate|formatprice}" /></td>
  <td>{$lng.lbl_per_weight_charge|substitute:"weight":$config.General.weight_symbol} ({$config.General.currency_symbol}):</td>
  <td nowrap="nowrap"><input type="text" name="posted_data[{$shipping_rate.rateid}][weight_rate]" size="5" value="{$shipping_rate.weight_rate|formatprice}" /></td>
</tr>

{if not %rate.last%}
<tr>
  <td colspan="7" class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{/if}

{/section}

</table>
  </td>
</tr>

{/foreach}
{/capture}

{if $smarty.capture.rates_list}
{$smarty.capture.rates_list}
<tr>
  <td>&nbsp;</td>
</tr>
{else}
<tr>
  <td>{if $type eq "D"}{$lng.lbl_no_shipping_rates_defined}{else}{$lng.lbl_no_shipping_markups_defined}{/if}</td>
</tr>
{/if}

{/if}

{/section}

<tr>
  <td>
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('posted_data\\[[0-9]+\\]\\[to_delete\\]', 'gi'))) submitForm(this, 'delete');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>

<br /><br /><br />

<a name="addrate"></a>

{/if}

<br />
{if $type eq "D"}{include file="main/subheader.tpl" title=$lng.lbl_add_shipping_charge_values}{else}{include file="main/subheader.tpl" title=$lng.lbl_add_shipping_markup_values}{/if}

{if $shipping ne ""}

<form action="shipping_rates.php" method="post" name="addshippingrate">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="zoneid" value="{$zoneid}" />
<input type="hidden" name="shippingid" value="{$shippingid}" />
<input type="hidden" name="type" value="{$type|escape}" />

<table cellpadding="0" cellspacing="3">

<tr>
  <td><b>{$lng.lbl_shipping_method}:</b></td>
  <td>&nbsp;</td>
  <td>
  <select name="shippingid_new">
    <option value="">{$lng.lbl_select_one}</option>
{section name=ship_num loop=$shipping}
    <option value="{$shipping[ship_num].shippingid}">{$shipping[ship_num].shipping|trademark} ({if $shipping[ship_num].destination eq "I"}{$lng.lbl_intl|wm_remove|escape}{else}{$lng.lbl_national|wm_remove|escape}{/if})</option>
{/section}
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_zone}:</b></td>
  <td>&nbsp;</td>
  <td>
  <select name="zoneid_new">
{section name=zone loop=$zones}
    <option value="{$zones[zone].zoneid}"{if $smarty.get.zoneid eq $zones[zone].zoneid} selected="selected"{/if}>{$zones[zone].zone}</option>
{/section}
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_apply_rate_to}:</b></td>
  <td>&nbsp;</td>
  <td>
  <select name="apply_to_new">
    <option value="DST" selected="selected">DST ({$lng.lbl_discounted_subtotal|wm_remove|escape})</option>
    <option value="ST">ST ({$lng.lbl_subtotal|wm_remove|escape})</option>
  </select>
  </td>
</tr>

</table>

<table cellpadding="0" cellspacing="3" width="100%">

<tr>
  <td><b>{$lng.lbl_weight_range}:</b></td>
  <td nowrap="nowrap">
<input type="text" name="minweight_new" size="9" value="{0|formatprice}" />
-
<input type="text" name="maxweight_new" size="9" value="{$maxvalue|formatprice}" />
  </td>
  <td><b>{$lng.lbl_flat_charge} ({$config.General.currency_symbol}):</b></td>
  <td nowrap="nowrap"><input type="text" name="rate_new" size="5" value="{0|formatprice}" /></td>
  <td><b>{$lng.lbl_percent_charge}:</b></td>
  <td><input type="text" name="rate_p_new" size="5" value="{0|formatprice}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_subtotal_range}:</b></td>
  <td nowrap="nowrap">
<input type="text" name="mintotal_new" size="9" value="{0|formatprice}" />
-
<input type="text" name="maxtotal_new" size="9" value="{$maxvalue|formatprice}" />
  </td>
  <td><b>{$lng.lbl_per_item_charge} ({$config.General.currency_symbol}):</b></td>
  <td nowrap="nowrap"><input type="text" name="item_rate_new" size="5" value="{0|formatprice}" /></td>
  <td><b>{$lng.lbl_per_weight_charge|substitute:"weight":$config.General.weight_symbol} ({$config.General.currency_symbol}):</b></td>
  <td nowrap="nowrap"><input type="text" name="weight_rate_new" size="5" value="{0|formatprice}" /></td>
</tr>

</table>

<br />
<input type="submit" value=" {$lng.lbl_add|strip_tags:false|escape} " />

</form>

{elseif $type eq "D"}

{$lng.txt_shipping_charge_rtc_note}

{/if}

{/capture}
{if $type eq "D"}
{include file="dialog.tpl" title=$lng.lbl_shipping_charges content=$smarty.capture.dialog extra='width="100%"'}
{else}
{include file="dialog.tpl" title=$lng.lbl_shipping_markups content=$smarty.capture.dialog extra='width="100%"'}
{/if}

