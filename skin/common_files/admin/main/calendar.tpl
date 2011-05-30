{*
$Id: calendar.tpl,v 1.5 2010/07/02 11:52:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$lng.lbl_calendar|wm_remove|escape}</title>
	<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
	<link rel="stylesheet" type="text/css" href="{$SkinDir}/modules/Subscriptions/calendar.css" />
	<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
  <script type="text/javascript" src="{$SkinDir}/modules/Subscriptions/calendar.js" ></script>
</head>
<body{$reading_direction_tag}>
<br />
{capture name=dialog}

<table cellpadding="0" cellspacing="0" align="center">
<tr>
<td align="left">

<table cellpadding="3" cellspacing="3" width="100%">
<tr>
<td>
<font class="TopLabel">#{$productid}: {$product} - {$lng.lbl_pay_dates}</font>

<p>{$lng.lbl_choose_pay_dates_from_calendar}</p>

</td>
</tr>
</table>

</td>
</tr>

<tr>
<td align="left">

<table cellpadding="4" cellspacing="4">
<tr>
<td>

<form action="calendar.php" method="get" name="yearform">
<input type="hidden" name="productid" value="{$productid}" />

<font class="TopLabel">
{$lng.lbl_year}: <select name="current_year" onchange='javascript: document.yearform.submit();'>
{section name=idx loop=$avail_years}
<option{if $avail_years[idx] eq $current_year} selected="selected"{/if}>{$avail_years[idx]}</option>
{/section}
</select>
</font>

</form>

</td>
</tr>
</table>

</td>
</tr>

<tr>
<td align="left">

{if $year_array}

<form name="pay_dates_form" method="post" action="calendar.php" onsubmit="javascript: if (window.opener &amp;&amp; window.opener.document.getElementById('fields_subscription') &amp;&amp; window.opener.document.getElementById('fields_subscription').checked) document.pay_dates_form.fields_subscription.value = 'Y';">
<input type="hidden" name="fields_subscription" value="" />
<input type="hidden" name="productid" value="{$productid}" />
<input type="hidden" name="current_year" value="{$current_year}" />
<input type="hidden" name="geid" value="{$geid}" />

{section name=dayofyear loop=$days_array}
<input type="hidden" name='pay_dates_array[{inc value=%dayofyear.index%}]' value='{$days_array[dayofyear]}' />
{if $days_array[dayofyear]}
<script type="text/javascript">
//<![CDATA[
pay_dates[index++] = '{$days_array[dayofyear]}';
//]]>
</script>
{/if}
{/section}

<table cellpadding="2" cellspacing="2" align="center" >

{section name=month loop=$year_array}

{if %month.index% is div by 4}
<tr>
{/if}
<th>

<table cellpadding="2" cellspacing="2">
<tr>
  <td bgcolor="#FFFFFF">
  <table cellpadding="1" cellspacing="1" width="100%">
  <tr>
    <th class="titleMonth">{$year_array[month].month|date_format:"%B"}</th>
  </tr>
  </table>
<table cellpadding="2" cellspacing="2" width="100%">
{section name=weekday loop=$year_array[month].month_array start=0 max=7 step=1}
<tr>
<td class="weekDayLabel">
{if $smarty.section.weekday.index == 1}Mo
{elseif $smarty.section.weekday.index == 2}Tu
{elseif $smarty.section.weekday.index == 3}Wd
{elseif $smarty.section.weekday.index == 4}Th
{elseif $smarty.section.weekday.index == 5}Fr
{elseif $smarty.section.weekday.index == 6}St
{elseif $smarty.section.weekday.index == 0}Su
{/if}
</td>
{section name=weeknum loop=$year_array[month].month_array start=0 max=$year_array[month].wnum step=1}
{if $year_array[month].month_array[weekday][weeknum].day}
{if $year_array[month].month_array[weekday][weeknum].date}
{assign var="suffix" value="On"}
{else}
{assign var="suffix" value="Off"}
{/if}
{if $year_array[month].month_array[weekday][weeknum].day eq $current_date}
{assign var="cell_end_tag" value="th"}
<th title="calendar_currentdate" class="calendar_currentdate{$suffix}" onclick="javascript: ChangeStatDate(this,'{$year_array[month].month_array[weekday][weeknum].day}',{$year_array[month].month_array[weekday][weeknum].dayofyear});">
{else}
{assign var="cell_end_tag" value="td"}
<td title="calendar_date" class="calendar_date{$suffix}" onclick="javascript: ChangeStatDate(this,'{$year_array[month].month_array[weekday][weeknum].day}',{$year_array[month].month_array[weekday][weeknum].dayofyear});">
{/if}
<a href="javascript:void(0);">{$year_array[month].month_array[weekday][weeknum].day|date_format:"%d"}</a></{$cell_end_tag}>
{else}
<td class="calendar_dateOff">&nbsp;</td>
{/if}
{/section}
</tr>
{/section}
</table>
</td></tr></table>

</th>
{inc value=%month.index% assign="month_index" inc=5}
{if $month_index is div by 4}
</tr>
{/if}

{/section}

<tr>
  <td valign="top">{$lng.lbl_pay_type}: <b>{$pay_type}</b></td>
  <td colspan="3" align="right">&nbsp;</td>
</tr>

<tr>
  <td><input type="button" value="{$lng.lbl_reset_to_empty|strip_tags:false|escape}" onclick="javascript: window.open('calendar.php?productid={$productid}&amp;reset2nil=y&amp;current_year={$current_year}','calendar');" /></td>
  <td><input type="button" value="{$lng.lbl_reset_to|strip_tags:false|escape} {$pay_type|strip_tags:false|escape}" onclick="javascript: window.open('calendar.php?productid={$productid}&amp;reset=y&amp;current_year={$current_year}','calendar');" /></td>
  <td colspan="2" align="right"><input type="submit" value="{$lng.lbl_update|escape}" /></td>
</tr>

</table>
</form>

</td>
</tr>
</table>

{/if}
{/capture}
<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_pay_dates extra="width='90%'"}
</div>
</body>
</html>
