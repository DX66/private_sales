{*
$Id: order_labels_print.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.txt_site_title}</title>
{include file="meta.tpl"}
</head>
<body{$reading_direction_tag}>
{section name=oi loop=$orders_data}
{assign var=order value=$orders_data[oi].order}
{assign var=customer value=$orders_data[oi].customer}
{assign var=products value=$orders_data[oi].products}
{assign var=giftcerts value=$orders_data[oi].giftcerts}
<pre>{include file="main/order_label_print.tpl"}</pre>
======================================================
{/section}
</body>
</html>
