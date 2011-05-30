{*
$Id: checkout_btn.tpl,v 1.1.2.2 2011/04/26 09:14:09 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{getvar func='func_is_acheckout_button_enabled'}
{if $func_is_acheckout_button_enabled}
  <div class="gcheckout-cart-buttons">
    <div>
      {if not $std_checkout_disabled or $paypal_express_active or $gcheckout_button}
        <p>{$lng.lbl_gcheckout_or_use}</p>
      {/if}
        <a href="cart.php?mode=acheckout"><img alt="" src="https://{$amazon_host}/gp/cba/button?color=orange&amp;cartOwnerId={$config.Amazon_Checkout.amazon_mid}&amp;size={if $btn_size eq ''}large{else}{$btn_size}{/if}&amp;background=white" /></a>
    </div>
  </div>
{/if}
