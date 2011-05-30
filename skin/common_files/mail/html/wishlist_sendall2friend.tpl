{*
$Id: wishlist_sendall2friend.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p>{$lng.eml_hello}

<p>{$lng.eml_wish_list_send_msg|substitute:"sender":"`$userinfo.firstname` `$userinfo.lastname`"}

<p>
{section name=num loop=$wl_products}
<hr noshade="noshade" size="1" width="70%" align="left" />
<font color="#AA0000"><b>{$wl_products[num].product}</b></font>
<table cellpadding="0" cellspacing="0"><tr><td>
{$wl_products[num].descr|truncate:200:"..."}
</td></tr></table>
<b>{$lng.lbl_price}: <font color="#00AA00">{currency value=$wl_products[num].taxed_price|default:$wl_products[num].price}</font></b>
{/section}

<hr noshade="noshade" size="1" width="70%" align="left" />

<p>{$lng.eml_click_to_view_wishlist}:
<br />
<a href="{$catalogs.customer}/cart.php?mode=friend_wl&amp;wlid={$wlid}">{$catalogs.customer}/cart.php?mode=friend_wl&wlid={$wlid}</a>
</p>

{include file="mail/html/signature.tpl"}

