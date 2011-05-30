{*
$Id: decline_notification.tpl,v 1.1 2010/05/21 08:32:13 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}

{include file="mail/salutation.tpl" title=$customer.title firstname=$customer.firstname lastname=$customer.lastname}

{$lng.eml_order_declined}

{$lng.lbl_order_id|mail_truncate}#{$order.orderid}
{$lng.lbl_order_date|mail_truncate}{$order.date|date_format:$config.Appearance.datetime_format}

{include file="mail/order_data.tpl"}

{include file="mail/signature.tpl"}
