{*
$Id: survey_instances.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var txt_survey_noempty_changing_js_note = "{$lng.txt_survey_noempty_changing_js_note|wm_remove|escape:javascript}";
//]]>
</script>
{if not $survey.result}

{capture name=dialog}

{$smarty.capture.survey_menu_box}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_survey_display_statistics href="survey.php?surveyid=`$survey.surveyid`&section=stats"}</div>

<br />

<div align="left">{include file="modules/Survey/survey_filter.tpl" date_from=$filter.date_from date_to=$filter.date_to}</div>

<br />

{if $results}

<form action="survey.php" method="post" name="surveyinstancesform">
<input type="hidden" name="surveyid" value="{$surveyid}" />
<input type="hidden" name="section" value="instances" />
<input type="hidden" name="mode" value="" />

{if $total_items gt "0"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}

<br />
<br />

{include file="main/navigation.tpl"}

<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
  <td width="1%">&nbsp;</td>
  <th width="15%" align="left" nowrap="nowrap">{$lng.lbl_date}</th>
  <th width="20%">{$lng.lbl_username}</th>
  <th width="14%">{$lng.lbl_ip_address}</th>
  <th width="14%">{$lng.lbl_survey_completed}</th>
  <th width="12%">{$lng.lbl_survey_note}</th>
</tr>
{foreach from=$results item=result key=sresultid}
<tr{cycle values=', class="TableSubHead"'}>
  <td><input type="checkbox" name="check[]" value="{$sresultid}" /></td>
  <td><a href="survey.php?section=instances&amp;surveyid={$surveyid}&amp;sresultid={$sresultid}">{$result.date|date_format:$config.Appearance.datetime_format}</a></td>
  <td align="center">
{if $result.login eq ''}
{$lng.lbl_survey_anonymous_user}
{else}
<a href="user_modify.php?user={$result.userid}&amp;usertype=C">{$result.login}</a>
{/if}
  </td>
  <td align="center">{$result.ip|default:$lng.txt_not_available}</td>
  <td align="center">{$result.completed_msg|default:$lng.txt_not_available}</td>
  <td align="center">
{if $result.obj_link}
{if $result.obj_link.link}<a href="{$result.obj_link.link|amp}">{/if}
{$result.obj_link.obj_name}
{if $result.obj_link.link}</a>{/if}
{else}
-
{/if}
  </td>
</tr>
{/foreach}
</table>

<br />
<br />

{include file="main/navigation.tpl"}

<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('check\\[\\]', 'gi')) &amp;&amp;confirm(txt_survey_noempty_changing_js_note)) submitForm(this, 'delete');" />
</form>

{else}

<br />
<br />

<div align="center">{$lng.lbl_survey_has_never_been_completed}</div>

<br />

{/if}

{/capture}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_instances`" content=$smarty.capture.dialog extra='width="100%"'}

{else}

{capture name=dialog}

{$smarty.capture.survey_menu_box}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_survey_instances_back_link href="survey.php?surveyid=`$surveyid`&section=instances"}</div>

<br />

<table cellpadding="3" cellspacing="1">
<tr>
  <td colspan="2"><b>{$lng.lbl_survey_survey_instance_id|substitute:"id":$survey.result.sresultid}</b></td>
</tr>
<tr>
  <td><b>{$lng.lbl_survey_submited}:</b></td>
  <td>{$survey.result.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
<tr>
  <td><b>{$lng.lbl_survey_instance_user}:</b></td>
  <td>
{if $survey.result.login}
<a href="user_modify.php?user={$survey.result.userid}&amp;usertype=C">{$survey.result.login}</a>
{else}
{$lng.lbl_survey_anonymous_user}
{/if}
  </td>
</tr>
<tr>
  <td><b>{$lng.lbl_survey_instance_ip}:</b></td>
  <td>{$survey.result.ip|default:$lng.txt_not_available}</td>
</tr>
</table>

<br />
<br />

<table cellspacing="1" cellpadding="2" width="100%">

{foreach from=$survey.questions item=q key=qid}
<tr{cycle values=', class="TableSubHead"' advance=false}>
    <td><b>{counter}. {$q.question}</b></td>
</tr>
<tr{cycle values=', class="TableSubHead"'}>
    <td>{include file="modules/Survey/display_result.tpl" question=$q}<br /></td>
</tr>
{foreachelse}

<tr colspan="2">
    <td align="center">{$lng.txt_survey_question_list_is_empty}</td>
</tr>

{/foreach}

</table>

{/capture}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_instance`" content=$smarty.capture.dialog extra='width="100%"'}

{/if}
