{*
$Id: opc_summary.tpl,v 1.4.2.2 2010/11/18 11:33:38 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div id="opc_summary">

  <h2>{$lng.lbl_order_summary}</h2>

  {include file="modules/One_Page_Checkout/summary/cart_totals.tpl"}
  
  {if $active_modules.Discount_Coupons}
    {include file="modules/One_Page_Checkout/summary/coupon.tpl"}
  {/if}

</div>

<form action="{$payment_script_url|default:$payment_data.payment_script_url}" method="post" name="checkout_form" id="checkout_form">

  <input type="hidden" name="paymentid" id="paymentid" value="{$cart.paymentid}" />
  <input type="hidden" name="action" value="place_order" />
  <input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
  <input type="hidden" name="payment_method" id="payment_method" value="{$payment_method|default:$payment_data.payment_method_orig|escape}" />

  <div class="checkout-customer-notes">
    <label for="customer_notes">{$lng.lbl_customer_notes}:</label>
    <textarea cols="70" rows="3" id="customer_notes" name="Customer_Notes"></textarea>
  </div>

  {if $active_modules.XAffiliate eq "Y" and $partner eq '' and $config.XAffiliate.ask_partnerid_on_checkout eq 'Y'}
    <div class="checkout-partner">
      <label for="partner_id">{$lng.lbl_partner_id}: <input type="text" name="partner_id" id="partner_id" /></label>
    </div>
  {/if}

  {if $active_modules.Mailchimp_Subscription ne ''} 
    <div class="terms_n_conditions">
      <label for="mailchimp_subscription">
        <input type="checkbox" id="mailchimp_subscription" name="mailchimp_subscription" value="Y" />
        {$lng.lbl_mailchimp_subscription}
      </label>
    </div>
  {/if}

  <div class="terms_n_conditions">
    <label for="accept_terms">
      <input type="checkbox" name="accept_terms" id="accept_terms" value="Y" />
      {$lng.txt_terms_and_conditions_note|substitute:"terms_url":"`$xcart_web_dir`/pages.php?alias=conditions":"privacy_url":"`$xcart_web_dir`/pages.php?alias=business"}
    </label>
  </div>

  <div class="button-row center" id="btn_box">
    <div class="halign-center place-order-button">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_submit_order href=$button_href type="input" additional_button_class="main-button place-order-button"}
    </div>
  </div>

</form>
