{*
$Id: opc_payment.tpl,v 1.3 2010/07/02 10:00:40 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div id="opc_payment">
  <h2>{$lng.lbl_payment_method}</h2>

  <form action="cart.php" method="post" name="paymentform">
    <input type="hidden" name="mode" value="checkout" />
    <input type="hidden" name="cart_operation" value="cart_operation" />
    <input type="hidden" name="action" value="update" />

    <div class="opc-section-container opc-payment-options">
      {include file="customer/main/checkout_payment_methods.tpl}
      <div class="clearing"></div>
    </div>
  </form>
</div>
