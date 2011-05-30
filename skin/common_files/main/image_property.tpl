{*
$Id: image_property.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $image and $image.image_type ne '' and $image.image_size gt 0}
  {$lng.lbl_image_size|escape}: {$image.image_x}x{$image.image_y}, {byte_format value=$image.image_size format=k}Kb
  {$lng.lbl_image_type|escape}: {$image.image_type|replace:"image/":""}
{/if}
