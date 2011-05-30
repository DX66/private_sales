{*
$Id: feature_image.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<img src="{if $tmbn_url}{$tmbn_url|amp}{else}{$xcart_web_dir}/image.php?id={$fclassid}&amp;type=F{/if}" alt="{$class|escape}"{if $image_x ne 0} width="{$image_x}"{/if}{if $image_y ne 0} height="{$image_y}"{/if} />
