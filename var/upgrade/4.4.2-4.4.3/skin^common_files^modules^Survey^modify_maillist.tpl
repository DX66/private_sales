{*
$Id: modify_maillist.tpl,v 1.4 2010/06/15 07:58:18 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/multirow.tpl"}
<script type="text/javascript" src="{$SkinDir}/js/popup_product.js"></script>
<script type="text/javascript">
//<![CDATA[
var txt_survey_list_is_empty = "{$lng.txt_survey_list_is_empty|wm_remove|escape:javascript}";
var lbl_news_list_is_empty = "{$lng.lbl_survey_news_list_is_empty|wm_remove|escape:javascript}";
var txt_import_file_wasnt_assigned = "{$lng.txt_survey_import_file_wasnt_assigned|wm_remove|escape:javascript}";
var txt_respondents_list_clean_note = "{$lng.txt_survey_respondents_list_clean_note|wm_remove|escape:javascript}";
var txt_send_invitations = "{$lng.txt_survey_send_invitations_js_note|wm_remove|escape:javascript}";
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/modules/Survey/modify_maillist.js"></script>

{capture name=dialog}

{$smarty.capture.survey_menu_box}

<form action="survey.php" method="post" name="surveymaillistform">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="maillist" />
<input type="hidden" name="surveyid" value="{$surveyid}" />

{include file="main/subheader.tpl" title=$lng.lbl_survey_invitations_list}

{if $maillist}

{include file="main/navigation.tpl"}

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="surveymaillistform" prefix="check"}

<table cellspacing="1" cellpadding="3" width="90%" id="email_table">
<tr class="TableHead">
  <th width="15">&nbsp;</th>
  {if $config.email_as_login ne 'Y'}
  <th width="25%">{$lng.lbl_email}</th>
  {/if}
  <th width="25%">{$lng.lbl_username}</th>
  <th width="{if $config.email_as_login ne 'Y'}45{else}70{/if}%">{$lng.lbl_status}</th>
</tr>
{foreach from=$maillist item=m}
<tr{cycle values=', class="TableSubHead"'}>
  <td><input type="checkbox" name="check[]" value="{$m.email}" /></td>
  {if $config.email_as_login ne 'Y'}
  <td nowrap="nowrap">{$m.email}</td>
  {/if}
  <td nowrap="nowrap" align="center">{if $m.login}<a href="user_modify.php?user={$m.userid}&amp;usertype=C">{$m.login}</a>{else}{$lng.txt_not_available}{/if}</td>
  <td align="center" nowrap="nowrap">
{if $m.complete_date}
{assign var="date" value=$m.complete_date|date_format:$config.Appearance.datetime_format}
{$lng.lbl_survey_status_completed|substitute:"date":$date}
{elseif $m.sent_date}
{assign var="date" value=$m.sent_date|date_format:$config.Appearance.datetime_format}
{$lng.lbl_survey_status_sent|substitute:"date":$date}
{else}
{$lng.lbl_survey_status_queued}
{/if}
  </td>
</tr>
{/foreach}

</table>

<br />
{include file="main/navigation.tpl"}

<table cellpadding="0" cellspacing="0">

<tr>
  <td class="SubmitBox" colspan="5">

{$lng.lbl_survey_send_invitations_note}<br />
<br />

<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('check\\[\\]', 'gi'))) submitForm(this, 'delete');" />
<input type="button" value="{$lng.lbl_survey_send_invitations|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('check\\[\\]', 'gi')) &amp;&amp;confirm(txt_send_invitations)) submitForm(this, 'send');" />

<br /><br />

<input type="button" value="{$lng.lbl_delete_all|strip_tags:false|escape}" onclick="javascript: if (confirm(txt_respondents_list_clean_note)) submitForm(this, 'clear_list');" />

<input type="button" value="{$lng.lbl_survey_send_invitations_for_unsent|strip_tags:false|escape}" onclick="javascript: if (confirm(txt_send_invitations)) submitForm(this, 'send_all');" />

  </td>
</tr>

</table>

{else}

<br />

<div style="PADDING-LEFT: 50px;">{$lng.txt_survey_maillist_is_empty}</div>

{/if}

</form>

<br />
<br />
<br />

<table cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td class="SurveyTabSelected" id="inv_auto_switch" onclick="javascript: selectTab(this, 'inv', 'auto');">{$lng.lbl_survey_auto_invitations}</td>
  <td class="SurveyTab" id="inv_man_switch" onclick="javascript: selectTab(this, 'inv', 'man');">{$lng.lbl_survey_manual_invitations}</td>
  <td width="100%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
  <td colspan="3" class="SurveyTabLine"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr>

</table>

<br />

<table cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td id="inv_man_part" style="display: none;">

<script type="text/javascript">
//<![CDATA[
var boxes = ['manual','users','news','respondents','import'];

{literal}
function changeBox(code) {
  for (var i = 0; i < boxes.length; i++) {
    var obj = document.getElementById('box_'+boxes[i]);
    if (!obj)
      continue;

    obj.style.display = (boxes[i] == code) ? '' : 'none';
  }
}
{/literal}
//]]>
</script>

<b>{$lng.txt_survey_select_respondents_source}:</b>

<br /><br />

<table cellspacing="1" cellpadding="2">
<tr>
  <td><input type="radio" name="change_box" checked="checked" id="radio_manual" onclick="javascript: changeBox('manual');" /></td>
  <td><label for="radio_manual">{$lng.lbl_survey_add_respondents_manually}</label></td>
</tr>
<tr>
  <td><input type="radio" name="change_box" id="radio_users" onclick="javascript: changeBox('users');" /></td>
  <td><label for="radio_users">{$lng.lbl_survey_add_respondents_from_users}</label></td>
</tr>
<tr>
  <td><input type="radio" name="change_box" id="radio_news" onclick="javascript: changeBox('news');" /></td>
  <td><label for="radio_news">{$lng.lbl_survey_add_respondents_from_subscr}</label></td>
</tr>
<tr>
  <td><input type="radio" name="change_box" id="radio_respondents" onclick="javascript: changeBox('respondents');" /></td>
  <td><label for="radio_respondents">{$lng.lbl_survey_add_respondents_from_survey}</label></td>
</tr>
<tr>
  <td><input type="radio" name="change_box" id="radio_import" onclick="javascript: changeBox('import');" /></td>
  <td><label for="radio_import">{$lng.lbl_survey_import_respondents}</label></td>
</tr>
</table>

<br /><br />
<div id="box_manual">
{include file="main/subheader.tpl" title=$lng.lbl_survey_add_respondents_manually class="grey"}

<form action="survey.php" method="post" name="surveyaddemailsform" onsubmit="javascript: return check_emailslist(this);">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="surveyid" value="{$surveyid}" />

<table cellspacing="1" cellpadding="2">

<tr>
    <td id="email_box_1"><b>{$lng.lbl_email}:</b></td>
  <td id="email_box_2"><input type="text" size="50" maxlength="128" name="new_email[0]" /></td>
    <td>{include file="buttons/multirow_add.tpl" mark="email" is_lined=true}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="SubmitRow"><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

</div>

<div id="box_users" style="display: none;">
{include file="main/subheader.tpl" title=$lng.lbl_survey_add_respondents_from_users class="grey"}

<script type="text/javascript" src="{$SkinDir}/js/popup_users_open.js"></script>

<form action="survey.php" method="post" name="surveyaddusersform">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="add_users" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<input type="hidden" name="userids" value="" />

<table cellspacing="1" cellpadding="2">
<tr>
  <td>&nbsp;</td>
  <td class="SubmitRow">
<input type="button" value="{$lng.lbl_search_users|strip_tags:false|escape}" onclick="javascript: open_popup_users('surveyaddusersform', '~~email~~ (~~firstname~~ ~~lastname~~)', true);" />
  </td>
</tr>
</table>
</form>
</div>

{if $active_modules.News_Management}
<div id="box_news" style="display: none;">
{include file="main/subheader.tpl" title=$lng.lbl_survey_add_respondents_from_subscr class="grey"}

<form action="survey.php" method="post" name="surveyaddnewsusersform" onsubmit="javascript: return check_newslist(this);">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="add_news_users" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<table cellspacing="1" cellpadding="2">

{if $news_list}

<tr>
    <td nowrap="nowrap"><b>{$lng.lbl_news_lists}:</b></td>
    <td>
<select name="newslist[]" multiple="multiple" size="5">
{foreach from=$news_list item=n}
<option value="{$n.listid}">{$n.name}</option>
{/foreach}
</select>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>{$lng.lbl_hold_ctrl_key}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td class="SubmitRow"><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
</tr>

{else}

<tr>
  <td align="center">{$lng.lbl_survey_news_list_is_empty}</td>
</tr>

{/if}
</table>
</form>
</div>
{/if}

<div id="box_respondents" style="display: none;">
{include file="main/subheader.tpl" title=$lng.lbl_survey_add_respondents_from_survey class="grey"}
<form action="survey.php" method="post" name="surveyaddsurveyusersform" onsubmit="javascript: return check_surveys(this);">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="add_survey_users" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<table cellspacing="1" cellpadding="2">

{if $another_surveys}

<tr>
  <td nowrap="nowrap"><b>{$lng.lbl_survey_surveys}:</b></td>
  <td>
<select name="surveylist[]" multiple="multiple" size="5">
{foreach from=$another_surveys item=s key=sid}
<option value="{$sid}">{$s}</option>
{/foreach}
</select>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>{$lng.lbl_hold_ctrl_key}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="SubmitRow"><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /><td>
</tr>

{else}

<tr>
  <td align="center">{$lng.txt_survey_list_is_empty}</td>
</tr>

{/if}
</table>
</form>
</div>

<div id="box_import" style="display: none;">
{include file="main/subheader.tpl" title=$lng.lbl_survey_import_respondents class="grey"}

<br />
{$lng.txt_survey_respondents_import_format}<br />
<br />

<form action="survey.php" method="post" name="surveyimportusersform" enctype="multipart/form-data"  onsubmit="javascript: return check_import(this);">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="import_users" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<table cellspacing="1" cellpadding="2">
<tr>
  <td><b>{$lng.lbl_file_for_upload}:</b></td>
  <td><input type="file" size="32" name="userfile" /></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="SubmitBox"><input type="submit" value="{$lng.lbl_import|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
</div>

  </td>
  <td id="inv_auto_part">{include file="modules/Survey/modify_events.tpl"}</td>
</tr>
</table>

<div style="text-align: center; width: 100%;">
<hr size="1" class="GreyLine" />
<input type="button" value="{$lng.lbl_finish}" onclick="javascript: document.surveyeventsform.go_to.value = 'finish'; document.surveyeventsform.submit();" />
</div>

{if $show}
<script type="text/javascript">
//<![CDATA[
selectTab(document.getElementById('inv_man_switch'), 'inv', 'man');
document.getElementById('radio_{$show}').checked = true;
document.getElementById('radio_{$show}').onclick();
//]]>
</script>
{/if}

{/capture}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_maillist`" content=$smarty.capture.dialog extra='width="100%"'}
