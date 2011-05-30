{*
$Id: pconf_order_info.tpl,v 1.1.2.2 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<tr>
<td colspan="2">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td class="SubHeaderGreyLine" width="2"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_pconf_components}</td>
</tr>
{section name=pconf_num loop=$products}
{if $products[pconf_num].deleted eq "" and $active_modules.Product_Configurator ne "" and $products[pconf_num].extra_data.pconf.parent eq $cartid}
<tr>
  <td class="SubHeaderGreyLine" width="2"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td width="100%" colspan="2" valign="top" class="ProductTitle">{$products[pconf_num].product} #{$products[pconf_num].productid}</td>
</tr>
<tr>
  <td class="SubHeaderGreyLine" width="3"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td class="SubHeaderGreyLine" width="2"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top" width="30%">{$lng.lbl_sku}</td>
  <td valign="top" width="70%">{$products[pconf_num].productcode|default:"-"}</td>
</tr>
<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top">{$lng.lbl_provider}</td>
  <td valign="top">{$products[pconf_num].provider_login}</td>
</tr>
<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top">{$lng.lbl_price}</td>
  <td valign="top">{currency value=$products[pconf_num].price}</td>
</tr>

{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y"}
<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top">&nbsp;&nbsp;&nbsp;{$lng.lbl_including}</td>
  <td>
{foreach from=$products[pconf_num].extra_data.taxes key=tax_name item=tax}
{if $tax.tax_value gt 0}
{if $cart.product_tax_name eq ""}<span style="white-space: nowrap;">{$tax.tax_display_name}:</span>{/if}
{if $tax.rate_type eq "%"}{$tax.rate_value}%{else}{currency value=$tax.rate_value}{/if}<br />
{/if}
{/foreach}
  </td>
</tr> 
{/if}

<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top">{$lng.lbl_quantity}</td>
  <td valign="top">{$lng.lbl_n_items|substitute:"items":$products[pconf_num].amount}</td>
</tr>
{if $products[pconf_num].product_options ne ""}
<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top">{$lng.lbl_selected_options}</td>
  <td valign="top">{include file="modules/Product_Options/display_options.tpl" options=$products[pconf_num].product_options}</td>
</tr>
{/if}
<tr>
  <td class="SubHeaderGreyLine" width="3"><img src="{$ImagesDir}/spacer.gif" width="2" height="1" alt="" /></td>
  <td colspan="3">&nbsp;</td>
</tr>
{/if}
{/section}
<tr>
  <td class="SubHeaderGreyLine" colspan="4"><img src="{$ImagesDir}/spacer.gif" width="2" height="2" alt="" /></td>
</tr>
<tr>
  <td colspan="4">&nbsp;</td>
</tr>
</table>
</td>
</tr>
