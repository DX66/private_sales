{*
$Id: common.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_special_offers}

{if $mode eq ""}

{include file="modules/Special_Offers/offers.tpl"}

{else}

<br />

{capture name=dialog}

{if $mode eq "modify"}
{assign var="dialog_title" value=$lng.lbl_sp_offer_details_s|substitute:"name":$offer.name}

{elseif $mode eq "conditions"}
{assign var="dialog_title" value=$lng.lbl_sp_offer_conditions_s|substitute:"name":$offer.name}

{elseif $mode eq "bonuses"}
{assign var="dialog_title" value=$lng.lbl_sp_offer_bonuses_s|substitute:"name":$offer.name}

{elseif $mode eq "promo"}
{assign var="dialog_title" value=$lng.lbl_sp_offer_promos_s|substitute:"name":$offer.name}

{elseif $mode eq "status"}
{assign var="dialog_title" value=$lng.lbl_sp_offer_status_name_s|substitute:"name":$offer.name}

{else}
{assign var="dialog_title" value=$offer.name}

{/if}

{include file="modules/Special_Offers/wizard_step.tpl"}

{/capture}
{include file="dialog.tpl" title=$dialog_title content=$smarty.capture.dialog extra='width="100%"'}
{/if}
