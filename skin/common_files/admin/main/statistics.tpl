{*
$Id: statistics.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_statistics}

<br />
{if $mode ne 'adaptives' and $mode ne 'users_online'}
{capture name=dialog}

<form action="statistics.php{if $smarty.server.QUERY_STRING}?{$smarty.server.QUERY_STRING}{/if}" method="post">
<input type="hidden" name="mode" value="{$mode}" />

<table cellpadding="1" cellspacing="1">

<tr>
  <th align="right">{$lng.lbl_date_from}:</th>
  <th>{include file="main/datepicker.tpl" name="start_date" date=$start_date}&nbsp;</th>
  <th align="right">{$lng.lbl_date_through}:</th>
  <th>{include file="main/datepicker.tpl" name="end_date" date=$end_date}</th>
</tr>

<tr>
  <td colspan="4"><input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_date_setting content=$smarty.capture.dialog extra='width="100%"'}

<br />
{/if}

{if $mode eq "general"}

{capture name=dialog}

<table cellspacing="0" cellpadding="0" width="100%">

<tr>
  <th class="TableHead" colspan="2" align="left" height="16">&nbsp;{$lng.lbl_total_statistics}</th>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_number_of_customers}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.clients}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_number_of_providers}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.providers}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_number_of_products}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.products}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_number_of_root_categories}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.categories}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_number_of_subcategories}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.subcategories}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_number_of_orders}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.orders}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr>
  <th class="TableHead" colspan="2" align="left" height="16">&nbsp;{$lng.lbl_general_statistics_for_period}: <i><font color="#000099">{$start_date|date_format:$config.Appearance.datetime_format} - {$end_date|date_format:$config.Appearance.datetime_format}</font></i></th>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_new_customers}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.clients_last_month}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_new_providers}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.providers_last_month}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_new_products}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.products_last_month}</td>
</tr>

<tr> 
  <td height="10" colspan="2"></td>
</tr>

<tr> 
  <td valign="top" width="343" class="Text">{$lng.lbl_new_orders}</td>
  <td valign="top" width="67" class="Text" align="right">{$statistics.orders_last_month}</td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_total_statistics content=$smarty.capture.dialog extra='width="100%"'}

<br />

{else}

{if $mode eq "shop" and $active_modules.Advanced_Statistics ne ""}

{include file="modules/Advanced_Statistics/advanced_stats.tpl"}

{else}

{capture name=dialog}

{if $mode eq "pagesviews"}
{include file="admin/main/atracking_pageviews.tpl"}
{assign var="box_title" value=$lng.lbl_top_pages_views}

{elseif $mode eq "cartfunnel"}
{include file="admin/main/atracking_cartfunnel.tpl"}
{assign var="box_title" value=$lng.lbl_shopping_cart_conversion_funnel}

{elseif $mode eq "toppaths"}
{include file="admin/main/atracking_toppaths.tpl"}
{assign var="box_title" value=$lng.lbl_top_paths_thru_site}

{elseif $mode eq "logins"}
{include file="admin/main/atracking_logins.tpl"}
{assign var="box_title" value=$lng.lbl_log_in_history}

{elseif $mode eq "adaptives"}
{include file="admin/main/atracking_adaptives.tpl"}
{assign var="box_title" value=$lng.lbl_browser_settings}

{elseif $mode eq "search"}
{include file="admin/main/atracking_search.tpl"}
{assign var="box_title" value=$lng.lbl_search_statistics}

{elseif $mode eq "users_online"}
{include file="modules/Users_online/stats.tpl"}
{assign var="box_title" value=$lng.lbl_users_online}

{/if}

{/capture}
{include file="dialog.tpl" title=$box_title content=$smarty.capture.dialog extra='width="100%"'}

{/if}

{/if}
