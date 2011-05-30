{*
$Id: product.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<table width="100%">
<tr>
  <td valign="top" align="left" width="30%">
{if $active_modules.Detailed_Product_Images ne "" and $config.Detailed_Product_Images.det_image_popup eq 'Y' and $images ne ''}
{include file="modules/Detailed_Product_Images/popup_image.tpl"}
{else}
{include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}&nbsp;
{/if}
{if $active_modules.Magnifier ne "" and $config.Magnifier.magnifier_image_popup eq 'Y' and $zoomer_images ne ''}
{include file="modules/Magnifier/popup_magnifier.tpl"}
{/if}

<br />
{if $smarty.get.mode ne "printable"}
    <img src="{$ImagesDir}/printer.gif" width="14" height="15" alt="{$lng.lbl_printable_version|escape}" />&nbsp;
    <a href="product.php?productid={$product.productid}&amp;mode=printable" target="_blank" rel="nofollow">{$lng.lbl_printable_version}</a>
{/if}
  </td>
  <td valign="top">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
  <td>{$lng.lbl_sku}</td>
  <td>{$product.productcode}</td>
</tr>
{if $product.category_text}
<tr>
  <td>{$lng.lbl_category}</td>
  <td>{$product.category_text}</td>
</tr>
{/if}
{if $usertype eq "A"}
<tr>
  <td>{$lng.lbl_vendor}</td>
  <td>{$product.provider}</td>
</tr>
{/if}
<tr>
  <td>{$lng.lbl_availability}</td>
  <td>{if $product.forsale eq "Y"}{$lng.lbl_avail_for_sale}{elseif $product.forsale eq "B"}{$lng.lbl_pconf_avail_for_sale_bundled}{elseif $product.forsale eq "H"}{$lng.lbl_hidden}{else}{$lng.lbl_disabled}{/if}</td>
</tr>
<tr>
  <td colspan="2">
<br />
<br />
<span class="Text">{$product.descr}</span>
<br />
<br />
  </td>
</tr>
<tr>
  <td colspan="2"><b><font class="ProductDetailsTitle">{$lng.lbl_price_info}</font></b></td>
</tr>
<tr>
  <td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
</tr>
{if $product.product_type eq "C"}
<tr>
  <td width="50%">{$lng.lbl_pconf_wiz_steps_defined}:</td>
  <td nowrap="nowrap">{$wizards_count|default:0} {$lng.lbl_pconf_steps}</td>
</tr>
<tr>
  <td width="50%">{$lng.lbl_pconf_base_price}</td>
  <td nowrap="nowrap"><font class="ProductPriceSmall">{currency value=$product.price}</font></td>
</tr>
{else}
<tr>
  <td width="50%">{$lng.lbl_price}</td>
  <td nowrap="nowrap"><font class="ProductPriceSmall">{currency value=$product.price}</font></td>
</tr>
<tr>
  <td width="50%">{$lng.lbl_in_stock}</td>
  <td nowrap="nowrap">{$lng.lbl_items_available|substitute:"items":$product.avail}</td>
</tr>
<tr>
  <td width="50%">{$lng.lbl_weight}</td>
  <td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
{/if}
</table>
<br />

<table cellspacing="0" cellpadding="0">
<tr>
  <td style="padding-right: 20px;">{include file="buttons/modify.tpl" href="product_modify.php?productid=`$product.productid`"}</td>
  <td style="padding-right: 20px;">{include file="buttons/clone.tpl" href="process_product.php?mode=clone&productid=`$product.productid`"}</td>
  <td style="padding-right: 20px;">{include file="buttons/delete.tpl" href="process_product.php?mode=delete&productid=`$product.productid`"}</td>
</tr>
</table>

  </td>
</tr>
</table>
{/capture}
{if $smarty.get.mode eq "printable"}
{include file="dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog extra="width=440"}
{else}
{include file="dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog extra='width="100%"'}
{/if}
