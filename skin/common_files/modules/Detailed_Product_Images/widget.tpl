{*
$Id: widget.tpl,v 1.1.2.2 2011/04/29 11:57:51 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{getvar var=det_images_widget}
{if $det_images_widget eq 'cloudzoom'}
{include file="modules/Detailed_Product_Images/cloudzoom_image.tpl"}
{elseif $det_images_widget eq 'colorbox'}
{include file="modules/Detailed_Product_Images/colorbox_image.tpl"}
{else}
{include file="modules/Detailed_Product_Images/popup_image.tpl"}
{/if}
