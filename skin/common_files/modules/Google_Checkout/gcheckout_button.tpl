{*
$Id: gcheckout_button.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $gcheckout_button}
  <div class="gcheckout-cart-buttons">
    <div>
      {if not $std_checkout_disabled or $paypal_express_active}
        <p>{$lng.lbl_gcheckout_or_use}</p>
      {/if}
      {$gcheckout_button}
    </div>
  </div>
{/if}
