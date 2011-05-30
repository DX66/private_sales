{*
$Id: product_offer_thumb.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.have_offers and $config.Special_Offers.offers_show_thumb_on_lists eq "Y"}
  <a href="offers.php?mode=product&amp;productid={$product.productid}" class="offers-thumbnail"><img src="{$ImagesDir}/spacer.gif" alt="" /></a>
{/if}
