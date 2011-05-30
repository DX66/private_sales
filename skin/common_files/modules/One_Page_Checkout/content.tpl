{*
$Id: content.tpl,v 1.3 2010/07/15 11:55:24 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="customer/dialog_message.tpl"}

{if $main eq 'cart'}

  <div class="checkout-buttons">
    {if not $std_checkout_disabled}
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_checkout style="div_button" href="cart.php?mode=checkout" additional_button_class="checkout-3-button"}
    {/if}
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue_shopping style="div_button" href=$stored_navigation_script additional_button_class="checkout-1-button"}
  </div>
  <div class="clearing"></div>

  {include file="customer/main/cart.tpl"}

{else}

  {include file="modules/One_Page_Checkout/opc_main.tpl"}

{/if}
