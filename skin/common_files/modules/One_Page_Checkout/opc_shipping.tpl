{*
$Id: opc_shipping.tpl,v 1.1 2010/05/21 08:32:45 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div id="opc_shipping">
  <h2>{$lng.lbl_shipping_method}</h2>

  <form action="cart.php" method="post" name="shippingsform">

    <input type="hidden" name="mode" value="checkout" />
    <input type="hidden" name="cart_operation" value="cart_operation" />
    <input type="hidden" name="action" value="update" />

    <div class="opc-section-container opc-shipping-options">
      {include file="customer/main/checkout_shipping_methods.tpl"}
      <div class="clearing"></div>
    </div>

    {if $display_ups_trademarks and $current_carrier eq "UPS"}
      {include file="modules/UPS_OnLine_Tools/ups_notice.tpl"}
    {/if}

  </form>
</div>
