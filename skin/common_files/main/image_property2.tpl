{*
$Id: image_property2.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}
  {if $image and $image.image_type ne '' and $image.image_size gt 0}
    {$image.image_x}x{$image.image_y}, {byte_format value=$image.image_size format=k} kb
  {/if}
  {if $show_modified}
    &nbsp;&nbsp;<span style="color: #b51a00;"><b>{$lng.lbl_modified}</b></span>
  {/if}
{/strip}
