{*
$Id: preauth_order_customer.tpl,v 1.2 2010/05/25 08:14:23 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<br />{include file="mail/salutation.tpl" title=$order.title firstname=$order.firstname lastname=$order.lastname}

<br />{$lng.eml_thankyou_for_order}

<br /><b>{if $order.status eq 'A' or $order.status eq 'P' or $order.status eq 'C'}{$lng.lbl_receipt}{else}{$lng.lbl_invoice}{/if}:</b>

<br />{include file="mail/html/order_invoice.tpl"}

{include file="mail/html/signature.tpl"}
