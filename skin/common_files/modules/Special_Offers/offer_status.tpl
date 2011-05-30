{*
$Id: offer_status.tpl,v 1.2 2010/07/22 10:05:33 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{cycle name="status_rows" values=",class='TableSubHead'" print=false}

<table cellpadding="3" width="100%">
<tr class="TableHead">
  <td align="center" width="80%">{$lng.lbl_sp_offer_settings}</td>
  <td align="center">{$lng.lbl_status}</td>
</tr>

{* -- AVAIL TRIGGER -- *}
<tr {cycle name="status_rows"}>
  <td><a href="offers.php?offerid={$offer.offerid}&amp;mode=modify">{$lng.lbl_active}</a></td>
  <td align="center" nowrap="nowrap">
<a href="offers.php?offerid={$offer.offerid}&amp;mode=modify">
{if $offer.avail eq 'Y' and $offer.invalid ne 1}
  {include file="modules/Special_Offers/show_status.tpl" condition=true label_true=$lng.lbl_yes label_false=$lng.lbl_no}
{else}
  {include file="modules/Special_Offers/show_status.tpl" condition=false label_true=$lng.lbl_yes label_false=$lng.lbl_no}
{/if}
</a>
  </td>
</tr>

{* -- PERIOD -- *}
<tr {cycle name="status_rows"}>
  <td><a href="offers.php?offerid={$offer.offerid}&amp;mode=modify">{$lng.lbl_sp_validity_period}</a></td>
  <td align="center" nowrap="nowrap">
<a href="offers.php?offerid={$offer.offerid}&amp;mode=modify">
{if $offer.incorrect_period}
  {if not $offer.incorrect_period}
    {include file="modules/Special_Offers/show_status.tpl" condition=true label_false=$lng.lbl_sp_offer_status_incorrect_period}
  {else}
    {include file="modules/Special_Offers/show_status.tpl" condition=false label_false=$lng.lbl_sp_offer_status_incorrect_period}
  {/if}
{elseif $offer.upcoming}
  {include file="modules/Special_Offers/show_status.tpl" condition=true label_true=$lng.lbl_sp_offer_status_upcoming}
{else}
  {if not $offer.expired}
    {include file="modules/Special_Offers/show_status.tpl" condition=true label_true=$lng.lbl_sp_offer_status_ok label_false=$lng.lbl_sp_offer_status_expired}
  {else}
    {include file="modules/Special_Offers/show_status.tpl" condition=false label_true=$lng.lbl_sp_offer_status_ok label_false=$lng.lbl_sp_offer_status_expired}
  {/if}
{/if}
</a>
  </td>
</tr>

{* -- CONDITIONS -- *}
<tr {cycle name="status_rows" advance=false}>
  <td><a href="offers.php?offerid={$offer.offerid}&amp;mode=conditions">{$lng.lbl_sp_offer_conditions}</a></td>
  <td align="center" nowrap="nowrap">
<a href="offers.php?offerid={$offer.offerid}&amp;mode=conditions">
{if $offer.conditions eq ""}
{include file="modules/Special_Offers/show_status.tpl" condition=false label_false=$lng.lbl_sp_not_defined_yet}
{else}
{include file="modules/Special_Offers/show_status.tpl" condition=$offer.conditions_valid label_true=$lng.lbl_sp_offer_status_ok label_false=$lng.lbl_sp_offer_status_fail}
{/if}
</a>
  </td>
</tr>

{if $offer.conditions_valid ne 1 and $offer.conditions ne ""}
{foreach name=conditions from=$offer.conditions item=condition}
{if $condition.avail eq "Y"}
<tr {cycle name="status_rows" advance=false}>
  <td>&nbsp;&nbsp;&nbsp;
<a href="offers.php?offerid={$offer.offerid}&amp;mode=conditions&amp;last_item_type={$condition.condition_type}">{include file="modules/Special_Offers/condition_names.tpl" item_type=$condition.condition_type}</a>
  </td>
  <td align="center" nowrap="nowrap">
<a href="offers.php?offerid={$offer.offerid}&amp;mode=conditions&amp;last_item_type={$condition.condition_type}">{include file="modules/Special_Offers/show_status.tpl" condition=$condition.valid label_true=$lng.lbl_sp_offer_status_ok label_false=$lng.lbl_sp_offer_status_fail}</a>
  </td>
</tr>
{/if}
{/foreach}
{/if}

{* -- BONUSES -- *}
{cycle name="status_rows" print=false}
<tr {cycle name="status_rows" advance=false}>
  <td><a href="offers.php?offerid={$offer.offerid}&amp;mode=bonuses">{$lng.lbl_sp_offer_bonuses}</a></td>
  <td align="center" nowrap="nowrap">
<a href="offers.php?offerid={$offer.offerid}&amp;mode=bonuses">
{if $offer.bonuses eq ""}
{include file="modules/Special_Offers/show_status.tpl" condition=false label_false=$lng.lbl_sp_not_defined_yet}
{else}
{include file="modules/Special_Offers/show_status.tpl" condition=$offer.bonuses_valid label_true=$lng.lbl_sp_offer_status_ok label_false=$lng.lbl_sp_offer_status_fail}
{/if}
</a>
  </td>
</tr>

{if $offer.bonuses_valid ne 1 and $offer.bonuses ne ""}
{foreach name=bonuses from=$offer.bonuses item=bonus}
{if $bonus.avail eq "Y"}
<tr {cycle name="status_rows" advance=false}>
  <td>&nbsp;&nbsp;&nbsp;
<a href="offers.php?offerid={$offer.offerid}&amp;mode=bonuses&amp;last_item_type={$bonus.bonus_type}">
{include file="modules/Special_Offers/bonus_names.tpl" item_type=$bonus.bonus_type}
</a>
  </td>
  <td align="center" nowrap="nowrap">
<a href="offers.php?offerid={$offer.offerid}&amp;mode=bonuses&amp;last_item_type={$bonus.bonus_type}">
{include file="modules/Special_Offers/show_status.tpl" condition=$bonus.valid label_true=$lng.lbl_sp_offer_status_ok label_false=$lng.lbl_sp_offer_status_fail}
</a>
  </td>
</tr>
{/if}
{/foreach}
{/if}
</table>
