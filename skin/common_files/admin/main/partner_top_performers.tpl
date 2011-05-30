{*
$Id: partner_top_performers.tpl,v 1.1.2.1 2010/12/15 09:44:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_top_performers}
{$lng.txt_top_performers_note}<br /><br />

<br />
 
{capture name=dialog}
<form action="partner_top_performers.php" method="post">

<table>

<tr>
  <td>{$lng.lbl_period_from}:</td>
  <td>{include file="main/datepicker.tpl" name="start_date" date=$search.start_date|default:$month_begin}</td>
</tr>

<tr>
  <td>{$lng.lbl_period_to}:</td>
  <td>{include file="main/datepicker.tpl" name="end_date" date=$search.end_date}</td>
</tr>

<tr>
    <td>{$lng.lbl_report_by}:</td>
    <td>
  <select name="search[report]">
    <option value='userid'{if $search.report eq 'userid'} selected="selected"{/if}>{$lng.lbl_affiliates}</option>
    <option value='referer'{if $search.report eq 'referer'} selected="selected"{/if}>{$lng.lbl_referrer}</option>
  </select>
  </td>
</tr>
<tr>
    <td>{$lng.lbl_sort_by}:</td>
    <td>
  <select name="search[sort]">
      <option value='clicks'{if $search.sort eq 'clicks'} selected="selected"{/if}>{$lng.lbl_clicks}</option>
      <option value='sales'{if $search.sort eq 'sales'} selected="selected"{/if}>{$lng.lbl_sales}</option>
    </select>
  </td>
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
<table cellspacing="1" cellpadding="3">
<tr class="TableHead">
  <td>{if $search.report eq 'login'}{$lng.lbl_affiliates}{else}{$lng.lbl_referrer}{/if}</td>
    <td>{$lng.lbl_clicks}</td>
    <td>{$lng.lbl_sales_number}</td>
    <td>{$lng.lbl_sales}</td>
</tr>
{foreach from=$result item=v}
<tr{cycle values=", class='TableSubHead'"}>
  <td>{$v.name|default:$lng.lbl_unknown}</td>
  <td>{$v.clicks}</td>
  <td>{$v.num_sales}</td>
    <td align="right" nowrap="nowrap">{currency value=$v.sales|default:"0"}</td>
</tr>
{/foreach}
</table>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_top_performers extra='width="100%"'} 
{/if}
