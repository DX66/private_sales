{*
$Id: cloudzoom_image.tpl,v 1.3.2.1 2011/04/29 11:57:51 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="image-box" style="{if $max_image_width gt 0}width: {$max_image_width}px;{/if} {if $max_image_height gt 0}height: {$max_image_height}px;{/if}">
<a href="{$product.image_url}" class="cloud-zoom" class="cloud-zoom" id="cloud_zoom_image" rel="tint:: '#fff', position:: 'right', smoothMove::3, adjustX:: 50, adjustY:: 0, zoomWidth:: 500, zoomHeight:: 600">
{include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}
</a>
</div>

{if $config.Detailed_Product_Images.det_image_icons_box eq 'Y'}
 
<div class="dpimages-icons-box">
  {foreach from=$images item=i name=images}
    {if $config.Detailed_Product_Images.det_image_icons_limit lte 0 or $config.Detailed_Product_Images.det_image_icons_limit > $smarty.foreach.images.index}
    <a href="{$i.image_url|amp}" class="cloud-zoom-gallery" rel="useZoom:: cloud_zoom_image, smallImage:: '{$i.thbn_url|escape:"javascript"}'" title="{$i.alt|escape}"><img src="{$i.icon_url|amp}" alt="{$i.alt|escape}" width="{$i.icon_image_x}" height="{$i.icon_image_y}" /></a>
    {/if}
    {/foreach}
  <div class="clearing"></div>
</div>

{/if}
