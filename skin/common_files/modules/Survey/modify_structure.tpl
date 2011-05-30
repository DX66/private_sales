{*
$Id: modify_structure.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{$smarty.capture.survey_menu_box}

<script type="text/javascript">
//<![CDATA[
{if $has_stats}
var txt_survey_noempty_deleting_js_note = "{$lng.txt_survey_noempty_deleting_js_note|wm_remove|escape:javascript}";
var txt_survey_noempty_changing_js_note = "{$lng.txt_survey_noempty_changing_js_note|wm_remove|escape:javascript}";
{else}
var lbl_survey_deleting_confirm_questions = "{$lng.lbl_survey_deleting_confirm_questions|wm_remove|escape:javascript}";
{/if}
//]]>
</script>

<form action="survey.php" method="post" name="surveystructureform">
<input type="hidden" name="section" value="structure" />
<input type="hidden" name="mode" value="structure" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<input type="hidden" name="go_to" value="" />

<h1 align="center">{$survey.survey}</h1>

{$survey.header}<br />
<br />

<table cellspacing="1" cellpadding="3" width="100%">
<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td>{$lng.lbl_orderby}</td>
  <td>{$lng.lbl_columns}</td>
  <td>{$lng.lbl_survey_mandatory}</td>
  <td width="100%">{$lng.lbl_survey_question}</td>
</tr>

{foreach from=$survey.questions item=q key=qid}
<tr{cycle values=' class="TableSubHead",' advance=false}>
  <td valign="top"><input type="checkbox" name="check[]" value="{$qid}" /></td>
  <td valign="top" align="center"><input type="text" size="5" name="data[{$qid}][orderby]" value="{$q.orderby}" /></td>
  <td valign="top" align="center"><input type="text" size="5" name="data[{$qid}][col]" value="{$q.col}" /></td>
  <td valign="top" align="center">
    <select name="data[{$qid}][required]">
      <option value="N"{if $q.required ne "Y"} selected="selected"{/if}>{$lng.lbl_no}</option>
      <option value="Y"{if $q.required eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    </select>
  </td>
  <td style="padding-left: 20px;">
    <table>
    <tr>
      <td><a href="survey.php?surveyid={$surveyid}&amp;section=question&amp;qid={$qid}" class="Button">{$q.question}</a></td>
    </tr>  
    <tr>
      <td>{include file="modules/Survey/display_body_admin.tpl" question=$q}<br /></td>
    </tr>
    </table>
  </td>
</tr>
{foreachelse}

<tr>
  <td align="center" colspan="5">{$lng.txt_survey_question_list_is_empty}</td>
</tr>

{/foreach}

{if $survey.questions}
<tr>
  <td colspan="5"><hr size="1" /></td>
</tr>
<tr>
  <td colspan="5" class="SubmitBox">
{if $has_stats}
<font class="ErrorMessage">{$lng.lbl_warning}! {$lng.txt_survey_noempty_deleting_note}</font><br />
<br />
<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: if (confirm(txt_survey_noempty_changing_js_note)) this.form.submit();" />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('check\\[\\]', 'gi')) &amp;&amp; confirm(txt_survey_noempty_deleting_js_note)) submitForm(this, 'delete');" />
{else}
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('check\\[\\]', 'gi')) &amp;&amp; confirm(lbl_survey_deleting_confirm_questions)) submitForm(this, 'delete');" />
{/if}
  </td>
</tr>
{/if}

</table>

</form>

<br />

{$survey.footer}

<br /><br />
<hr size="1" class="GreyLine" />

<input type="button" value="{$lng.lbl_survey_add_survey_question|strip_tags:false|escape}" onclick="javascript: self.location='survey.php?surveyid={$surveyid}&amp;section=question'" />

<div style="text-align: center; width: 100%;">
<hr size="1" class="GreyLine" />
{if $has_stats}
<input type="button" value="{$lng.lbl_next}" onclick="javascript: if (confirm(txt_survey_noempty_changing_js_note)) {ldelim} document.surveystructureform.go_to.value = 'next'; document.surveystructureform.submit();{rdelim}"{if not $survey.questions} disabled="disabled"{/if} />
{else}
<input type="button" value="{$lng.lbl_next}" onclick="javascript: document.surveystructureform.go_to.value = 'next'; document.surveystructureform.submit();"{if not $survey.questions} disabled="disabled"{/if} />
{/if}
</div>

{/capture}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_structure`" content=$smarty.capture.dialog extra='width="100%"'}
