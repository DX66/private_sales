{*
$Id: survey_filter.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div align="right">

{include file="main/visiblebox_link.tpl" mark="filter" title=$lng.lbl_survey_filter_results visible=$is_filter}
<br />

<div id="boxfilter" style="background-color: #dddddd; padding: 2px;{if not $is_filter} display: none;{/if}">
<form action="survey.php" method="post">
<input type="hidden" name="section" value="{$section|escape}" />
<input type="hidden" name="mode" value="filter" />
<input type="hidden" name="surveyid" value="{$survey.surveyid}" />

<table cellpadding="3" cellspacing="0">
<tr>
  <td><b>{$lng.lbl_survey_filter_date_from}</b></td>
  <td>{include file="main/datepicker.tpl" name="start_date" date=$date_from|default:$prev_month}</td>
  <td style="padding-left: 8px;"><b>{$lng.lbl_survey_filter_date_to}</b></td>
  <td>{include file="main/datepicker.tpl" name="end_date" date=$date_to}
</tr>
</table>

<br />

<input type="submit" value="{$lng.lbl_apply}" />

{if $is_filter}
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_reset}" onclick="javascript: submitForm(this, 'reset_filter');" />
{/if}

</form>
</div>

</div>
