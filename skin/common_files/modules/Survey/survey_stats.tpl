{*
$Id: survey_stats.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{$smarty.capture.survey_menu_box}

{if $mode eq 'printable'}
<div style="FONT-SIZE: 11px; FONT-WEIGHT: bold;">{$survey.survey}</div>

<br />
<br />
{/if}

{include file="main/subheader.tpl" title=$lng.lbl_survey_survey_summary}

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.lbl_survey_invitations_sent}:</td>
  <td>{$survey.general_stats.invitations_sent|default:0} / {$stats.maillist_count|default:0}</td>
</tr>
<tr>
  <td>{$lng.lbl_survey_started_filling}:</td>
  <td>{$survey.general_stats.started_filling|default:0}</td>
</tr>
<tr>
  <td>{$lng.lbl_survey_completed_filling_survey}:</td>
  <td>{$survey.general_stats.completed_filling|default:0}</td>
</tr>
<tr>
  <td>{$lng.lbl_survey_first_filled}:</td>
  <td>{if $survey.general_stats.first_filled eq 0}{$lng.lbl_survey_has_never_been_completed}{else}{$survey.general_stats.first_filled|date_format:$config.Appearance.datetime_format}{/if}</td>
</tr>
<tr>
  <td>{$lng.lbl_survey_last_filled}:</td>
  <td>{if $survey.general_stats.last_filled eq 0}{$lng.lbl_survey_has_never_been_completed}{else}{$survey.general_stats.last_filled|date_format:$config.Appearance.datetime_format}{/if}</td>
</tr>
</table>

{if $survey.questions}

<script type="text/javascript">
//<![CDATA[

var lbl_survey_loading_texts = "{$lng.lbl_survey_loading_texts|wm_remove|escape:javascript}";
var txt_survey_loading_timeout = "{$lng.txt_survey_loading_timeout|wm_remove|escape:javascript}";
var txt_survey_loading_timeout_and_open_popup = "{$lng.txt_survey_loading_timeout_and_open_popup|wm_remove|escape:javascript}";
var txt_survey_loading_http_error = "{$lng.txt_survey_loading_http_error|wm_remove|escape:javascript}";
var txt_survey_loading_http_error_and_open_popup = "{$lng.txt_survey_loading_http_error_and_open_popup|wm_remove|escape:javascript}";
var txt_survey_statistocs_is_cleared_note = "{$lng.txt_survey_statistocs_is_cleared_note|wm_remove|escape:javascript}";
var loadingTextsQuery = [];
var loadingTextsIdx = false;
var loadingTextsLimit = false;
var loadingTextsPopup = false;

//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/modules/Survey/survey_stats.js"></script>

<br />
<br />

{include file="main/subheader.tpl" title=$lng.lbl_survey_statistics_by_questions}

{if $mode ne 'printable'}
{include file="modules/Survey/survey_filter.tpl" date_from=$filter.date_from date_to=$filter.date_to}

<br />
<br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_printable_version href="survey.php?surveyid=`$survey.surveyid`&section=stats&mode=printable"}</div>

{elseif $filter.date_from and $filter.date_to}
{assign var="stat_start_date" value=$filter.date_from|date_format:$config.Appearance.datetime_format}
{assign var="stat_end_date" value=$filter.date_to|date_format:$config.Appearance.datetime_format}
<div align="right" style="FONT-SIZE: 11px; FONT-WEIGHT: bold;">{$lng.txt_survey_results_by_period|substitute:"from":$stat_start_date:"to":$stat_end_date}</div>

<br />

{/if}

{if $is_filter}
{$lng.lbl_survey_started_filling_filter}: {$survey.count|default:0}<br />
{/if}

<br />

<table cellspacing="1" cellpadding="2">

{foreach from=$survey.questions item=q key=qid name="survey_question"}

{counter assign="question_counter"}

<tr>
  <td>

<span class="SurveyQuestion">{$question_counter}. {$q.question}</span><br />

<div style="padding-left: 17px;">
  {$lng.lbl_survey_percent_filling|substitute:total:$q.result_filled:percent:$q.percent}
 
{if $q.answers_type eq 'N' and $q.result_filled gt 0}

<br /><br />
<table cellspacing="1" cellpadding="3" width="100%">

{if $mode ne 'printable'}

<tr>
  <td>
<a href="javascript:void(0);" onclick="javascript: loadAnswersTexts(this, {$qid}, 0);">{$lng.lbl_survey_answer_comment_variants}</a></td>
</tr>

{else}

<tr>
  <td class="SurveyAnswerComment">{include file="modules/Survey/survey_texts.tpl" texts=$q.answers.0.texts}</td>
</tr>

{/if}

</table>

{else if $q.answers_type ne 'N'}
<br /><br />

{if $q.answers}

<table cellspacing="1" cellpadding="3" width="100%">

{foreach from=$q.answers item=a key=aid}
<tr{cycle values=", class='TableSubHead'" name="ccl_`$question_counter`"}>
  <td width="350">
{if $a.result_comment gt 0 and $mode ne 'printable'}
<a href="javascript:void(0);" id="a{$qid}-{$aid}" onclick="javascript: loadAnswersTexts(this, {$qid}, {$aid});"><b>{$a.answer}</b></a>
{else}
<b>{$a.answer}</b>
{/if}
  </td>
  <td width="20" align="right">{$a.result}</td>
  <td valign="middle">{include file="modules/Survey/display_bar.tpl" width=$a.bar_width percent=$a.percent highlighted=$a.highlighted width_invert=$a.bar_width_invert}</td>
</tr>

{if $a.result_comment gt 0 and $mode eq 'printable'}
<tr>
  <td class="SurveyAnswerComment" colspan="3">{include file="modules/Survey/survey_texts.tpl" texts=$a.texts}</td>
</tr>
{/if}

{/foreach}

</table>

{/if}

{/if}

</div>

  <br />

  </td>
</tr>

{/foreach}

</table>

{if $has_stats}
<br />
<hr />

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_survey_display_instances href="survey.php?surveyid=`$survey.surveyid`&section=instances"}</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_survey_clear_statistics href="javascript: if (confirm(txt_survey_statistocs_is_cleared_note)) self.location='survey.php?surveyid=`$survey.surveyid`&amp;mode=clear_stats';"}</td>
</tr>
</table>

{/if}
{/if}

{/capture}
{if $mode eq 'printable'}
{$smarty.capture.dialog}
{else}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_statistics`" content=$smarty.capture.dialog extra='width="100%"'}
{/if}
