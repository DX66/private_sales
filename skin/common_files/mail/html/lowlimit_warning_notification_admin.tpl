{*
$Id: lowlimit_warning_notification_admin.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<br />{$lng.eml_lowlimit_warning_message|substitute:"sender":$config.Company.company_name:"productid":$product.productid}

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.lbl_sku}:</td>
  <td><b>{$product.productcode}</b></td>
</tr>
<tr>
  <td>{$lng.lbl_product}:</td>
  <td><b>{$product.product}</b></td>
</tr>
{if $product.product_options ne ""}
<tr>
  <td>{$lng.lbl_selected_options}:</td>
  <td>{include file="modules/Product_Options/display_options.tpl" options=$product.product_options options_txt=$product.product_options_txt}</td>
</tr>
{/if}
</table>

<br />{$lng.lbl_items_in_stock|substitute:"items":$product.avail}

{include file="mail/html/signature.tpl"}
