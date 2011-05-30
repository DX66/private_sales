{*
$Id: preauth_order_notification.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{$lng.eml_order_notification|substitute:"orderid":$order.orderid}

{include file="mail/order_invoice.tpl"}

{include file="mail/signature.tpl"}
