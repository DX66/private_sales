{*
$Id: colorbox_image.tpl,v 1.7.2.3 2010/12/20 07:12:13 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{load_defer file="lib/colorbox/colorbox.css" type="css"}
{load_defer file="lib/colorbox/jquery.colorbox-min.js" type="js"}

<script type="text/javascript">
//<![CDATA[
var lbl_previous = '{$lng.lbl_previous|wm_remove:escape:"javascript"}';
var lbl_next = '{$lng.lbl_next|wm_remove:escape:"javascript"}';
var lbl_close = '{$lng.lbl_close|wm_remove:escape:"javascript"}';
var lbl_cb_start_slideshow = '{$lng.lbl_cb_start_slideshow|wm_remove:escape:"javascript"}';
var lbl_cb_stop_slideshow = '{$lng.lbl_cb_stop_slideshow|wm_remove:escape:"javascript"}';
var lbl_cb_current_format = '{$lng.lbl_cb_current_format|wm_remove:escape:"javascript"}';

{literal}
$(document).ready(function(){
  var dpOpts = {
    transition: "fade", // Can be set to "elastic", "fade", or "none".
    speed: 350,
    href: false,
    title: false,
    rel: false,
    width: false,
    height: false,
    innerWidth: false,
    innerHeight: false,
    initialWidth: 100,
    initialHeight: 100,
    maxWidth: false,
    maxHeight: false,
    scalePhotos: true,
    scrolling: true,
    iframe: false,
    inline: false,
    html: false,
    photo: false,
    opacity: 0.3,
    open: false,
    preloading: true,
    overlayClose: true,
    slideshow: true,
    slideshowSpeed: 2500,
    slideshowAuto: false,
    slideshowStart: lbl_cb_start_slideshow,
    slideshowStop: lbl_cb_stop_slideshow,
    current: lbl_cb_current_format,
    previous: lbl_previous,
    next: lbl_next,
    close: lbl_close,
    onOpen: false,
    onLoad: false,
    onComplete: false,
    onCleanup: false,
    onClosed: false
  };
  $("a[rel=dpimages]").colorbox(dpOpts);
});
{/literal}
//]]>
</script>

<div class="image-box" style="{if $max_image_width gt 0}width: {$max_image_width}px;{/if} {if $max_image_height gt 0}height: {$max_image_height}px;{/if}">
  {include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}
</div>

<div class="dpimages-popup-link">
  <a href="javascript:void(0);" onclick="javascript: $('a[rel=dpimages]').colorbox({ldelim}open: true{rdelim}); return false;">{$lng.lbl_view_detailed_images|substitute:"counter":$images_counter}</a>
</div>

{if $config.Detailed_Product_Images.det_image_icons_box eq 'Y'}
  <div class="dpimages-icons-box">
    {foreach from=$images item=i name=images}
      <a href="{$i.image_url|amp}" class="lightbox"{if $config.Detailed_Product_Images.det_image_icons_limit gt 0 and $config.Detailed_Product_Images.det_image_icons_limit gt $smarty.foreach.images.index} style="display:none;"{/if} rel="dpimages" title="{$i.alt|escape}"><img src="{$i.icon_url|amp}" alt="{$i.alt|escape}" width="{$i.icon_image_x}" height="{$i.icon_image_y}" /></a>
    {/foreach}
    <div class="clearing"></div>
  </div>
{else}
  {foreach from=$images item=i name=images}
    <a href="{$i.image_url|amp}" class="lightbox" style="display:none;" rel="dpimages" title="{$i.alt|escape}"></a>
  {/foreach}
{/if}
