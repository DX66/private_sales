{*
$Id: display_options.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $options and $force_product_options_txt eq ''}

{if $is_plain eq 'Y'}

{if $options ne $options_txt}

{foreach from=$options item=v}
   {$v.class}: {$v.option_name}
{/foreach}

{else}

{$options_txt}

{/if}

{else}

{if $options ne $options_txt}

<table cellspacing="0" class="poptions-options-list" summary="{$lng.lbl_product_options|escape}">
{foreach from=$options item=v}
  <tr>
    <td>{$v.class|escape}:</td>
    <td>{$v.option_name|escape}</td>
  </tr>
{/foreach}
</table>

{else}

{$options_txt|escape|replace:"\n":"<br />"}

{/if}

{/if}

{elseif $force_product_options_txt}

{if $is_plain eq 'Y'}

{$options_txt|escape:"html"}

{else}

{$options_txt|replace:"\n":"<br />"}

{/if}

{/if}

{if ($options or $force_product_options_txt) and $product.options_expired}
<div id="cart_message_{$product.cartid}" class="cart-message cart-message-W">
<div class="close-link" onclick="javascript: return close_opts_expire_msg('{$product.cartid}');">&nbsp;</div>
{$lng.lbl_product_options_expired}
</div>
{/if}

