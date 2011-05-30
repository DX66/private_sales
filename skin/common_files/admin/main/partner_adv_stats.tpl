{*
$Id: partner_adv_stats.tpl,v 1.1.2.1 2010/12/15 09:44:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_adv_statistics}
{$lng.txt_advertising_stats_note}<br /><br />

<br />
 
{capture name=dialog}
<form action="partner_adv_stats.php" method="post">

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td>{$lng.lbl_period_from}:</td>
  <td>{include file="main/datepicker.tpl" name="start_date" date=$search.start_date|default:$month_begin}</td>
</tr>

<tr>
  <td>{$lng.lbl_period_to}:</td>
  <td>{include file="main/datepicker.tpl" name="end_date" date=$search.end_date}</td>
</tr>

<tr>
    <td>{$lng.lbl_campaigns}:</td>
    <td><select name="search[campaignid]">
  <option value=''{if $search.campaignid eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
  {if $campaigns ne ''}
  {foreach from=$campaigns item=v}
  <option value='{$v.campaignid}'{if $search.campaignid eq $v.campaignid} selected="selected"{/if}>{$v.campaign}</option>
  {/foreach}
  {/if}
  </select></td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td class="SubmitBox"><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_search extra='width="100%"'}

<br />

{if $result ne ''}
{capture name=dialog}
<table cellspacing="1" cellpadding="3" width="100%">
<tr class="TableHead">
  <td>{$lng.lbl_campaign}</td>
    <td>{$lng.lbl_clicks}</td>
    <td>{$lng.lbl_estimated_expences}</td>
    <td>{$lng.lbl_acquisition_cost}</td>
  <td>{$lng.lbl_sales}</td>
  <td>{$lng.lbl_roi}</td>
</tr>
{foreach from=$result item=v}
<tr{cycle values=", class='TableSubHead'"}>
  <td><a href="partner_adv_campaigns.php?campaignid={$v.campaignid}">{$v.campaign}</a></td>
  <td>{$v.clicks}</td>
    <td align="right" nowrap="nowrap">{currency value=$v.ee|default:"0"}</td>
  <td align="right" nowrap="nowrap">{currency value=$v.acost|default:"0"}</td>
    <td align="right" nowrap="nowrap">{currency value=$v.total|default:"0"}</td>
    <td>{$v.roi|default:"0"}%</td>
</tr>
{/foreach}
<tr>
    <td colspan="6" height="1"><hr size="1" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_total}:</b></td>
    <td>{$total.clicks}</td>
    <td align="right" nowrap="nowrap">{currency value=$total.ee|default:"0"}</td>
    <td align="right" nowrap="nowrap">{currency value=$total.acost|default:"0"}</td>
    <td align="right" nowrap="nowrap">{currency value=$total.total|default:"0"}</td>
    <td>{$total.roi|default:"0"}%</td>
</tr>
</table>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_advertising_campaigns extra='width="100%"'} 
{/if}
