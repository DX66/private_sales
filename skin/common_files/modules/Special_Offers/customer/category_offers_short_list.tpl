{*
$Id: category_offers_short_list.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $category_offers}
  {include file="modules/Special_Offers/customer/offers_short_list.tpl" offers_list=$category_offers generic_message=$lng.lbl_sp_category_generic link_href="offers.php?mode=cat&amp;cat=`$cat`"}
{/if}
