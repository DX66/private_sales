{*
$Id: gc_cart_details.tpl,v 1.2.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $cart.giftcerts ne ""}

{if $cart.products}

  <tr>
    <td colspan="{$colspan}">&nbsp;</td>
  </tr>

  <tr class="head-row">
    <td colspan="{$colspan}">{$lng.lbl_gift_certificates}:</td>
  </tr>

{/if}

{assign var="gc_summary_subtotal" value=0}

{foreach from=$cart.giftcerts item=gc name=gc key=gcindex}
{if $gc.deleted eq ""}

{assign var="have_giftcerts" value="Y"}
{inc value=$gc_summary_subtotal inc=$gc.amount assign="gc_summary_subtotal"}

{inc assign="index" value=$products_length inc=$smarty.foreach.gc.index}
  <tr{interline class="subhead-row" index=$index total=$list_length}>
    <td>
      <a href="giftcert.php?gcindex={$gcindex}" title="{$lng.lbl_gc_for|escape} {$gc.recipient|escape}">GC #{inc value=$smarty.foreach.gc.index}</a>
    </td>

{if $cart.display_cart_products_tax_rates eq "Y"}
    <td>&nbsp;</td>
{/if}

    <td class="cart-column-price">
      {currency value=$gc.amount}
    </td>
    <td>1</td>

{if $cart.discount gt 0}
    <td class="cart-column-price">
      {currency value=0}
    </td>
{/if}

{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
    <td class="cart-column-price">
      {currency value=0}
    </td>
{/if}

    <td class="cart-column-total">
      {currency value=$gc.amount}
    </td>
  </tr>
{/if}
{/foreach}

{if $have_giftcerts eq "Y"}

  <tr class="head-row">
    <td>{$lng.lbl_summary}:</td>

{if $cart.display_cart_products_tax_rates eq "Y"}
    <td>&nbsp;</td>
{/if}
    <td class="cart-column-price">
      <b>{currency value=$gc_summary_subtotal}</b>
    </td>
    <td class="cart-column-price">&nbsp;</td>

{if $cart.discount gt 0}
    <td class="cart-column-price">
      {currency value=0}
    </td>
{/if}

{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
    <td class="cart-column-price">
      {currency value=0}
    </td>
{/if}
    <td class="cart-column-price">
      {currency value=$gc_summary_subtotal}
    </td>
  </tr>

{/if}

{/if}
