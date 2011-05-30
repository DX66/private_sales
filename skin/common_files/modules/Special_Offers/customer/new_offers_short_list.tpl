{*
$Id: new_offers_short_list.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $new_offers}
  {include file="modules/Special_Offers/customer/offers_short_list.tpl" offers_list=$new_offers generic_message=$lng.lbl_sp_new_offers_generic link_href="offers.php"}
{/if}
