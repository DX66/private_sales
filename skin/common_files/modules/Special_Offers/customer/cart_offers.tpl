{*
$Id: cart_offers.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $products and $cart.have_offers and $cart.applied_offers}

  {capture name=dialog}

  {foreach name=offers from=$cart.applied_offers item=offer}

  {if $offer.promo_checkout ne ""}
    <div>
    {if $offer.html_checkout}
      {$offer.promo_checkout}
    {else}
      <tt>{$offer.promo_checkout|escape}</tt>
    {/if}
    </div>

     {if not $smarty.foreach.offers.last}
      <div><img src="{$ImagesDir}/spacer.gif" width="1" height="30" alt="" /></div>
     {/if}
  {/if}

  {/foreach}

  {/capture}
  {include file="customer/dialog.tpl" title=$lng.lbl_sp_offers_applied_to_cart content=$smarty.capture.dialog}

{/if}
