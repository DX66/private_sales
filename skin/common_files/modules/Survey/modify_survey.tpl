{*
$Id: modify_survey.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_required_fields_js.tpl"}
<script type="text/javascript">
//<![CDATA[
var requiredFields = [
    ["survey_name", "{$lng.lbl_survey_name|wm_remove|escape:javascript}"]
];
var txt_survey_noempty_changing_js_note = "{$lng.txt_survey_noempty_changing_js_note|wm_remove|escape:javascript}";
//]]>
</script>

{capture name=dialog}

{$smarty.capture.survey_menu_box}

<form action="survey.php" method="post" name="surveyform" onsubmit="javascript: return checkRequired(requiredFields){if $has_stats} &amp;&amp;confirm(txt_survey_noempty_changing_js_note){/if};">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<input type="hidden" name="go_to" value="" />

{include file="main/language_selector.tpl" script="survey.php?surveyid=`$surveyid`&"}

<table cellspacing="1" cellpadding="3" width="100%">

<tr>
  <td colspan="3">
{include file="main/subheader.tpl" title=$lng.lbl_survey_details}
<br />
  </td>
</tr>

<tr>
  <td width="15%" nowrap="nowrap"><b>{$lng.lbl_survey_name}:</b></td>
  <td width="5"><font class="Star">*</font></td>
  <td width="85%"><input type="text" size="70" maxlength="255" id="survey_name" name="add_data[survey]" value="{$survey.survey|escape}" /></td>
</tr>
<tr>
  <td nowrap="nowrap"><b>{$lng.lbl_survey_type}:</b></td>
  <td>&nbsp;</td>
  <td>
<select name="data[survey_type]">
{foreach from=$survey_types key=typeid item=t}
  <option value="{$typeid}"{if $survey.survey_type eq $typeid} selected="selected"{/if}>{$t.label}</option>
{/foreach}
</select>

  {include file="main/tooltip_js.tpl" text=$lng.txt_survey_type_help}

  </td>
</tr>

{assign var="_label" value="lbl_survey_type_note_`$survey.survey_type`"}
<tr>
  <td colspan="2"></td>
  <td id="stNote">{$lng.$_label}</td>
</tr>

<tr>
  <td nowrap="nowrap"><b>{$lng.lbl_survey_valid}:</b></td>
  <td>&nbsp;</td>
  <td>
<table cellspacing="0" cellpadding="0">
<tr>
  <td style="padding-right: 10px;"><b>{$lng.lbl_survey_from}</b></td>
  <td>{include file="main/datepicker.tpl" name="data[valid_from_date]" id="valid_from_date" date=$survey.valid_from_date}</td>
  <td style="padding-left: 10px; padding-right: 10px;"><b>{$lng.lbl_survey_to}</b></td>
  {inc value=$config.Company.end_year assign="endyear"}
  <td>{include file="main/datepicker.tpl" name="data[expires_data]" id="expires_data" date=$survey.expires_data end_year=$endyear}</td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_orderby}:</b></td>
  <td>&nbsp;</td>
  <td><input type="text" name="data[orderby]" size="5" value="{$survey.orderby}" /></td>
</tr>

<tr>
  <td colspan="3">

<br />
<table cellspacing="1" cellpadding="2">
<tr>
  <td width="15"><input type="checkbox" name="data[publish_results]" id="publish_results" value="Y"{if $survey.publish_results eq 'Y'} checked="checked"{/if} /></td>
  <td width="100%"><label for="publish_results">{$lng.lbl_survey_publish_result_to_respondents}</label></td>
</tr>
<tr>
  <td width="15"><input type="checkbox" name="data[display_on_frontpage]" id="display_on_frontpage" value="Y"{if $survey.display_on_frontpage eq 'Y'} checked="checked"{/if} /></td>
  <td><label for="display_on_frontpage">{$lng.lbl_survey_display_on_front_page_in_menu_boxes_column}</label></td>
</tr>

{if $survey.questions_count gt 2 and $survey.display_on_frontpage eq 'Y'}
<tr>
  <td colspan="2">{include file="main/warning.tpl" message=$lng.lbl_survey_error_msg_big_menu_survey}</td>
</tr>
{/if}

</table>

  </td>
</tr>

<tr>
  <td colspan="3">
<br />
{include file="main/subheader.tpl" title=$lng.lbl_additional_options}
  </td>
</tr>

<tr>
  <td colspan="3">
<br />
{include file="main/visiblebox_link.tpl" mark="header" title=$lng.lbl_survey_header visible=true}

<div id="boxheader">
{include file="main/textarea.tpl" name="add_data[header]" cols=55 rows=10 class="InputWidth" data=$survey.header width="80%" btn_rows=3}
</div>
  </td>
</tr>

<tr>
    <td colspan="3">
<br />
{include file="main/visiblebox_link.tpl" mark="footer" title=$lng.lbl_survey_footer visible=true}

<div id="boxfooter">
{include file="main/textarea.tpl" name="add_data[footer]" cols=55 rows=10 class="InputWidth" data=$survey.footer width="80%" btn_rows=3}
</div>
    </td>
</tr>

<tr>
    <td colspan="3">
<br />
{include file="main/visiblebox_link.tpl" mark="complete" title=$lng.lbl_survey_complete_message visible=true}

<div id="boxcomplete">
{include file="main/textarea.tpl" name="add_data[complete]" cols=55 rows=10 class="InputWidth" data=$survey.complete width="80%" btn_rows=3}
</div>
    </td>
</tr>

<tr>
  <td colspan="3" class="SubmitBox" align="left">
<script type="text/javascript">
//<![CDATA[
{if $survey.header eq ""}
document.getElementById('closeheader').onclick();
{/if}
{if $survey.footer eq ""}
document.getElementById('closefooter').onclick();
{/if}
{if $survey.complete eq ""}
document.getElementById('closecomplete').onclick();
{/if}
//]]>
</script>  
<br />
  
{if $has_stats}
<font class="ErrorMessage">{$lng.lbl_warning}! {$lng.txt_survey_noempty_changing_note}</font><br />
<br />
{/if}

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

  </td>
</tr>
</table>

</form>

<div style="text-align: center; width: 100%;">
<hr size="1" class="GreyLine" />
<input type="button" value="{$lng.lbl_next}" onclick="javascript: if (!document.surveyform.onsubmit || document.surveyform.onsubmit()) {ldelim} document.surveyform.go_to.value = 'next'; document.surveyform.submit(); {rdelim}" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_finish}" onclick="javascript: if (!document.surveyform.onsubmit || document.surveyform.onsubmit()) {ldelim} document.surveyform.go_to.value = 'finish'; document.surveyform.submit(); {rdelim}" />
</div>

{/capture}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_details`" content=$smarty.capture.dialog extra='width="100%"'}
