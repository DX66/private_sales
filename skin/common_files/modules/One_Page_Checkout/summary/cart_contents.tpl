{*
$Id: cart_contents.tpl,v 1.1.2.2 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<table cellspacing="0" class="cart-content width-100" summary="{$lng.lbl_products|strip_tags:false|escape}">

  {foreach from=$products item=product name=products}

    <tr>
      <td>
        <a href="product.php?productid={$product.productid}" title="{$product.product|escape}">{$product.product|truncate:50:"...":true|amp} ({$product.productcode|escape})</a>
        {if $product.product_type eq "C" and $product.display_price lt 0}
          <span class="pconf-negative-price"> {$lng.lbl_pconf_discounted}</span>
        {/if}
        {if $active_modules.Gift_Registry}
          {include file="modules/Gift_Registry/product_event_cart_contents.tpl"}
        {/if}
      </td>

      <td class="cart-content-text nowrap">
        {$lng.lbl_qty}: {$product.amount}
      </td>
      
      <td class="cart-column-total cart-content-text">
        {multi x=$product.display_price y=$product.amount assign="total"}
        {currency value=$total display_sign=$product.price_show_sign}
      </td>
    </tr>

  {/foreach}

  {if $cart.giftcerts ne ""}

    {foreach from=$cart.giftcerts item=gc name=gc}

      <tr>
        <td>{$lng.lbl_gc_for} {$gc.recipient|truncate:30:"...":true}</td>
        <td class="cart-content-text">{$lng.lbl_qty}: 1</td>
        <td class="cart-column-price cart-content-text">
          {currency value=$gc.amount}
        </td>
      </tr>

    {/foreach}

  {/if}

</table>
