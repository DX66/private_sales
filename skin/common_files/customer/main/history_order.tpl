{*
$Id: history_order.tpl,v 1.2 2010/05/25 08:14:23 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_order_details_label}</h1>

<p class="text-block">{$lng.txt_order_details_top_text}</p>

{capture name=dialog}

  <div>
    {if $orderid_prev ne ""}
      <a href="order.php?orderid={$orderid_prev}">&lt;&lt;&nbsp;{$lng.lbl_order} #{$orderid_prev}</a>
    {/if}
    {if $orderid_next ne ""}
      {if $orderid_prev ne ""}
        |
      {/if}
      <a href="order.php?orderid={$orderid_next}">{$lng.lbl_order} #{$orderid_next}&nbsp;&gt;&gt;</a>
    {/if}
  </div>

  <div class="buttons-row">
    {if $active_modules.RMA ne ''} 

      {if $return_products ne ''}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_create_return href="#returns" style="link"}
        <div class="button-separator"></div>
      {/if}

      {if $order.is_returns}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_order_returns href="returns.php?mode=search&search[orderid]=`$order.orderid`" style="link"}
        <div class="button-separator"></div>
      {/if}

    {/if}

    {assign var=order_url value="order.php?orderid=`$order.orderid`"}
    {if $order.access_key}
      {assign var=order_url value="`$order_url`&amp;access_key=`$order.access_key`"}
    {/if}
    {if $order.status eq 'A' or $order.status eq 'P' or $order.status eq 'C'}
      {assign var=bn_title value=$lng.lbl_print_receipt}
    {else}
      {assign var=bn_title value=$lng.lbl_print_invoice}
    {/if}
    {include file="customer/buttons/button.tpl" button_title=$bn_title target="_blank" href="`$order_url`&mode=invoice" style="link"}
    
    {if $active_modules.Advanced_Order_Management and $order.history ne ""}
      <div class="button-separator"></div>
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_aom_show_history href="javascript:popupOpen('order.php?orderid=`$order.orderid`&amp;mode=history','`$lng.lbl_aom_show_history`')" style="link" link_href="`$order_url`&mode=history" target="_blank"}
    {/if}
  </div>

  <hr />

  {include file="mail/html/order_invoice.tpl" is_nomail='Y'}

  {if $active_modules.Order_Tracking and $order.tracking}

    <br />
    <br />
    <br />

    {include file="customer/subheader.tpl" title=$lng.lbl_tracking_order}

    {assign var="postal_service" value=$order.shipping|truncate:3:"":true}
    {$lng.lbl_tracking_number}: {$order.tracking}<br />
    <br />

    {if $postal_service eq "UPS"}
      {include file="modules/Order_Tracking/ups.tpl"}

    {elseif $postal_service eq "USP"}
      {include file="modules/Order_Tracking/usps.tpl"}

    {elseif $postal_service eq "Fed"}
      {include file="modules/Order_Tracking/fedex.tpl"}

    {elseif $postal_service eq "Aus"}
      {include file="modules/Order_Tracking/australia_post.tpl"}

    {elseif $postal_service eq "DHL"}
      {include file="modules/Order_Tracking/dhl.tpl"}

    {/if}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_order_details_label content=$smarty.capture.dialog noborder=true}

{if $active_modules.RMA}

  <a name="returns"></a>
  {include file="modules/RMA/customer/add_returns.tpl"}

{/if}
