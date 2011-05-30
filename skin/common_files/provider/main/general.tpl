{*
$Id: general.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_summary last_url="general.php"}

{$lng.txt_summary_provider_top_text}
<br />
{capture name=dialog}
<table cellpadding="2" cellspacing="0" width="100%">
<tr>
  <td width="10"></td>
  <td width="100%"></td>
</tr>

<tr>
  <td class="TableHead" colspan="2" height="16"><font class="TopLabel ">{$lng.lbl_general_info}</font></td>
</tr>

<tr>
  <td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td>
</tr>

{*** STATUS INFO ***}

{if $empty_prices}
<tr>
  <td colspan="2">
<font class="AdminTitle">{$lng.txt_N_products_with_empty_price|substitute:"products":$empty_prices}</font>
&nbsp;&nbsp;&nbsp;<a href="search.php" title="{$lng.lbl_search_products|escape}">{$lng.lbl_click_here_to_check} &gt;&gt;</a>
  </td>
</tr>
{/if}

{if $config.Shipping.enable_shipping ne "Y"}
<tr>
  <td colspan="2">
  <font class="AdminTitle">{$lng.txt_store_admin_has_disabled_shipping_calculations}</font>
  </td>
</tr>
{/if}

{if $config.Shipping.realtime_shipping ne "Y" and $shipping_rates_count le "0" and $config.Shipping.enable_shipping eq "Y"}
<tr>
  <td colspan="2">
  <font class="AdminTitle">{$lng.txt_have_not_defined_shipping_rates}</font>
&nbsp;&nbsp;&nbsp;<a href="shipping_rates.php" title="{$lng.lbl_shipping_rates|escape}">{$lng.lbl_click_here_to_define} &gt;&gt;</a>
  </td>
</tr>
{/if}

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

{*** TAXES INFO ***}

<tr>
  <td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td colspan="2" height="16">
&nbsp;<font class="TopLabel">{$lng.lbl_taxes_info}</font>&nbsp;&nbsp;&nbsp;<a href="taxes.php" title="{$lng.lbl_tax_rates|escape}">{$lng.lbl_click_here_for_details} &gt;&gt;</a>
  </td>
</tr>

<tr>
  <td colspan="2" height="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
</tr>

<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
  <td width="100%">
<table cellpadding="1" cellspacing="2" border="0" width="80%">
<tr>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_tax}</td>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_tax_rates_count}</td>

</tr>
{assign var="index" value="0"}
{section name=idx loop=$tax_info}
<tr{cycle name="c4" values=", class='TableSubHead'"}>
  <td align="center"><a href="taxes.php?taxid={$tax_info[idx].taxid}">{$tax_info[idx].tax_name}</a></td>
  <td align="center">
{if $tax_info[idx].count eq 0}
<font class="AdminTitle">{$lng.txt_warn_no_tax_rates_defined}</font>
{else}
{$tax_info[idx].count}
{/if}
  </td>
</tr>
{inc value=$index assign="index"}
{/section}
</table>

  </td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

{*** ORDERS INFO ***}

<tr>
  <td colspan="2">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
  <td colspan="2" height="16">
&nbsp;<font class="TopLabel">{$lng.lbl_orders_info}</font>&nbsp;&nbsp;&nbsp;<a href="orders.php" title="{$lng.lbl_search_orders|escape}">{$lng.lbl_click_here_for_details} &gt;&gt;</a>
  </td>
</tr>

<tr>
  <td colspan="2" height="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
</tr>

<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
  <td width="100%">
<table cellpadding="1" cellspacing="2" width="80%">
<tr>
  <td height="14" class="TableHead">&nbsp;</td>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_since_last_log_in}</td>
  <td height="14" class="TableHead" align="center">{$lng.lbl_today}</td>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_this_week}</td>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_this_month}</td>

</tr>
{assign var="index" value="0"}
{foreach key=key item=item from=$orders}
<tr{cycle name="c1" values=", class='TableSubHead'"}>
  <td nowrap="nowrap">{if $key eq "P"}{$lng.lbl_orders_processed}{elseif $key eq "Q"}{$lng.lbl_orders_queued}{elseif $key eq "F"}{$lng.lbl_orders_failed}{elseif $key eq "D"}{$lng.lbl_orders_declined}{elseif $key eq "I"}{$lng.lbl_orders_not_finished}{/if}:</td>
{section name=period loop=$item}
  <td align="center">{$item[period]}</td>
{/section}
</tr>
{inc value=$index assign="index"}
{/foreach}
</table>

  </td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

{*** SHIPPING RATES INFO ***}
<tr>
  <td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td colspan="2" height="16">
&nbsp;<font class="TopLabel">{$lng.lbl_shipping_rates_info}</font>&nbsp;&nbsp;&nbsp;<a href="shipping_rates.php" title="{$lng.lbl_shipping_rates_info|escape}">{$lng.lbl_click_here_to_define} &gt;&gt;</a>
</td>
</tr>

<tr>
  <td colspan="2" height="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
</tr>

<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
  <td width="100%">
{if $shipping_rates_count gt "0"}
<font class="Text">
{$lng.txt_N_shipping_rates_defined|substitute:"count":$shipping_rates_count}:
</font>
<br />
<table cellpadding="1" cellspacing="2" border="0" width="80%">
<tr>
  <td height="14" class="TableHead">{$lng.lbl_carrier}</td>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_rates_enabled}</td>
</tr>
{section name=idx loop=$shipping_rates_enabled}
<tr{cycle name="c2" values=", class='TableSubHead'"}>
  <td>
{if $shipping_rates_enabled[idx].code eq "FDX"}FedEx{elseif $shipping_rates_enabled[idx].code eq "UPS"}UPS{elseif $shipping_rates_enabled[idx].code eq "USPS"}U.S.P.S.{elseif $shipping_rates_enabled[idx].code eq "DHL"}DHL{elseif $shipping_rates_enabled[idx].code eq "ABX"}Airborne{elseif $shipping_rates_enabled[idx].code eq "EWW"}Emery Worldwide{elseif $shipping_rates_enabled[idx].code eq "ANX"}AirNet Express{elseif $shipping_rates_enabled[idx].code}{$shipping_rates_enabled[idx].code}{else}{$lng.lbl_user_defined}{/if}
  </td>
  <td align="center">{$shipping_rates_enabled[idx].count}</td>
</tr>
{/section}

<tr>
  <td colspan="2"><br /></td>
</tr>
<tr>
  <td colspan="2" width="100%"><font class="Text">{$lng.txt_you_have_defined_shipping_rates_for_the_following_zones}:</font><br /></td>
</tr>

<tr>
  <td height="14" class="TableHead">{$lng.lbl_zone}</td>
  <td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_rates_enabled}</td>
</tr>
{section name=idx loop=$zone_rates}
<tr{cycle name="c3" values=", class='TableSubHead'"}>
  <td>{$zone_rates[idx].name}</td>
  <td align="center">{$zone_rates[idx].count}</td>
</tr>
{/section}
</table>
{else}
<font class="AdminTitle">{$lng.txt_no_shipping_rates}</font>
{/if}
  </td>
</tr>

</table>
  </td>
</tr>

<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
  <td width="100%"><font class="Text">
{if $config.Shipping.realtime_shipping eq "Y"}
{assign var="flag" value=$lng.lbl_enabled}
{else}
{assign var="flag" value=$lng.lbl_disabled}
{/if}
{$lng.txt_realtime_shipping_rates_is|substitute:"flag":$flag}
  </font>
  </td>
</tr>

<tr>
  <td colspan="2"><br /><br /></td>
</tr>

</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_summary content=$smarty.capture.dialog extra='width="100%"'}
