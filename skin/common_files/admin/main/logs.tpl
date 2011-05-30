{*
$Id: logs.tpl,v 1.4 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_shop_logs}

<br />

{$lng.txt_shop_logs_top_text}

<br /><br />

<script type="text/javascript">
//<![CDATA[
var txt_clean_logs_confirmation = "{$lng.txt_clean_logs_confirmation|wm_remove|escape:javascript}";
{literal}
function managedate(type, status) {
  if (!document.searchform)
    return;

  var fields = ['f_start_date','f_end_date'];
  for (var i = 0; i < fields.length; i++)
    document.searchform.elements[fields[i]].disabled = status;
}
{/literal}
//]]>
</script>

{capture name=dialog}

<form name="searchform" action="logs.php" method="post">
<input type="hidden" name="mode" value="" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_date_period}:</td>
<td width="10">&nbsp;</td>
<td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input id="date_period_all" type="radio" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript: managedate('date',true)" /></td>
  <td nowrap="nowrap"><label for="date_period_all">{$lng.lbl_all_dates}&nbsp;&nbsp;</label></td>

  <td width="5"><input id="date_period_M" type="radio" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td nowrap="nowrap"><label for="date_period_M">{$lng.lbl_this_month}&nbsp;&nbsp;</label></td>

  <td width="5"><input id="date_period_W" type="radio" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td nowrap="nowrap"><label for="date_period_W">{$lng.lbl_this_week}&nbsp;&nbsp;</label></td>

  <td width="5"><input id="date_period_D" type="radio" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
  <td nowrap="nowrap"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>
<tr>
  <td width="5"><input id="date_period_C" type="radio" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" /></td>
  <td colspan="7"><label for="date_period_C">{$lng.lbl_specify_period_below}</label></td>
</tr>
</table>
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_date_from}:</td>
<td width="10">&nbsp;</td>
<td>
{include file="main/datepicker.tpl" name="start_date" date=$search_prefilled.start_date}
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_date_through}:</td>
<td width="10">&nbsp;</td>
<td>
{include file="main/datepicker.tpl" name="end_date" date=$search_prefilled.end_date}
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_include_logs}:</td>
<td width="10">&nbsp;</td>
<td>
  <table>
{foreach key=log_label item=txt_label from=$log_labels}
   <tr>
     <td><input id="ll_{$log_label}" type="checkbox" name="posted_data[logs][]" value="{$log_label|escape}"{if $search_prefilled.logs.$log_label ne ""} checked="checked"{/if} /></td>
     <td><label for="ll_{$log_label}">{$txt_label}</label></td>
  </tr>
{/foreach}
  </table>
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_records_count}:</td>
<td width="10">&nbsp;</td>
<td>
<input type="text" name="posted_data[count]" value="{$search_prefilled.count}" size="5" />
<br />
<font class="SmallText">{$lng.lbl_log_records_count_note}</font>
</td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td class="SubmitBox">
  <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: document.searchform.mode.value = ''; document.searchform.submit();" />
  <input type="button" value="{$lng.lbl_log_clean_selected|strip_tags:false|escape}" onclick="javascript: if (!confirm(txt_clean_logs_confirmation)) return false; document.searchform.mode.value = 'clean'; document.searchform.submit();" />
  </td>
</tr>

</table>

{if $search_prefilled.date_period ne "C"}
<script type="text/javascript">
//<![CDATA[
managedate('date', true);
//]]></script>
{/if}

</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"' title=$lng.lbl_view_shop_logs}

{if $show_results ne ""}
<br /><br />
{capture name=dialog}
{if $logs ne ""}
{foreach key=label item=data from=$logs}
<a name="result_{$label}"></a>
{include file="main/subheader.tpl" title=$log_labels.$label|default:$label}
<div>
{$data|replace:"-------------------------------------------------\n":'<hr size="1" noshade="noshade" />'|replace:"\n":"<br />"|replace:"``":"&ldquo;"|replace:"''":"&rdquo;"}
</div>
<br />
{/foreach}
{else}{* $logs ne "" *}
{$lng.lbl_log_no_entries_found}
{/if}{* $logs ne "" *}
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"' title=$lng.lbl_search_results}
{/if}{* $show_results ne "" *}
