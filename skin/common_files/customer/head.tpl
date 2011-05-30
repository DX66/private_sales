{*
$Id: head.tpl,v 1.1 2010/05/21 08:32:02 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="line1">
  <div class="logo">
    <a href="{$catalogs.customer}/home.php"><img src="{$ImagesDir}/xlogo.gif" alt="" /></a>
  </div>

  {include file="customer/tabs.tpl"}

  {include file="customer/phones.tpl"}

</div>

<div class="line2">
  {if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}

    {include file="customer/search.tpl"}

    {include file="customer/language_selector.tpl"}

  {else}

    {include file="modules/`$checkout_module`/head.tpl"}

  {/if}
</div>

{include file="customer/noscript.tpl"}
