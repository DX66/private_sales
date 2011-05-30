{*
$Id: send2friend.tpl,v 1.2.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}

{$lng.eml_hello}

{$lng.eml_send2friend|substitute:"sender":$name}

{$product.product}
===========================================
{$product.descr}

{$lng.lbl_price}: {currency value=$product.taxed_price}

{if $message}
{$lng.lbl_message}:
===========================================
{$message}

{/if}

{$lng.eml_click_to_view_product}:

{resource_url type="product" id=$product.productid}

{include file="mail/signature.tpl"}
