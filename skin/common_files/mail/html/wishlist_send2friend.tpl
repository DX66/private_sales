{*
$Id: wishlist_send2friend.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p>{$lng.eml_hello}

<p>{$lng.eml_send2friend|substitute:"sender":"`$userinfo.firstname` `$userinfo.lastname`"}

<p><font color="#AA0000"><b>{$product.product}</b></font>
<hr noshade="noshade" size="1" width="70%" align="left" />
<table cellpadding="0" cellspacing="0"><tr><td>
{$product.descr}
</td></tr></table>
<b>{$lng.lbl_price}: <font color="#00AA00">{currency value=$product.taxed_price|default:$product.price}</font></b>

<p>
{$lng.eml_click_to_view_product}:
<br />
<a href="{$catalogs.customer}/product.php?productid={$product.productid}">{resource_url type="product" id=$product.productid}</a>
</p>

{include file="mail/html/signature.tpl"}

