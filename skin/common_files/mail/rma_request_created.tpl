{*
$Id: rma_request_created.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}


{$lng.eml_rma_request_created|substitute:"creator":"`$userinfo.firstname` `$userinfo.lastname`"}

{$lng.eml_return_requests}:
---------------------
{foreach from=$returns item=v}
{$lng.lbl_returnid|mail_truncate}{$v.returnid}
{$lng.lbl_product|mail_truncate}{$v.product.product}
{if $v.product.product_options ne ''}
{$lng.lbl_product_options}:
{include file="modules/Product_Options/display_options.tpl" options=$v.product.product_options is_plain="Y"}
{/if}
{$lng.lbl_quantity|mail_truncate}{$v.product.amount}
{if $v.reason ne ""}{$lng.lbl_reason_for_returning|mail_truncate}{$reasons[$v.reason]}{/if}
{if $v.action ne ""}{$lng.lbl_what_you_would_like_us_to_do|mail_truncate}{$actions[$v.action]}{/if}
{if $v.comment ne ""}{$lng.lbl_comment|mail_truncate}{$v.comment}{/if}
{$lng.eml_click_to_view_return}:
{$catalogs.customer}/returns.php?mode=modify&returnid={$return.returnid}

{/foreach}

{include file="mail/signature.tpl"}
