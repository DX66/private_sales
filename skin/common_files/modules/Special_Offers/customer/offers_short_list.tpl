{*
$Id: offers_short_list.tpl,v 1.1.2.1 2010/10/21 13:48:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $offers_list and $printable ne 'Y'}

  {if $config.Special_Offers.offers_per_row lte 0}
    {assign var='offers_per_row' value=1}
  {else}
    {assign var='offers_per_row' value=$config.Special_Offers.offers_per_row}
  {/if}

  {cell_width value=$offers_per_row dec=1 assign="cell_width"}

  <div class="offers-short-list">

    {foreach name=offers from=$offers_list item=offer}

      <div{interline name=offers additional_class="offers-cell"} style="width: {$cell_width}%;">

        {if $offer.promo_short_img eq "Y"}
          <a href="offers.php?mode=offer&amp;offerid={$offer.offerid}"><img src="{if $offer.image_url}{$offer.image_url|amp}{else}image.php?id={$offer.promo_lng_code}{$offer.offerid}&amp;type=S{/if}" alt="" /></a>

        {elseif $offer.promo_short and $offer.html_short}

          {$offer.promo_short|amp}

        {elseif $offer.promo_short}

          <a href="offers.php?mode=offer&amp;offerid={$offer.offerid}"><strong>{$offer.promo_short|escape}</strong></a>

        {else}

          {$generic_message}

        {/if}

      </div>

      {if $offers_per_row eq 1 or ($smarty.foreach.offers.iteration % $offers_per_row) eq 0}
        <div class="clearing"></div>
      {/if}

    {/foreach}

    <div class="clearing"></div>

    <div class="offers-more-info">
      <a href="{$link_href}">{$lng.lbl_sp_more_info}</a>
    </div>

  </div>

{/if}
