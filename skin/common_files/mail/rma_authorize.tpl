{*
$Id: rma_authorize.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

{$lng.eml_rma_return_authorized|substitute:"returnid":$return.returnid}

{$lng.eml_rma_return_auth_note}

{$lng.eml_return_request}:
{$lng.lbl_returnid|mail_truncate}{$return.returnid}
{$lng.lbl_product|mail_truncate}{$return.product.product}
{if $return.product.product_options}{$lng.lbl_product_options|mail_truncate}{include file="modules/Product_Options/display_options.tpl" options=$return.product.product_options is_plain="Y"}{/if}
{$lng.lbl_quantity|mail_truncate}{$return.amount}
{if $return.reason ne ""}{$lng.lbl_reason_for_returning|mail_truncate}{$reasons[$return.reason]}{/if}
{if $return.action ne ""}{$lng.lbl_what_you_would_like_us_to_do|mail_truncate}{$actions[$return.action]}{/if}
{if $return.comment ne ""}{$lng.lbl_comment|mail_truncate}{$return.comment}{/if}
{$lng.eml_click_to_view_return}:
{$catalogs.customer}/returns.php?mode=modify&returnid={$return.returnid}

{include file="mail/signature.tpl"}
