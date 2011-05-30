{*
$Id: popup_slot_products.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.txt_site_title}</title>
{include file="meta.tpl"}
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body>
{include file="presets_js.tpl"}
<script type="text/javascript" src="{$SkinDir}/js/common.js"></script>
{include file="modules/Product_Configurator/popup_slot_products_js.tpl"}

<form action="product_modify.php" method="post" name="slotform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="slot" />
<input type="hidden" name="productid" value="{$productid}" />
<input type="hidden" name="slot" value="{$slot}" />
<input type="hidden" name="action" value="assign_default_product" />
<input type="hidden" id="default_productid" name="default_productid" value="" />

<table class="slot-products-container">
<tr>
  <td class="slot-products-title">{$lng.lbl_pconf_default_product_popup_header|substitute:"slot_name":$slot_data.slot_name}</td>
</tr>
<tr>
  <td class="slot-products-container">
    <div class="slot-products-box">
<table width="100%">
{foreach from=$slot_products item=product}
<tr onmouseover="this.bgColor = '#eff3f7'" onmouseout="this.bgColor = '#ffffff'">
  <td>{include file="product_thumbnail.tpl" productid=$product.productid image_x=60 product=$product.product tmbn_url=$product.tmbn_url}</td>
{assign var=pname value=$product.product|wm_remove|escape:javascript}
  <td width="100%" class="slot-product">{include file="buttons/button.tpl" button_title=$product.product href="javascript: setDefaultProduct('`$product.productid`','`$pname`')" substyle="link"}<a href="product.php?productid={$product.productid}" target="_blank">{$lng.lbl_details}</a>
  </td>
</tr>
{/foreach}
</table>
    </div>
  </td>
</tr>
<tr>
  <td>{include file="popup_bottom.tpl"}</td>
</tr>
</table>
</form>

</body>
</html>
