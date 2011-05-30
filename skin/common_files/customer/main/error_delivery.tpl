{*
$Id: error_delivery.tpl,v 1.1 2010/05/21 08:32:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_cannot_add_product_to_cart}</h1>

<div class="error-message">{$lng.err_delivery_options_conflict_msg}</div>

<div class="buttons-row">
  {include file="customer/buttons/go_back.tpl"}
  <div class="buttons-auto-separator"></div>
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_view_cart href="cart.php" style="link"}
</div>
