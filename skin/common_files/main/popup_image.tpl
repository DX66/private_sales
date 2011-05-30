{*
$Id: popup_image.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/popup_image_js.js"></script>
<script type="text/javascript">
//<![CDATA[
var imagesNavigatorList = [];
{foreach from=$images item=v name=images}
imagesNavigatorList[{$smarty.foreach.images.iteration}] = {ldelim}url: '{$v.url|escape:javascript}', width: {$v.image_x|default:0}, height: {$v.image_y|default:0}{rdelim};
{/foreach}
//]]>
</script>

{if $js_selector}
  <div class="images-viewer-list">
    <a class="side-arrow left-arrow" href="javascript:void(0);" style="height: {inc value=$icon_box_height inc=2}px;"><img src="{$ImagesDir}/spacer.gif" class="hidden" alt="" /></a>

    <div class="images-viewer-icons" style="height: {inc value=$icon_box_height inc=3}px;">
      {foreach from=$images item=v name=images}
        <a href="{$href}&amp;page={$smarty.foreach.images.iteration}" style="width: {$icon_box_width}px; height: {$icon_box_height}px;"{if $smarty.foreach.images.iteration eq $page} class="selected"{/if}><img alt="{$v.alt|escape}" src="{if $v.icon_url}{$v.icon_url|amp}{else}{$v.url|amp}{/if}" width="{$v.icon_image_x}" height="{$v.icon_image_y}" /></a>
      {/foreach}
      <div class="clearing"></div>
    </div>

    <a class="side-arrow right-arrow" href="javascript:void(0);" style="height: {inc value=$icon_box_height inc=2}px;"><img src="{$ImagesDir}/spacer.gif" class="hidden" alt="" /></a>
  </div>
{/if}

<div class="images-viewer">
  <img id="detailed-image" alt="{$current_image.alt|escape}" src="{$ImagesDir}/spacer.gif" width="{$max_x}" height="{$max_y}" style="background: transparent url({$current_image.url|escape:'html'}) no-repeat center center;" />
</div>
