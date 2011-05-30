{*
$Id: widget.tpl,v 1.1 2010/06/21 11:26:22 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.Detailed_Product_Images.det_image_box_plugin eq 'Z'}
{include file="modules/Detailed_Product_Images/cloudzoom_image.tpl"}
{elseif $config.Detailed_Product_Images.det_image_box_plugin eq 'C' and $config.setup_images.D.location neq 'DB'}
{include file="modules/Detailed_Product_Images/colorbox_image.tpl"}
{else}
{include file="modules/Detailed_Product_Images/popup_image.tpl"}
{/if}
