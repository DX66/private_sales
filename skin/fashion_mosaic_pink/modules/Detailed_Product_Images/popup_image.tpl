{*
$Id: popup_image.tpl,v 1.4 2010/07/28 16:30:10 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="image-box" style="{if $max_image_width gt 0}width: {math equation="x+14" x=$max_image_width}px;{/if} {if $max_image_height gt 0}height: {math equation="x+18" x=$max_image_height}px;{/if}">
  {include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}
</div>
<div class="dpimages-popup-link">

  {include file="customer/images_preview.tpl"}

<script type="text/javascript">
//<![CDATA[
var dpimages = [
{foreach from=$images item=i name=images}
  ['{$i.image_url|escape:javascript}', '{$i.icon_url|escape:javascript}', '{$i.alt|escape:javascript}']{if not $smarty.foreach.images.last},{/if}
{/foreach}
];
var dpioptions = {ldelim}iconWidth: {$icon_box_width|default:64}, iconHeight: {$icon_box_height|default:52}{rdelim};
//]]>
</script>

  <a href="popup_image.php?type=D&amp;id={$product.productid}" onclick="javascript: setTimeout(function() {ldelim}imagesPreviewShow('dpi', dpimages, dpioptions);{rdelim}, 200); return false;" target="_blank">{$lng.lbl_view_detailed_images|substitute:"counter":$images_counter}</a>
</div>

{if $config.Detailed_Product_Images.det_image_icons_box eq 'Y'}

<script type="text/javascript">
//<![CDATA[
var det_images_popup_data = {ldelim}
  productid: {$product.productid},
  max_x: {$max_x},
  max_y: {$max_y},
  product: '{$product.product|wm_remove|escape|escape:javascript}'
{rdelim};
var config_image_height = {$config.Appearance.image_height|default:0};
//]]>
</script>
{load_defer file="modules/Detailed_Product_Images/icons_box.js" type="js"}

  <div class="dpimages-icons-box">
    {foreach from=$images item=i name=images}
      {if $config.Detailed_Product_Images.det_image_icons_limit lte 0 or $config.Detailed_Product_Images.det_image_icons_limit > $smarty.foreach.images.index}
        <a href="{$i.image_url|amp}" onclick="javascript: return dicon_click({$smarty.foreach.images.iteration});" onmouseover="javascript: dicon_over.call(this, '{$i.thbn_url|amp|escape:javascript}', {$i.thbn_image_x|default:$product.image_x}, {$i.thbn_image_y|default:$product.image_y}, {$i.is_png});" target="_blank"><img src="{$i.icon_url|amp}" alt="{$i.alt|escape}" width="{$i.icon_image_x}" height="{$i.icon_image_y}" /></a>
      {/if}
    {/foreach}
    <div class="clearing"></div>
  </div>

{/if}
