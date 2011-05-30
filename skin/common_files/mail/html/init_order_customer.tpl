{*
$Id: init_order_customer.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<br />{include file="mail/salutation.tpl" title=$order.title firstname=$order.firstname lastname=$order.lastname}

<br />{$lng.eml_init_order_customer}

<br />{$lng.lbl_order_details_label}:

<br />
<table cellpadding="2" cellspacing="1">
<tr>
<td width="20%"><b>{$lng.lbl_order_id}:</b></td>
<td width="10">&nbsp;</td>
<td>#{$order.orderid}</td>
</tr>
<tr>
<td><b>{$lng.lbl_order_date}:</b></td>
<td>&nbsp;</td>
<td>{$order.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
<tr>
<td><b>{$lng.lbl_order_status}:</b></td>
<td>&nbsp;</td>
<td>{include file="main/order_status.tpl" mode="static" status=$order.status}</td>
</tr>
</table>

{include file="mail/html/signature.tpl"}
