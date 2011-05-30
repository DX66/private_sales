{*
$Id: send2friend.tpl,v 1.2.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
{$lng.eml_hello}<br />
<br />
{$lng.eml_send2friend|substitute:"sender":$name}<br />
<br />
{$product.product}<br />
<hr />
{$product.descr}<br />
<br />
{$lng.lbl_price}: {currency value=$product.taxed_price}<br />
<br />
{if $message}
{$lng.lbl_message}:<br />
{$message|nl2br|escape}
<br />
{/if}
<br />
{$lng.eml_click_to_view_product}:<br />
<a href="{$catalogs.customer}/product.php?productid={$product.productid}">{resource_url type="product" id=$product.productid}</a><br />
<br />
{include file="mail/html/signature.tpl"}
