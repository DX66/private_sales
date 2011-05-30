{*
$Id: offers_list.tpl,v 1.3.2.1 2010/10/21 13:48:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_sp_offers_of_shop}</h1>

{capture name="dialog"}

  {if $offers eq ""}

    <p class="text-pre-block">{$lng.lbl_sp_offers_not_avail}</p>

  {else}

    {foreach name=offers from=$offers item=offer}

      <div class="text-block">
        {if $offer.promo_long ne ""}

          {if $offer.html_long}
            {$offer.promo_long|amp}
          {else}
            <tt>{$offer.promo_long|escape}</tt>
          {/if}

        {elseif $offer.promo_short ne ""}

          {if $offer.html_short}
            {$offer.promo_short}
          {else}
            <tt>{$offer.promo_short|escape}</tt>
          {/if}

        {else}

          {$lng.lbl_sp_promo_not_avail}

        {/if}

        <div class="clearing"></div>
      </div>

      <div><img src="{$ImagesDir}/spacer.gif" width="1" height="{if $smarty.foreach.offers.last}5{else}20{/if}" alt="" /></div>

    {/foreach}

    {if $action ne "popup"}

      <div class="buttons-row">
        {if $mode ne ""}
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_show_all_offers href="offers.php?offers_return_url=`$offers_return_url`"}
          <div class="button-separator"></div>
        {/if}

        {if $mode ne "cart" and $cart_offers}
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_show_offers_for_cart href="offers.php?mode=cart&amp;offers_return_url=`$offers_return_url`"}
          <div class="button-separator"></div>
        {/if}

        {if $offers_return_url}

          {if $offers_return_checkout}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_checkout href=$offers_return_url|escape|amp}
          {else}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue_shopping href=$offers_return_url|escape|amp}
          {/if}

        {/if}
      </div>
      <div class="clearing"></div>

    {/if}

  {/if}
{/capture}

{if $action eq "popup"}
  {$smarty.capture.dialog}
{else}
  {include file="customer/dialog.tpl" title=$lng.lbl_sp_offers_of_shop content=$smarty.capture.dialog noborder=true}
{/if}
