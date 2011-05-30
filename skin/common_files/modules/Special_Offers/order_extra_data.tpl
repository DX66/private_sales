{*
$Id: order_extra_data.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/subheader.tpl" title=$lng.lbl_sp_order_offers_applied}

{if not $data.applied_offers}
{$lng.lbl_sp_no_order_offers_applied}
{else}
<table width="100%" cellspacing="5" cellpadding="5" border="0">
{foreach name=offers from=$data.applied_offers item=offer}
<tr class="TableSubHead">
  <td colspan="3" class="sp-order-offer-name">{$offer.offer_name}</td>
</tr>
<tr>
  <td width="50%" class="sp-order-nav-title">{$lng.lbl_sp_nav_conditions}:</td>
  <td>&nbsp;</td>
  <td width="50%" class="sp-order-nav-title">{$lng.lbl_sp_nav_bonuses}:</td>
</tr>
<tr>
  <td valign="top">
  <table width="100%" cellspacing="5" cellpadding="0" border="0">
  {assign var="cnum" value=1}
  {foreach name=conditions from=$offer.conditions item=condition}
  <tr>
    <td class="sp-order-offer-name">{$cnum}.</td>
    <td width="100%" class="sp-order-offer-name">{include file="modules/Special_Offers/condition_names.tpl" item_type=$condition.condition_type}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>{include file="modules/Special_Offers/condition_names.tpl" item_type=$condition.condition_type action="include" item=$condition item_mode="view"}</td>
  </tr>
  {if not $smarty.foreach.conditions.last}
  <tr>
    <td>&nbsp;</td>
  </tr>
  {/if}
  {inc assign="cnum" value=$cnum}
  {/foreach}
  </table>
  </td>
  <td>&nbsp;</td>
  <td valign="top">
  <table width="100%" cellspacing="5" cellpadding="0" border="0">
  {assign var="bnum" value=1}
  {foreach name=bonuses from=$offer.bonuses item=bonus}
  <tr>
    <td class="sp-order-offer-name">{$bnum}.</td>
    <td width="100%" class="sp-order-offer-name">{include file="modules/Special_Offers/bonus_names.tpl" item_type=$bonus.bonus_type}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>{include file="modules/Special_Offers/bonus_names.tpl" item_type=$bonus.bonus_type action="include" item=$bonus item_mode="view"}</td>
  </tr>
  {if not $smarty.foreach.bonuses.last}
  <tr>
    <td>&nbsp;</td>
  </tr>
  {/if}
  {inc assign="bnum" value=$bnum}
  {/foreach}
  </table>
  </td>
</tr>
{/foreach}
</table>
{/if}
