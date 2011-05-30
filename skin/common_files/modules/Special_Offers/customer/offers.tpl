{*
$Id: offers.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $mode eq "add_free"}

  {include file="modules/Special_Offers/customer/checkout_free_products.tpl"}

{else}

  {include file="modules/Special_Offers/customer/offers_list.tpl"}

{/if}
