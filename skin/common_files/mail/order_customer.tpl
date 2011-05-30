{*
$Id: order_customer.tpl,v 1.2 2010/05/25 08:14:23 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{include file="mail/salutation.tpl" title=$order.title firstname=$order.firstname lastname=$order.lastname}

{$lng.eml_thankyou_for_order}

{if $order.userid lte 0}
{$lng.txt_anonymous_order_access_url|substitute:"url":"`$https_location`/order.php?orderid=`$order.orderid`&amp;access_key=`$order.access_key`"}

{/if}
{if $order.status eq 'A' or $order.status eq 'P' or $order.status eq 'C'}{$lng.lbl_receipt}{else}{$lng.lbl_invoice}{/if}:

{include file="mail/order_invoice.tpl"}

{include file="mail/signature.tpl"}
