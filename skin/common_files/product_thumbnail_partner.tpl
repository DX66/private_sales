{*
$Id: product_thumbnail_partner.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<img src="{$http_location}/image.php?id={$productid}&amp;type=T"{if $image_x ne 0} width="{$image_x}"{/if}{if $image_y ne 0} height="{$image_y}"{/if} alt="{$product|escape}" />
