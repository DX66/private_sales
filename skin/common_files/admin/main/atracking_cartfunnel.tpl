{*
$Id: atracking_cartfunnel.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $statistics}

<table cellspacing="1" class="DataSheet">
<tr class="DataSheet">
  <th width="70%" align="left">{$lng.lbl_scenario_analysis_step}</th>
  <th width="10%">{$lng.lbl_visits}</th>
  <th width="10%">{$lng.lbl_percent_of_previous_step}</th>
  <th width="10%">{$lng.lbl_percent_of_all_visits}</th>
</tr>

{section name=num loop=$statistics}
<tr>
  <td>{inc value=$smarty.section.num.index}.
{if $statistics[num].step eq "product_page"}{$lng.lbl_viewed_product_information}
{elseif $statistics[num].step eq "start_page"}{$lng.lbl_started_cart}
{elseif $statistics[num].step eq "step1"}{$lng.lbl_checkout_step1_registration_form}
{elseif $statistics[num].step eq "step2"}{$lng.lbl_checkout_step2_payment_method}
{elseif $statistics[num].step eq "step3"}{$lng.lbl_checkout_step3_confirmation}
{elseif $statistics[num].step eq "final_page"}{$lng.lbl_order_complete}
{/if}
  </td>
  <td align="center">{$statistics[num].visits}</td>
  <td align="center">{if $statistics[num].percent_parent eq ""}-{else}{$statistics[num].percent_parent|formatprice}%{/if}
{if $statistics[num].step eq "step2"}({$statistics[num].percent_parent2|formatprice}%)*{/if}
  </td>
  <td align="center">{$statistics[num].percent_all}%</td>
</tr>
{/section}
</table>

<br /><br />

{$lng.txt_shopping_cart_conversion_funnel_note}

{else}

<br />
<div align="center">{$lng.txt_no_statistics}</div>

{/if}

