{*
$Id: content.tpl,v 1.3 2010/07/15 11:55:24 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="customer/dialog_message.tpl"}

{if $main ne 'cart'}

  {include file="modules/Fast_Lane_Checkout/tabs_menu.tpl"}

{else}

  <div class="checkout-buttons">
    {if not $std_checkout_disabled}
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_checkout style="div_button" href="cart.php?mode=checkout" additional_button_class="checkout-3-button"}
    {/if}
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue_shopping style="div_button" href=$stored_navigation_script additional_button_class="checkout-1-button"}
  </div>
  <div class="clearing"></div>

{/if}

{include file="modules/Fast_Lane_Checkout/home_main.tpl"}
