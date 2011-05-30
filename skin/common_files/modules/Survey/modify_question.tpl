{*
$Id: modify_question.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/multirow.tpl"}
{include file="check_required_fields_js.tpl"}
<script type="text/javascript">
//<![CDATA[
var requiredFields = [
  ["question_text", "{$lng.lbl_survey_question_text|wm_remove|escape:javascript}"]
];
var has_stats = {if $has_stats}true{else}false{/if};
var txt_survey_noempty_deleting_js_note = "{$lng.txt_survey_noempty_deleting_js_note|wm_remove|escape:javascript}";
var txt_survey_noempty_changing_js_note = "{$lng.txt_survey_noempty_changing_js_note|wm_remove|escape:javascript}";
var lbl_survey_deleting_confirm_answers = "{$lng.lbl_survey_deleting_confirm_answers|wm_remove|escape:javascript}";
var qid = {$qid|default:0};
{literal}
function submitSForm() {
  if (!checkRequired(requiredFields))
    return false;

  if (has_stats) {
    if (qid) {
      return confirm(txt_survey_noempty_changing_js_note);

    } else {
      return confirm(txt_survey_noempty_deleting_js_note);
    }
  }

  return true;
}

function changeAnswersBox(mode) {
  if (!document.getElementById('answersBox') || !document.getElementById('ncBox'))
    return false;

  document.getElementById('answersBox').style.display = mode == 'N' ? 'none' : '';
  document.getElementById('ncBox').style.display = mode == 'N' ? 'none' : '';
}

{/literal}
//]]>
</script>

{capture name=dialog}

{$smarty.capture.survey_menu_box}

{include file="buttons/button.tpl" button_title=$lng.lbl_survey_back_to_survey_structure href="survey.php?surveyid=`$surveyid`&section=structure"}

{include file="main/language_selector.tpl" script="survey.php?surveyid=`$surveyid`&qid=`$qid`&section=question&"}

<form action="survey.php" method="post" name="surveyquestionform" onsubmit="javascript: return submitSForm();">
<input type="hidden" name="section" value="question" />
<input type="hidden" name="mode" value="question" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<input type="hidden" name="qid" value="{$qid}" />

{if $has_stats}
<font class="ErrorMessage">{$lng.lbl_warning}! {$lng.txt_survey_noempty_deleting_note}</font><br />
<br />
{/if}

<table cellspacing="1" cellpadding="2">
<tr>
  <td><b>{$lng.lbl_survey_question_text}:</b></td>
  <td><font class="Star">*</font></td>
  <td><textarea name="add_data[question]" cols="55" rows="3" id="question_text">{$question.question}</textarea></td>
</tr>
<tr>
  <td><b>{$lng.lbl_survey_type_of_answers}:</b></td>
  <td>&nbsp;</td>
  <td>
<select name="data[answers_type]" onchange="javascript: changeAnswersBox(this.options[this.selectedIndex].value);">
{foreach from=$answers_types item=at key=atid}
<option value="{$atid}"{if $question.answers_type eq $atid} selected="selected"{/if}>{$at}</option>
{/foreach}
</select>
  </td>
</tr>
<tr id="ncBox">
  <td><b>{$lng.lbl_survey_number_columns_answers_list}:</b></td>
  <td>&nbsp;</td>
  <td><input type="text" size="5" name="data[col]" value="{$question.col}" /></td>
</tr>
<tr>
  <td><b>{$lng.lbl_orderby}:</b></td>
  <td>&nbsp;</td>
  <td><input type="text" size="5" name="data[orderby]" value="{$question.orderby}" /></td>
</tr>
<tr>
  <td><b>{$lng.lbl_survey_mandatory}:</b></td>
  <td>&nbsp;</td>
  <td>
    <select name="data[required]">
      <option value="N"{if $question.required ne "Y"} selected="selected"{/if}>{$lng.lbl_no}</option>
      <option value="Y"{if $question.required eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    </select>
  </td>
</tr>
</table>

<div id="answersBox"{if $question.answers_type eq 'N'} style="display: none;"{/if}>
<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_survey_answers_list}

{if $question.answers}
{include file="main/check_all_row.tpl" style="line-height: 170%;" form="surveyquestionform" prefix="check"}
{/if}

<table cellspacing="1" cellpadding="2" id="answer_table">
<tr class="TableHead">
  <th width="15">&nbsp;</th>
  <th>{$lng.lbl_survey_answers_text}</th>
  <th>&nbsp;{$lng.lbl_survey_text_box}&nbsp;</th>
  <th>&nbsp;{$lng.lbl_orderby}&nbsp;</th>
</tr>
{foreach from=$question.answers item=a key=aid}
<tr{cycle values=', class="TableSubHead"'}>
  <td><input type="checkbox" name="check[]" value="{$aid}" /></td>
  <td><input type="text" size="80" name="answers[{$aid}][answer]" value="{$a.answer|escape}" /></td>
  <td align="center">
<select name="answers[{$aid}][textbox_type]">
<option value="N">{$lng.lbl_no}</option>
<option value="Y"{if $a.textbox_type eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
</select>
  </td>
  <td align="center"><input type="text" size="6" name="answers[{$aid}][orderby]" value="{$a.orderby}" /></td>
</tr>
{/foreach}

{if $question.answers}
<tr>
  <td colspan="4" class="SubmitBox">
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('check\\[\\]', 'gi')) && ((!has_stats &amp;&amp;confirm(lbl_survey_deleting_confirm_answers)) || (has_stats &amp;&amp;confirm(txt_survey_noempty_deleting_js_note)))) submitForm(this, 'delete');" />
  <br /><br /><br />
  {include file="main/subheader.tpl" title=$lng.lbl_survey_add_answers class="grey"}
  </td>
</tr>
{/if}

<tr>
  <td id="answer_box_1">&nbsp;</td>
  <td id="answer_box_2"><input type="text" size="80" name="new_answer[answer][0]" value="{$question.preanswers.0.answer|escape}" /></td>
  <td align="center" id="answer_box_3">
<select name="new_answer[textbox_type][0]">
<option value="N"{if $question.preanswers.0.textbox_type ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
<option value="Y"{if $question.preanswers.0.textbox_type eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
</select>
  </td>
  <td align="center" id="answer_box_4"><input type="text" size="6" name="new_answer[orderby][0]" value="{$question.preanswers.0.orderby}" /></td>
  <td id="answer_add_button">{include file="buttons/multirow_add.tpl" mark="answer" is_lined=true}</td>
</tr>

</table>

{if $question.preanswers}
<script type="text/javascript">
//<![CDATA[
{foreach from=$question.preanswers item=v key=idx}
{if $idx ne 0}
add_inputset_preset('answer', document.getElementById('answer_add_button'), true, 
  [
  {ldelim}regExp: /new_answer\[answer\]/, value: '{$v.answer|escape}'{rdelim},
  {ldelim}regExp: /new_answer\[textbox_type\]/, value: '{$v.textbox_type}'{rdelim},
  {ldelim}regExp: /new_answer\[orderby\]/, value: '{$v.orderby}'{rdelim},
  ]
);
{/if}
{/foreach}
//]]>
</script>
{/if}
</div>

<br />
<hr size="1" class="GreyLine" />
<input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" />

</form>

<script type="text/javascript">
//<![CDATA[
multirowInputSets.answer = {ldelim}noCloneContent: true{rdelim};
//]]>
</script>
{/capture}
{if $qid}
{assign var="dialog_title" value=$lng.lbl_survey_modify_survey_question}
{else}
{assign var="dialog_title" value=$lng.lbl_survey_add_survey_question}
{/if}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_question`" content=$smarty.capture.dialog extra='width="100%"'}
