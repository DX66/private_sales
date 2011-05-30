{*
$Id: cart_subtotal.tpl,v 1.1.2.4 2010/12/15 11:57:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="right-box cart">
{assign var="subtotal" value=$cart.subtotal}
{assign var="discounted_subtotal" value=$cart.discounted_subtotal}

  <table cellspacing="0" class="totals" summary="{$lng.lbl_total|escape}">

    <tr>
      <td class="total">{$lng.lbl_subtotal}:</td>
      <td class="total-value">{currency value=$cart.display_subtotal}</td>
      <td class="total-alt-value">{alter_currency value=$cart.display_subtotal}</td>
    </tr>

{if $cart.discount gt 0}
    <tr>
      <td class="total-name">{$lng.lbl_discount}:</td>
      <td class="total-value">{currency value=$cart.discount}</td>
      <td class="total-alt-value">{alter_currency value=$cart.discount}</td>
    </tr>
{/if}

{if $cart.coupon_discount ne 0 and $cart.coupon_type ne "free_ship"}
    <tr>
      <td class="total-name dcoupons-clear">
        {$lng.lbl_discount_coupon}
        <a href="cart.php?mode=unset_coupons" title="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_unset_coupon|escape}" /></a>:
      </td>
      <td class="total-value">{currency value=$cart.coupon_discount}</td>
      <td class="total-alt-value">{alter_currency value=$cart.coupon_discount}</td>
    </tr>
{/if}

{if $cart.discounted_subtotal ne $cart.subtotal}
    <tr>
      <td class="total-line" colspan="3">
        <img src="{$ImagesDir}/spacer.gif" alt="" />
      </td>
    </tr>

    <tr>
      <td class="total">{$lng.lbl_discounted_subtotal}:</td>
      <td class="total-value">{currency value=$cart.display_discounted_subtotal}</td>
      <td class="total-alt-value">{alter_currency value=$cart.display_discounted_subtotal}</td>
    </tr>
{/if}

{if $cart.taxes and $config.Taxes.display_taxed_order_totals eq "Y"}

    <tr>
      <td colspan="3" class="total-taxes">{$lng.lbl_including}:</td>
    </tr>

{foreach key=tax_name item=tax from=$cart.taxes}
    <tr class="total-tax-line">
      <td class="total-tax-name">{$tax.tax_display_name}:</td>
      <td>{currency value=$tax.tax_cost_no_shipping}</td>
      <td>{alter_currency value=$tax.tax_cost_no_shipping}</td>
    </tr>
{/foreach}

{/if}

{if $cart.applied_giftcerts}
    <tr>
      <td class="total-name">{$lng.lbl_giftcert_discount}:</td>
      <td class="total-value">{currency value=$cart.giftcert_discount}</td>
      <td class="total-alt-value">{alter_currency value=$cart.giftcert_discount}</td>
    </tr>
{/if}

  </table>

{if $cart.applied_giftcerts}
  <br />
  <br />
  <div class="form-text">{$lng.lbl_applied_giftcerts}:</div>
{foreach from=$cart.applied_giftcerts item=gc}
    <div class="dcoupons-clear">
      {$gc.giftcert_id}
      <a href="cart.php?mode=unset_gc&amp;gcid={$gc.giftcert_id}"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_unset_gc|escape}" /></a>
       : <span class="total-name">{currency value=$gc.giftcert_cost}</span>
    </div>
{/foreach}
{/if}

{if $not_logged_message eq "1"}
{$lng.txt_order_total_msg}
{/if}

</div>

<input type="hidden" name="action" value="update" />


{if $active_modules.Special_Offers ne ""}
{include file="modules/Special_Offers/customer/cart_bonuses.tpl"}
{/if}
