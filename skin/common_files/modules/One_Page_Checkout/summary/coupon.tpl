{*
$Id: coupon.tpl,v 1.3 2010/07/26 12:21:16 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div id="opc_coupon" class="coupon-info">

  <div id="coupon-applied-container"{if $cart.coupon eq ''} style="display:none;"{/if}>

    <strong>{$lng.lbl_discount_coupon_applied}</strong>
    <a class="dotted unset-coupon-link" href="cart.php?mode=unset_coupons" title="{$lng.lbl_unset_coupon|escape}">{$lng.lbl_unset_coupon|escape}</a>

  </div>

  <div id="couponform-container"{if $cart.coupon ne ''} style="display:none;"{/if}>

    <h3>{$lng.lbl_redeem_discount_coupon}</h3>
    <p>{$lng.txt_add_coupon_header}</p>
    {if $gcheckout_enabled}
      <p class="text-block">{$lng.txt_gcheckout_add_coupon_note}</p>
    {/if}
 
    <form action="cart.php" name="couponform">

      <input type="hidden" name="mode" value="add_coupon" />

      <label for="coupon">
        {$lng.lbl_coupon_code}:
        <input type="text" size="20" name="coupon" id="coupon" />
      </label>
      {include file="customer/buttons/button.tpl" type="input" style="image" onclick="return false;"}

    </form>

  </div>

  <hr />

</div>
