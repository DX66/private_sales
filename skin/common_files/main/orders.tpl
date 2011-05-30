{*
$Id: orders.tpl,v 1.5.2.1 2010/09/28 11:48:01 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_orders_management}

<br />

{if $orders ne ""}
{if $is_admin_user}
{$lng.txt_adm_search_orders_result_header}
{elseif $usertype eq "P"}
{$lng.txt_search_orders_header}
{elseif $usertype eq "C"}
{$lng.txt_search_orders_header}
{/if}
{else}
{$lng.txt_search_orders_header}
{/if}
<br />

{if $mode ne "search" or $orders eq ""}

<br /><br />

<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var txt_delete_orders_warning = "{$lng.txt_delete_orders_warning|wm_remove|escape:javascript|strip_tags}";
var searchform_def = [
  ['posted_data[date_period]', true],
  ['f_start_date', '{$smarty.now|date_format:$config.Appearance.date_format}'],
  ['f_end_date', '{$smarty.now|date_format:$config.Appearance.date_format}'],
  ['StartYear', '{$smarty.now|date_format:"%Y"}'],
  ['EndDay', '{$smarty.now|date_format:"%d"}'],
  ['EndMonth', '{$smarty.now|date_format:"%m"}'],
  ['EndYear', '{$smarty.now|date_format:"%Y"}'],
  ['posted_data[total_min]', '{$zero}'],
  ['posted_data[total_max]', ''],
  ['posted_data[by_title]', true],
  ['posted_data[by_options]', true],
  ['posted_data[price_min]', '{$zero}'],
  ['posted_data[price_max]', ''],
  ['posted_data[address_type]', ''],
  ['posted_data[is_export]', ''],
  ['posted_data[orderid1]', ''],
  ['posted_data[orderid2]', ''],
  ['posted_data[paymentid]', ''],
  ['posted_data[product_substring]', ''],
  ['posted_data[features][]', ''],
  ['posted_data[provider]', ''],
  ['posted_data[shipping_method]', ''],
  ['posted_data[productcode]', ''],
  ['posted_data[productid]', ''],
  ['posted_data[customer]', ''],
  ['posted_data[by_username]', true],
  ['posted_data[by_firstname]', true],
  ['posted_data[by_lastname]', true],
  ['posted_data[company]', ''],
  ['posted_data[city]', ''],
  ['posted_data[state]', ''],
  ['posted_data[country]', ''],
  ['posted_data[zipcode]', ''],
  ['posted_data[phone]', ''],
  ['posted_data[email]', ''],
  ['posted_data[one_return_customer]', ''],
  ['posted_data[status]', '']
];
{literal}
function managedate(type, status) {
  if (type != 'date')
    var fields = ['posted_data[city]','posted_data[state]','posted_data[country]','posted_data[zipcode]'];
  else
    var fields = ['f_start_date', 'f_end_date'];
  
  for (i in fields) {
    if (document.searchform.elements[fields[i]])
      document.searchform.elements[fields[i]].disabled = status;
  }
}
{/literal}
//]]>
</script>

{capture name=dialog}
<a name="SearchOrders"></a>
<form name="searchform" action="orders.php" method="post">
<input type="hidden" name="mode" value="" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
  <td colspan="2">
{$lng.txt_search_orders_text}
<br /><br />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_date_period}:</td>
  <td>

<table cellpadding="2" cellspacing="2">

<tr>
  <td width="5"><input type="radio" id="date_period_null" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td class="OptionLabel" colspan="2"><label for="date_period_null">{$lng.lbl_all_dates}</label></td>
</tr>

<tr>
  <td width="5"><input type="radio" id="date_period_M" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td class="OptionLabel" colspan="2"><label for="date_period_M">{$lng.lbl_this_month}</label></td>
</tr>

<tr>
  <td width="5"><input type="radio" id="date_period_W" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td class="OptionLabel" colspan="2"><label for="date_period_W">{$lng.lbl_this_week}</label></td>
</tr>

<tr>
  <td width="5"><input type="radio" id="date_period_D" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td class="OptionLabel" colspan="2"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>

<tr>
  <td width="5"><input type="radio" id="date_period_C" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" /></td>
  <td class="OptionLabel" align="right"><label for="date_period_C">{$lng.lbl_from}:</label></td>
  <td>{include file="main/datepicker.tpl" name="start_date" date=$search_prefilled.start_date|default:$start_date}</td>
</tr>

<tr> 
  <td width="5">&nbsp;</td>
  <td class="OptionLabel" align="right"><label>{$lng.lbl_to}:</label></td>
  <td>{include file="main/datepicker.tpl" name="end_date" date=$search_prefilled.end_date|default:$end_date}</td>
</tr>

{if ($usertype eq 'A' and $current_membership_flag ne 'FS') or $usertype eq 'P'}
<tr>
  <td width="5" style="padding-top: 9px;"><input type="checkbox" id="posted_data_is_export" name="posted_data[is_export]" value="Y" /></td>
  <td colspan="2" class="FormButton" nowrap="nowrap" style="padding-top: 9px;">&nbsp;<label for="posted_data_is_export">{$lng.lbl_search_and_export}</label></td>
</tr>
{/if}

</table>

</td>
</tr>

</table>

{if $search_prefilled.date_period ne "C"}
<script type="text/javascript">
//<![CDATA[
$(document).ready( function(){ldelim}
  managedate('date',true);
{rdelim});
//]]>
</script>
{/if}

<br />
{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options extra=' width="100%"'}
<br />

<table cellpadding="0" cellspacing="0" width="100%" style="display: none;" id="box1">
<tr>
  <td>

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
  <td width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_order_id}:</td>
  <td>
<input type="text" name="posted_data[orderid1]" size="10" maxlength="15" value="{$search_prefilled.orderid1|escape}" />
-
<input type="text" name="posted_data[orderid2]" size="10" maxlength="15" value="{$search_prefilled.orderid2|escape}" />
  </td>
</tr>

{if $usertype ne "C"}
<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_total} ({$config.General.currency_symbol}):</td>
  <td>

<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="text" size="10" maxlength="15" name="posted_data[total_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.total_min|formatprice}{/if}" /></td>
  <td>&nbsp;-&nbsp;</td>
  <td><input type="text" size="10" maxlength="15" name="posted_data[total_max]" value="{$search_prefilled.total_max|formatprice}" /></td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_payment_method}:</td>
  <td>
  <select name="posted_data[paymentid]" style="width: 70%;">
    <option value="">&nbsp;</option>
{foreach from=$payment_methods item=pm}
    <option value="{$pm.paymentid}"{if $search_prefilled.paymentid eq $pm.paymentid} selected="selected"{/if}>{$pm.payment_method}</option>
{/foreach}
  </select>
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_delivery}:</td>
  <td>
{if $shipping_methods}
  <select name="posted_data[shipping_method]" style="width:70%">
    <option value="">&nbsp;</option>
{foreach from=$shipping_methods item=s}
    <option value="{$s.shippingid}"{if $search_prefilled.shipping_method eq $s.shippingid} selected="selected"{/if}>{$s.shipping|trademark}</option>
{/foreach}
  </select>
{else}
  {$lng.txt_shipping_methods_is_empty}
{/if}
  </td>
</tr>

{/if}

<tr> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_status}:</td>
  <td>{include file="main/order_status.tpl" status=$search_prefilled.status mode="select" name="posted_data[status]" extended="Y" extra="style='width:70%'" display_preauth=true}</td>
</tr>

{if $usertype ne "C"}
{if $usertype eq "A"}
<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_provider}:</td>
  <td>
    <select name="posted_data[provider]">
      <option value="">{$lng.lbl_all}</option>
    {if $providers}
    {section name=prov loop=$providers}
      <option value="{$providers[prov].id}" {if $search_prefilled.provider eq $providers[prov].id}selected{/if}>{$providers[prov].login} ({$providers[prov].title} {$providers[prov].lastname} {$providers[prov].firstname})</option>
    {/section}
    {/if}
    </select>  
  </td>
</tr>
{/if}

<tr> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_features}:</td>
  <td>
{assign var="features" value=$search_prefilled.features}
  <select name="posted_data[features][]" multiple="multiple" size="7" style="width:70%">
    <option value="gc_applied"{if $features.gc_applied} selected="selected"{/if}>{$lng.lbl_entirely_or_partially_payed_by_gc|strip_tags}</option>
    <option value="discount_applied"{if $features.discount_applied} selected="selected"{/if}>{$lng.lbl_global_discount_applied|strip_tags}</option>
    <option value="coupon_applied"{if $features.coupon_applied} selected="selected"{/if}>{$lng.lbl_discount_coupon_applied|strip_tags}</option>
    <option value="free_ship"{if $features.free_ship} selected="selected"{/if}>{$lng.lbl_free_shipping|strip_tags}</option>
    <option value="free_tax"{if $features.free_tax} selected="selected"{/if}>{$lng.lbl_tax_exempt|strip_tags}</option>
    <option value="gc_ordered"{if $features.gc_ordered} selected="selected"{/if}>{$lng.lbl_gc_purchased|strip_tags}</option>
    <option value="notes"{if $features.notes} selected="selected"{/if}>{$lng.lbl_orders_with_notes_assigned|strip_tags}</option>
  </select><br />
{$lng.lbl_hold_ctrl_key}
  </td>
</tr>

{/if}

{if $usertype ne "C"}

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
  <td>
  <input type="text" name="posted_data[product_substring]" size="30" value="{$search_prefilled.product_substring|escape}" style="width:70%" />
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
  <td>

<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>

  <td width="5"><input type="checkbox" id="posted_data_by_options" name="posted_data[by_options]"{if $search_prefilled eq "" or $search_prefilled.by_options} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_options">{$lng.lbl_options}</label></td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
  <td>
  <input type="text" maxlength="64" name="posted_data[productcode]" value="{$search_prefilled.productcode|escape}" style="width:70%" />
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_productid}#:</td>
  <td>
  <input type="text" maxlength="64" name="posted_data[productid]" value="{$search_prefilled.productid|escape}" style="width:70%" />
  </td>
</tr>

<tr> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}" /></td>
  <td>&nbsp;-&nbsp;</td>
  <td><input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></td>
</tr>
</table>
  </td>
</tr>

{/if}

{if $usertype ne "C"}

<tr> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_customer}:</td>
  <td><input type="text" name="posted_data[customer]" size="30" value="{$search_prefilled.customer|escape}" style="width:70%" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_search_in}:</td>
  <td>
<table cellspacing="0" cellpadding="0">
<tr>
    <td width="5"><input type="checkbox" id="posted_data_by_username" name="posted_data[by_username]"{if $search_prefilled eq "" or $search_prefilled.by_username} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_username">{$lng.lbl_username}</label>&nbsp;&nbsp;</td>

  <td width="5"><input type="checkbox" id="posted_data_by_firstname" name="posted_data[by_firstname]"{if $search_prefilled eq "" or $search_prefilled.by_firstname} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_firstname">{$lng.lbl_first_name}</label>&nbsp;&nbsp;</td>

  <td width="5"><input type="checkbox" id="posted_data_by_lastname" name="posted_data[by_lastname]"{if $search_prefilled eq "" or $search_prefilled.by_lastname} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_lastname">{$lng.lbl_last_name}</label></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_company}:</td>
  <td><input type="text" maxlength="128" name="posted_data[company]" value="{$search_prefilled.company|escape}" style="width:70%" /></td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_search_by_address}:</td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="radio" id="address_type_null" name="posted_data[address_type]" value=""{if $search_prefilled eq "" or $search_prefilled.address_type eq ""} checked="checked"{/if} onclick="javascript:managedate('address',true)" /></td>
  <td class="OptionLabel"><label for="address_type_null">{$lng.lbl_ignore_address}</label></td>

  <td width="5"><input type="radio" id="address_type_B" name="posted_data[address_type]" value="B"{if $search_prefilled.address_type eq "B"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
  <td class="OptionLabel"><label for="address_type_B">{$lng.lbl_billing}</label></td>

  <td width="5"><input type="radio" id="address_type_S" name="posted_data[address_type]" value="S"{if $search_prefilled.address_type eq "S"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
  <td class="OptionLabel"><label for="address_type_S">{$lng.lbl_shipping}</label></td>

  <td width="5"><input type="radio" id="address_type_both" name="posted_data[address_type]" value="Both"{if $search_prefilled.address_type eq "Both"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
  <td class="OptionLabel"><label for="address_type_both">{$lng.lbl_both}</label></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_city}:</td>
  <td><input type="text" maxlength="64" name="posted_data[city]" value="{$search_prefilled.city|escape}" style="width:70%" /></td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_state}:</td>
  <td>{include file="main/states.tpl" states=$states name="posted_data[state]" default=$search_prefilled.state required="N" style="style='width:70%'"}</td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_country}:</td>
  <td>
  <select name="posted_data[country]" style="width:70%">
    <option value="">[{$lng.lbl_please_select_one|wm_remove|escape}]</option>
{section name=country_idx loop=$countries}
    <option value="{$countries[country_idx].country_code}"{if $search_prefilled.country eq $countries[country_idx].country_code} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
  </select>
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_zip_code}:</td>
  <td>
<input type="text" maxlength="32" name="posted_data[zipcode]" value="{$search_prefilled.zipcode|escape}" style="width:70%" />
{if $search_prefilled eq "" or $search_prefilled.address_type eq ""}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
managedate('address',true);
//]]>
</script>
{/if}
  </td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_phone}/{$lng.lbl_fax}:</td>
  <td><input type="text" maxlength="32" name="posted_data[phone]" value="{$search_prefilled.phone|escape}" style="width:70%" /></td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_email}:</td>
  <td><input type="text" maxlength="128" name="posted_data[email]" value="{$search_prefilled.email|escape}" style="width:70%" /></td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_one_return_customer}:</td>
  <td><table><tr>
  <td><input type="radio" id="one_return_customer" name="posted_data[one_return_customer]" value=""{if $search_prefilled.one_return_customer eq ""} checked="checked"{/if} /></td><td><label for="one_return_customer">{$lng.lbl_all}</label></td>
  <td><input type="radio" id="one_return_customerO" name="posted_data[one_return_customer]" value="O"{if $search_prefilled.one_return_customer eq "O"} checked="checked"{/if} /></td><td><label for="one_return_customerO">{$lng.lbl_one_customer}</label></td>
  <td><input type="radio" id="one_return_customerR" name="posted_data[one_return_customer]" value="R"{if $search_prefilled.one_return_customer eq "R"} checked="checked"{/if} /></td><td><label for="one_return_customerR">{$lng.lbl_return_customer}</label></td>
  </tr></table>
  </td>
</tr>

{/if}

</table>

  </td>
</tr>

</table>

<table cellpadding="1" cellspacing="5" width="100%">
  <tr>
    <td class="FormButton normal" width="20%">
      <a href="javascript:void(0);" onclick="javascript: reset_form('searchform', searchform_def);" class="underline">{$lng.lbl_reset_filter}</a>
    </td>
    <td class="main-button">
      <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
    </td>
  </tr>
</table>

</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
visibleBox('1');
//]]>
</script>
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_orders content=$smarty.capture.dialog extra='width="100%"'}

{/if}

{if $mode eq "search"}
<br /><br />
{if $total_items gte "1"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

<br /><br />

{if $orders ne ""}

{include file="main/orders_list.tpl"}

{/if}

{if $usertype ne "C" and $mode ne "search" and $current_membership_flag ne 'FS'}

{capture name=dialog}
<a name="ExportOrders"></a>
<br />

{if $is_admin_user}
{$lng.txt_delete_export_all_orders_note_admin}
{else}
{$lng.txt_delete_export_all_orders_note_provider}
{/if}
<br />
<br />

<form name="ordersform" action="orders.php" method="post">
<input type="hidden" name="mode" value="" />

<table cellpadding="1" cellspacing="5">

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_export_file_format}:</td>
  <td>&nbsp;</td>
  <td>
  <select name="export_fmt">
    <option value="std">{$lng.lbl_standart}</option>
    <option value="csv_tab">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_tab_delimiter}</option>
    <option value="csv_semi">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_semicolon_delimiter}</option>
    <option value="csv_comma">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_comma_delimiter}</option>
{if $active_modules.QuickBooks eq "Y"}
{include file="modules/QuickBooks/orders.tpl"}
{/if}
  </select>
  </td>
  <td><input type="button" value="{$lng.lbl_export_all|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'export_all');" /></td>
</tr>

<tr> 
  <td colspan="4" class="SubmitBox">
{if $usertype eq "A"}
  <input type="button" value="{$lng.lbl_delete_all_orders|strip_tags:false|escape}" onclick="javascript: if (confirm(txt_delete_orders_warning)) submitForm(this, 'delete_all');" />
{/if}
<br />
  </td>
</tr>

</table>
</form>

{/capture}
{if $is_admin_user}
{include file="dialog.tpl" title=$lng.lbl_export_delete_orders content=$smarty.capture.dialog extra='width="100%"'}
{else}
{include file="dialog.tpl" title=$lng.lbl_export_orders content=$smarty.capture.dialog extra='width="100%"'}
{/if}

<br /><br />
{if $active_modules.Order_Tracking}
{include file="main/orders_tracking.tpl"}
{/if}

{/if}

<br /><br />
