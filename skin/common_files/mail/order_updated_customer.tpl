{*
$Id: order_updated_customer.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{include file="mail/salutation.tpl" title=$order.title firstname=$order.firstname lastname=$order.lastname}

{$lng.eml_order_has_been_updated}

{include file="mail/order_invoice.tpl"}

{include file="mail/signature.tpl"}
