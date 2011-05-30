{*
$Id: product_thumbnail.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}
<img
{if $id ne ''} id="{$id}"{/if} src="
{if $tmbn_url}
{$tmbn_url|amp}
{else}
{if $full_url}
{$current_location}
{else}
{$xcart_web_dir}
{/if}
/image.php?type={$type|default:"T"}&amp;id={$productid}
{/if}
"
{if $image_x ne 0} width="{$image_x}"{/if}
{if $image_y ne 0} height="{$image_y}"{/if} alt="{$product|escape}" />
{/strip}
