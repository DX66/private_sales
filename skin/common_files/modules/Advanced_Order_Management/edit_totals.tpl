{*
$Id: edit_totals.tpl,v 1.4.2.2 2011/01/04 15:55:56 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
{literal}
function MarkElement(type) {
  if (document.edittotals_form.elements['total_details['+type+'_alt]'] && document.edittotals_form.elements['total_details[use_'+type+'_alt]']) { 
      document.edittotals_form.elements['total_details['+type+'_alt]'].disabled = !document.edittotals_form.elements['total_details[use_'+type+'_alt]'].checked;
    if (type == 'discount')
      document.edittotals_form.elements['total_details[discount_type_alt]'].disabled = !document.edittotals_form.elements['total_details[use_'+type+'_alt]'].checked;
    else if (type == 'coupon_discount')
      document.edittotals_form.elements['total_details[coupon_alt]'].disabled = document.edittotals_form.elements['total_details[use_'+type+'_alt]'].checked;
    else if (type == 'payment')
      document.edittotals_form.elements['total_details[payment_alt]'].disabled = !document.edittotals_form.elements['total_details[use_'+type+'_alt]'].checked;
  }
}

{/literal}
//]]>
</script>

{capture name=dialog}

<form action="order.php" method="post" name="edittotals_form">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="update_totals" />
<input type="hidden" name="show" value="totals" />
<input type="hidden" name="orderid" value="{$orderid}" />

<table cellspacing="1" cellpadding="5" width="100%">

<tr> 
  <td colspan="3">{include file="main/subheader.tpl" title=$lng.lbl_order_info}</td>
</tr>

{if $config.Taxes.display_taxed_order_totals eq "Y"}
<tr>
  <td colspan="3">{$lng.txt_taxed_order_totals_displayed}</td>
</tr>
{/if}

<tr>
  <td colspan="3">{include file="customer/main/cart_details.tpl" products=$cart_products}<br /></td>
</tr>

{if $shipping_lost}
<tr>
  <td colspan="3">
{assign var="t_ship_method" value=$orig_order.shipping|trademark:'use_alt'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.lbl_aom_unaccessible_shipmethod|substitute:"t_ship_method":$t_ship_method}
<br /><br />
  </td>
</tr>
{/if}

{if $config.Shipping.enable_shipping eq "Y" and $active_modules.UPS_OnLine_Tools and $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y" and $show_carriers_selector eq "Y"}
<tr>
  <td colspan="3">
<font class="FormButton">{$lng.lbl_aom_shipping_carrier}: </font>
<select name="selected_carrier" onchange="document.edittotals_form.submit()">
<option value="UPS"{if $current_carrier eq "UPS"} selected="selected"{/if}>{$lng.lbl_ups_carrier}</option>
<option value=""{if $current_carrier ne "UPS"} selected="selected"{/if}>{$lng.lbl_other_carriers}</option>
</select>
<br /><br />
</td>
</tr>
{/if}

<tr class="TableHead">
  <td height="16">&nbsp;</td>
  <th height="16" align="left">{$lng.lbl_aom_current_value}</th>
  <th height="16" align="left">{$lng.lbl_aom_original_value}</th>
</tr>

<tr {cycle values=', class="TableSubHead"'}> 
  <td>{$lng.lbl_payment_method}</td>
  <td><input type="text" size="30" maxlength="50" name="total_details[payment_method]" value="{$cart_order.payment_method|escape}" /><br />
  {$lng.lbl_other}:<input type="checkbox" name="total_details[use_payment_alt]" onclick="javascript: MarkElement('payment')" />
  <select name="total_details[payment_alt]" disabled="disabled">
{section name=pm loop=$payment_methods}
  <option value="{$payment_methods[pm].paymentid}:::{$payment_methods[pm].payment_method}"{if $payment_methods[pm].paymentid eq $cart_order.paymentid} selected="selected"{/if}>{$payment_methods[pm].payment_method}</option>
{/section}
  </select></td>
  <td>{$orig_order.payment_method}</td>
</tr>

{if $shipping_calc_error ne ""}
<tr class="TableHead">
  <td colspan="3">{$shipping_calc_service} {$lng.lbl_err_shipping_calc}<br /><font class="ErrorMessage">{$shipping_calc_error}</font>
</tr>
{/if}

<tr{cycle values=', class="TableSubHead"'}> 
  <td>{$lng.lbl_delivery}</td>
  <td>
  {if $shipping}
  <select name="total_details[shippingid]">
  <option value="0">{$lng.lbl_aom_shipmethod_notavail}</option>
  {section name=ship_num loop=$shipping}
  <option value="{$shipping[ship_num].shippingid}"{if $shipping[ship_num].shippingid eq $cart_order.shippingid} selected="selected"{/if}>{$shipping[ship_num].shipping|trademark:"use_alt"}{if $config.Appearance.display_shipping_cost eq "Y"} ({currency value=$shipping[ship_num].rate plain_text_message=1}){/if}</option>
  {/section}
  </select>
  {else}
  {$lng.lbl_aom_shipmethod_notavail}
  {/if}
  </td>
  <td>{$orig_order.shipping|trademark|default:$lng.lbl_aom_shipmethod_notavail}</td>
</tr>

{if $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y" and (not $active_modules.UPS_OnLine_Tools or $show_carriers_selector ne 'Y' or $current_carrier ne 'UPS') and $dhl_ext_countries and $has_active_arb_smethods}
<tr{cycle values=', class="TableSubHead"'}>
  <td height="18">{$lng.lbl_dhl_ext_country}</td>
  <td>
<select name="dhl_ext_country" id="dhl_ext_country">
  <option value="">{$lng.lbl_please_select_one}</option>
{foreach from=$dhl_ext_countries item=c}
  <option value="{$c}"{if $c eq $dhl_ext_country} selected="selected"{/if}>{$c}</option>
{/foreach}
</select>
  </td>
  <td>{$orig_order.extra.dhl_ext_country}</td>
</tr>
{/if}

<tr{cycle values=', class="TableSubHead"'}>
  <td height="18">{$lng.lbl_subtotal}</td>
  <td>{currency value=$cart_order.display_subtotal}</td>
  <td>{currency value=$orig_order.display_subtotal}</td>
</tr>

<tr{cycle values=', class="TableSubHead"' advance=false}>
  <td>{$lng.lbl_discount}</td>
  <td>{currency value=$cart_order.discount}</td>
  <td rowspan="2">{currency value=$orig_order.discount}{if $orig_order.discount gt 0 and $orig_order.extra.discount_info.discount_type eq "percent"} ({$orig_order.extra.discount_info.discount}%){/if}</td>
</tr>

<tr{cycle values=', class="TableSubHead"'}>
  <td>{$lng.lbl_aom_new_discount}</td>
  <td><input type="checkbox" name="total_details[use_discount_alt]" onclick="javascript: MarkElement('discount')"{if $cart_order.use_discount_alt eq 'Y' and $cart_order.extra.discount_info.discount gt 0} checked="checked"{/if} />
<input type="text" size="12" maxlength="12" name="total_details[discount_alt]" value="{$cart_order.extra.discount_info.discount|escape}"{if $cart_order.use_discount_alt ne 'Y' or $cart_order.extra.discount_info.discount eq 0} disabled="disabled"{/if} />
  <select name="total_details[discount_type_alt]"{if $cart_order.use_discount_alt ne 'Y'} disabled="disabled"{/if}>
  <option value="percent"{if $cart_order.extra.discount_info.discount_type eq "percent"} selected="selected"{/if}>%</option>
  <option value="absolute"{if $cart_order.extra.discount_info.discount_type eq "absolute"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
  </select>
  </td>
</tr>

<tr{cycle values=', class="TableSubHead"' advance=false}>
  <td>{$lng.lbl_coupon_saving}</td>
  <td>
{currency value=$cart_order.coupon_discount} (
<select name="total_details[coupon_alt]"{if $cart_order.use_coupon_discount_alt eq 'Y' and not $cart_order.use_old_coupon_discount} disabled="disabled"{/if}>
  <option value="">{$lng.lbl_none}</option>
{foreach from=$coupons item=v}
  <option value="{if $v.__deleted}__old_coupon__{else}{$v.coupon|escape}{/if}"{if $cart_order.coupon eq $v.coupon or ($cart_order.use_coupon_discount_alt eq 'Y' and not $cart_order.use_old_coupon_discount and $cart_order.__last_coupon eq $v.coupon)} selected="selected"{/if}>{$v.coupon} -{$v.discount}{if $v.coupon_type eq 'percent'}%{else}{$config.General.currency_symbol}{/if}{if $v.__deleted and $v.coupon_type neq "free_ship"} ({$lng.lbl_aom_coupon_not_found|wm_remove|escape}){/if}</option>
{/foreach}
</select>
)
  </td>
  <td>{currency value=$orig_order.coupon_discount}{if $orig_order.coupon ne ""} ({$orig_order.coupon}){/if}</td>
</tr>
<tr{cycle values=', class="TableSubHead"'}>
  <td>{$lng.lbl_new_coupon_saving}</td>
  <td><input type="checkbox" name="total_details[use_coupon_discount_alt]" onclick="javascript: MarkElement('coupon_discount')"{if $cart_order.use_coupon_discount_alt eq 'Y' and not $cart_order.use_old_coupon_discount} checked="checked"{/if} />
  <input type="text" size="12" maxlength="12" id="coupon_discount_alt" name="total_details[coupon_discount_alt]" value="{$cart_order.coupon_discount|escape}"{if $cart_order.use_coupon_discount_alt ne 'Y' or $cart_order.use_old_coupon_discount} disabled="disabled"{/if} /></td>
  <td>&nbsp;</td>
</tr>

{if $order.discounted_subtotal ne $order.subtotal}
<tr{cycle values=', class="TableSubHead"'}>
  <td>{$lng.lbl_discounted_subtotal}</td>
  <td>{currency value=$cart_order.display_discounted_subtotal}</td>
  <td>{currency value=$orig_order.display_discounted_subtotal}</td>
</tr>
{/if}

{if $cart_order.coupon_type eq "free_ship"}
{assign var="shipping_cost" value=0}
{*math equation="x+y" x=$cart_order.shipping_cost y=$cart_order.coupon_discount assign="shipping_cost"*}
{else}
{assign var="shipping_cost" value=$cart_order.display_shipping_cost}
{/if}
{if $cart_order.coupon_type eq "free_ship"}
<tr{cycle values=', class="TableSubHead"'}>
{else}
<tr{cycle values=', class="TableSubHead"' advance=false}>
{/if}
  <td>{$lng.lbl_shipping_cost}</td>
  <td>
{if $order.coupon and $order.coupon_type eq "free_ship"}
{currency value=0}&nbsp;({$lng.lbl_free_ship_coupon_record|substitute:"code":$order.coupon})
{else}
{currency value=$shipping_cost}
{/if}
  </td>
  <td>
{if $orig_order.coupon and $orig_order.coupon_type eq "free_ship"}
{currency value=0}&nbsp;({$lng.lbl_free_ship_coupon_record|substitute:"code":$orig_order.coupon})
{else}
{currency value=$orig_order.display_shipping_cost}
{/if}
  </td>
</tr>

{if $cart_order.coupon_type ne "free_ship"}
{assign var="note_shipping_cost" value="1"}
<tr{cycle values=', class="TableSubHead"'}>
  <td align="right">{$lng.lbl_aom_use_fixed_shipping}*</td>
  <td><input type="checkbox" name="total_details[use_shipping_cost_alt]" value="Y"{if $cart_order.use_shipping_cost_alt eq "Y"} checked="checked"{/if} onclick="javascript:MarkElement('shipping_cost')" /><input type="text" size="15" maxlength="15" name="total_details[shipping_cost_alt]" value="{$cart_order.shipping_cost_alt|formatprice}"{if $cart_order.use_shipping_cost_alt eq ""} disabled="disabled"{/if} /></td>
  <td>&nbsp;</td>
</tr>
{/if}

{if ($orig_order.applied_taxes or $cart_order.taxes) and $config.Taxes.display_taxed_order_totals ne "Y"}
<tr{cycle values=', class="TableSubHead"'}>
  <td>
{foreach key=tax_name item=tax from=$cart_order.taxes}
{$tax.tax_display_name}({$tax.formula}){if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:<br />
{/foreach}
  </td>
  <td>
{foreach key=tax_name item=tax from=$cart_order.taxes}
{currency value=$tax.tax_cost}<br />
{/foreach}
  </td>
  <td nowrap="nowrap">
{foreach key=tax_name item=tax from=$orig_order.applied_taxes}
{$tax.tax_display_name}({$tax.formula}){if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}: {currency value=$tax.tax_cost}<br />
{/foreach}
  </td>
</tr>
{/if}

{if $order.payment_surcharge}
<tr{cycle values=', class="TableSubHead"'}>
  <td class="LabelStyle" nowrap="nowrap">{if $order.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}</td>
  <td>{currency value=$cart_order.payment_surcharge}</td>
  <td>{currency value=$orig_order.payment_surcharge}</td>
</tr>
{/if}

{if $order.giftcert_discount gt 0}
<tr{cycle values=', class="TableSubHead"'}>
  <td class="LabelStyle" nowrap="nowrap">{$lng.lbl_giftcert_discount}</td>
  <td>{currency value=$cart_order.giftcert_discount}</td>
  <td>{currency value=$orig_order.giftcert_discount}</td>
</tr>
{/if}

<tr{cycle values=', class="TableSubHead"'}>
  <td><b style="text-transform: uppercase;">{$lng.lbl_total}</b></td>
  <td><b>{currency value=$cart_order.total}</b></td>
  <td><b>{currency value=$orig_order.total}</b></td>
</tr>

{if ($orig_order.applied_taxes or $cart_order.taxes) and $config.Taxes.display_taxed_order_totals eq "Y"}
<tr>
  <td colspan="2"><b>{$lng.lbl_including}:</b></td>
  <td><b>{$lng.lbl_including}:</b></td>
</tr>
<tr class="TableSubHead">
  <td>
{foreach key=tax_name item=tax from=$cart_order.taxes}
{$tax.tax_display_name}({$tax.formula}){if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:<br />
{/foreach}
  </td>
  <td>
{foreach key=tax_name item=tax from=$cart_order.taxes}
{currency value=$tax.tax_cost}<br />
{/foreach}
  </td>
  <td nowrap="nowrap">
{foreach key=tax_name item=tax from=$orig_order.applied_taxes}
{$tax.tax_display_name}({$tax.formula}){if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}: {currency value=$tax.tax_cost}<br />
{/foreach}
  </td>
</tr>
{/if}

{section name=rn loop=$cart_order.reg_numbers}
{if %rn.first%}
<tr{cycle values=', class="TableSubHead"' advance=false}>
  <td valign="top" colspan="3" class="LabelStyle" nowrap="nowrap">{$lng.lbl_registration_number}:  </td>
</tr>
{/if}

<tr{cycle values=', class="TableSubHead"'}>
  <td valign="top" colspan="3" nowrap="nowrap">&nbsp;&nbsp;{$cart_order.reg_numbers[rn]}</td>
</tr>
{/section}

{if $cart_order.applied_giftcerts}
<tr>
  <td colspan="3" height="14">&nbsp;</td>
</tr>

<tr class="TableHead">
  <td colspan="3" height="16" class="LabelStyle"><b>{$lng.lbl_applied_giftcerts}:</b></td>
</tr>

{section name=gc loop=$cart_order.applied_giftcerts}
<tr{cycle values=' class="TableSubHead,"'}>
  <td>&nbsp;&nbsp;{$cart_order.applied_giftcerts[gc].giftcert_id}:</td>
  <td>{currency value=$cart_order.applied_giftcerts[gc].giftcert_cost}</td>
  <td>{currency value=$orig_order.applied_giftcerts[gc].giftcert_cost}</td>
</tr>
{/section}
{/if}

<tr>
<td colspan="3"><br />
<input type="submit" value="{$lng.lbl_update}" />
<br /><br />
</td>
</tr>

</table>
</form>

{if $note_shipping_cost ne ""}
{$lng.lbl_aom_use_fixed_shipping_note}
{if ($orig_order.applied_taxes or $cart_order.taxes) and $config.Taxes.display_taxed_order_totals eq "Y"}
<br />
{$lng.lbl_aom_use_fixed_shipping_note2}
{/if}
{/if}

{if $display_ups_trademarks and $current_carrier eq "UPS"}
<br />
{include file="modules/UPS_OnLine_Tools/ups_notice.tpl"}
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_aom_edit_totals_title|substitute:"orderid":$order.orderid content=$smarty.capture.dialog extra='width="100%"'}
