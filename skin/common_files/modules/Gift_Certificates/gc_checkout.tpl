{*
$Id: gc_checkout.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $cart.giftcerts ne ""}

{foreach from=$cart.giftcerts item=gc name=gc}

{inc assign="index" value=$products_length inc=$smarty.foreach.gc.index}
    <tr{interline index=$index total=$list_length}>
      <td>&nbsp;</td>
      <td>{$lng.lbl_gc_for} {$gc.recipient|truncate:30:"...":true}</td>
{if $cart.display_cart_products_tax_rates eq "Y"}
      <td>&nbsp;</td>
{/if}
      <td class="cart-column-price cart-content-text">
        {currency value=$gc.amount}
      </td>
      <td class="cart-content-text">1</td>
      <td class="cart-column-total cart-content-text">
        {currency value=$gc.amount}
      </td>
    </tr>

{/foreach}

{/if}
