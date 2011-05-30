{*
$Id: payment_methods.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" class="checkout-payments" summary="{$lng.lbl_payment_methods|escape}">

{foreach from=$payment_methods item=payment name=pm}

  <tr{interline name=pm}{if $payment.is_cod eq "Y"} id="cod_tr{$payment.paymentid}"{/if}>
    <td>
      <input type="radio" name="paymentid" id="pm{$payment.paymentid}" value="{$payment.paymentid}"{if $payment.is_default eq "1"} checked="checked"{/if} />
    </td>

{if $payment.processor eq "ps_paypal_pro.php"}
    <td colspan="2" class="checkout-payment-paypal">

      <table cellspacing="0" cellpadding="0">
        <tr>
          <td>{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="logo"}</td>
          <td><label for="pm{$payment.paymentid}">{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="text"}</label></td>
        </tr>
      </table>

    </td>
{else}

    <td class="checkout-payment-name">
      <label for="pm{$payment.paymentid}">{$payment.payment_method}</label>
    </td>
    <td class="checkout-payment-descr">
      {$payment.payment_details}
      {if $payment.processor eq "cc_mbookers_wlt.php"}
        {include file="payments/mbookers_checkout_logo.tpl"}
      {/if}

      {if $payment.background eq "I"}
        <noscript><font class="error-message">{$lng.txt_payment_js_required_warn}</font></noscript>
      {/if}
    </td>
{/if}
  </tr>

{/foreach}

</table>
