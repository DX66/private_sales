{*
$Id: head.tpl,v 1.1 2010/05/21 08:33:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}
  <div class="head-bg">
    <div class="head-bg2">
      <div class="cart-container">

        {include file="customer/language_selector.tpl"}

        <div class="logo">
          <a href="{$catalogs.customer}/home.php"><img src="{$AltImagesDir}/vivid_dreams/logo.gif" alt="" /></a>
        </div>

        {include file="customer/phones.tpl"}

        <div class="cart-block">
          {include file="customer/menu_cart.tpl"}



          {include file="customer/authbox.tpl"}

        </div>
      </div>


      <div class="line2">

        {include file="customer/search.tpl"}

        {include file="customer/tabs.tpl"}

      </div>

    </div>
  </div>

{else}

    {include file="modules/`$checkout_module`/head.tpl"}

{/if}

{include file="customer/noscript.tpl"}
