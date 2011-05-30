{*
$Id: order_edit.tpl,v 1.2 2010/06/29 07:08:24 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_advanced_order_management}

<table width="100%">
<tr> 
<td valign="top"> 

  <div align="left"><b>{$lng.lbl_order} #{$order.orderid}</b><br />{$lng.lbl_date}: {$order.date|date_format:$config.Appearance.datetime_format}</div>

  <hr noshade="noshade" size="1" />

  {if $confirmation eq "Y"}
    {include file="modules/Advanced_Order_Management/confirmation.tpl"}
  {elseif $rejected}
    {$lng.lbl_aom_edit_rejected}
  {else}

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td valign="top">
<ul class="AomActions">
  <li class="AomAction{if $show eq "preview"}Active{/if}">{if $show neq "preview"}<a href="order.php?orderid={$orderid}&amp;mode=edit&amp;show=preview">{/if}{$lng.lbl_aom_preview_order}{if $show neq "preview"}</a>{/if}</li>
  <li class="AomAction{if not $single_mode and $has_giftcerts}Disabled{elseif $show eq "products"}Active{/if}">{if $show neq "products" and not (not $single_mode and $has_giftcerts)}<a href="order.php?orderid={$orderid}&amp;mode=edit&amp;show=products">{/if}{$lng.lbl_aom_edit_ordered_products}{if $show neq "products" and not (not $single_mode and $has_giftcerts)}</a>{/if}{if not $single_mode and $has_giftcerts} {$lng.lbl_aom_products_cannot_be_added}{/if}</li>
{if $has_giftcerts}
  <li class="AomAction{if $show eq "giftcerts"}Active{/if}">{if $show neq "giftcerts"}<a href="order.php?orderid={$orderid}&amp;mode=edit&amp;show=giftcerts">{/if}{$lng.lbl_aom_edit_ordered_giftcerts}{if $show neq "giftcerts"}</a>{/if}</li>
{/if}
  <li class="AomAction{if $show eq "customer"}Active{/if}">{if $show neq "customer"}<a href="order.php?orderid={$orderid}&amp;mode=edit&amp;show=customer">{/if}{$lng.lbl_aom_edit_custinfo}{if $show neq "customer"}</a>{/if}</li>
  <li class="AomAction{if $show eq "totals"}Active{/if}">{if $show neq "totals"}<a href="order.php?orderid={$orderid}&amp;mode=edit&amp;show=totals">{/if}{$lng.lbl_aom_edit_order_totals}{if $show neq "totals"}</a>{/if}</li>
</ul>

{if $order.history ne ""}
  <br />
  <ul class="AomActions">
    <li class="AomAction"><a href="javascript:void(0);" onclick="javascript: window.open('order.php?orderid={$orderid}&amp;mode=history', 'HISTORY', 'width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" class="popup-link">{$lng.lbl_aom_show_history}</a></li>
  </ul>
{/if}

  </td>
</tr>
</table>

<hr />

{if $empty_order eq "Y"}
  <font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.lbl_aom_empty_order_warning}
  <br /><br />
{/if}

<table cellspacing="1" cellpadding="2">
<tr>
  <td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_aom_delete_order href="order.php?orderid=`$orderid`&mode=edit&action=delete" substyle="delete"}</td>
  <td class="ButtonsRow">&nbsp;</td>
  <td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_aom_exit href="order.php?orderid=`$orderid`" substyle="link"}</td>
{if $order.flag_change}
  <td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_aom_cancel_changes href="order.php?orderid=`$orderid`&mode=edit&action=cancel" substyle="cancel"}</td>
{/if}
{if $empty_order ne "Y" and $order.flag_change}
  <td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_aom_save_order href="order.php?orderid=`$orderid`&mode=edit&action=save" substyle="confirm"}</td>
{/if}
</tr>
</table>

{/if}
  </td>
</tr>
</table>

<br />

{if $show eq "preview"}
  {include file="modules/Advanced_Order_Management/preview.tpl"}
{elseif $show eq "products"}
  {include file="modules/Advanced_Order_Management/edit_products.tpl"}
{elseif $show eq "giftcerts"}
  {include file="modules/Advanced_Order_Management/edit_giftcerts.tpl"}
{elseif $show eq "customer"}
  {include file="modules/Advanced_Order_Management/edit_customer.tpl"}
{elseif $show eq "totals"}
  {include file="modules/Advanced_Order_Management/edit_totals.tpl"}
{/if}

