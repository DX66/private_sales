{*
$Id: head.tpl,v 1.1 2010/05/21 08:33:01 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}
<div class="head-bg">
  <div class="head-bg2">

    <div class="phones">
      {if $config.Company.company_phone}
        <span>{$lng.lbl_phone_1_title}: {$config.Company.company_phone}</span>
      {/if}
      {if $config.Company.company_phone_2}
        <span>{$lng.lbl_phone_2_title}: {$config.Company.company_phone_2}</span>
      {/if}
    </div>

    <div class="logo">
      <a href="{$catalogs.customer}/home.php"><img src="{$AltImagesDir}/vivid_dreams/logo.png" alt="" /></a>
    </div>

    <img class="header-chromo" src="{$ImagesDir}/spacer.gif" alt="" />

    <div class="cart-container">
      <div class="cart-block">



        {include file="customer/authbox.tpl"}

        {include file="customer/menu_cart.tpl"}
      </div>
    </div>

    <div class="line2">

      {include file="customer/search.tpl"}

      {include file="customer/language_selector.tpl"}
    
      {include file="customer/tabs.tpl"}
    </div>

  </div>
</div>

{else}

  {include file="modules/`$checkout_module`/head.tpl"}

{/if}

{include file="customer/noscript.tpl"}
