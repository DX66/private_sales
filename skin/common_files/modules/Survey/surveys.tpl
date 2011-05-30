{*
$Id: surveys.tpl,v 1.4 2010/06/15 07:03:26 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var lbl_survey_deleting_confirm = "{$lng.lbl_survey_deleting_confirm|wm_remove|escape:javascript}";
//]]>
</script>
{include file="page_title.tpl" title=$lng.lbl_survey_surveys}

<br />

{$lng.txt_survey_top_text}

<br /><br />

{capture name=dialog}

<br />

{if $surveys}

<form action="surveys.php" method="post" name="surveysform">
<input type="hidden" name="mode" value="update" />

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="surveysform" prefix="check"}

<table cellspacing="1" cellpadding="2" width="100%">
<tr class="TableHead">
  <th width="10">&nbsp;</th>
  <th width="20%" align="left">{$lng.lbl_survey}</th>
  <th width="20%">{$lng.lbl_survey_maillist}</th>
  <th width="20%">{$lng.lbl_survey_completed}</th>
  <th width="20%">{$lng.lbl_survey_recently_completed}</th>
  <th width="10%">{$lng.lbl_status}</th>
  <th width="10%">{$lng.lbl_orderby}</th>
</tr>

{foreach from=$surveys key=surveyid item=s}
<tr{cycle values=', class="TableSubHead"'}>
  <td><input type="checkbox" name="check[]" value="{$surveyid}" /></td>
  <td><a href="survey.php?surveyid={$surveyid}">{$s.survey}</a></td>
  <td align="center"><a href="survey.php?surveyid={$surveyid}&amp;section=maillist">{if $s.count_maillist gt 0}{$s.count_maillist} ({$s.count_sent|default:0}){else}{$lng.txt_not_available}{/if}</a></td>
  <td align="center">{if $s.count_completed gt 0}<a href="survey.php?surveyid={$surveyid}&amp;section=stats">{$s.count_completed}</a>{else}{$lng.txt_not_available}{/if}</td>
  <td align="center">{if $s.max_completed gt 0}<a href="survey.php?surveyid={$surveyid}&amp;section=instances{if $s.last_sresultid gt 0}&amp;sresultid={$s.last_sresultid}{/if}">{$s.max_completed|date_format:$config.Appearance.datetime_format}</a>{else}{$lng.txt_not_available}{/if}</td>
  <td align="center">
<select name="data[{$surveyid}][survey_type]">
{foreach from=$survey_types key=typeid item=t}
  <option value="{$typeid}"{if $s.survey_type eq $typeid} selected="selected"{/if}>{$t.label}</option>
{/foreach}
</select>
  </td>
  <td align="center"><input type="text" size="5" name="data[{$surveyid}][orderby]" value="{$s.orderby}" /></td>
</tr>
{/foreach}

<tr>
  <td colspan="7"><br />{$lng.txt_survey_list_note}</td>
</tr>

<tr>

  <td colspan="2" class="SubmitBox main-button">
    <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>

  <td colspan="5" align="right">

    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^check\\[\\]', 'gi')) &amp;&amp; confirm(lbl_survey_deleting_confirm)) submitForm(this, 'delete');" />&nbsp;
    <input type="button" value="{$lng.lbl_survey_clone_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^check\\[\\]', 'gi'))) submitForm(this, 'clone');" />&nbsp;
    <input type="button" value="{$lng.lbl_survey_send_invitations|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^check\\[\\]', 'gi'))) submitForm(this, 'send');" />
  </td>
</tr>

</table>

</form>

{else}

{$lng.txt_survey_list_is_empty}

<br />
<br />

{/if}

<br />

<div class="main-button">
  <input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='survey.php?mode=create';" />
</div>

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_survey_surveys content=$smarty.capture.dialog extra='width="100%"'}
