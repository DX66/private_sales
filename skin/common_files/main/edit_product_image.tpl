{*
$Id: edit_product_image.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $idtag eq ''}{assign var="idtag" value="edit_image"}{/if}
<table cellpadding="0" cellspacing="0" style="{if $type eq "P"}width: {$config.images_dimensions.P.width}px; height: {$config.images_dimensions.P.height}px{else}width: {$config.images_dimensions.T.width}px; height: {$config.images_dimensions.T.height}px{/if}">
<tr><td class="ProductDetailsImage" align="center" valign="middle">
<a title="{$lng.lbl_view_full_size}" id='a_{$idtag}' href="{$xcart_web_dir}/image.php?type={$type}&amp;id={$id}&amp;ts={$smarty.now}{if $already_loaded}&amp;tmp=Y{/if}" target="_blank">
<img id="{$idtag}" src="{$xcart_web_dir}/image.php?type={$type}&amp;id={$id}&amp;ts={$smarty.now}{if $already_loaded}&amp;tmp=Y{/if}"{if $image_x ne 0} width="{$image_x}"{/if}{if $image_y ne 0} height="{$image_y}"{/if} alt="{include file="main/image_property.tpl"}"/>
</a>
<input id="skip_image_{$type}" type="hidden" name="skip_image[{$type}]" value="" />
</td></tr>
</table>

