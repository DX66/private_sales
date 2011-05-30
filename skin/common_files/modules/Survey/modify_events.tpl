{*
$Id: modify_events.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
{literal}
function changeEType(obj) {
  var obj = document.getElementById('allow_events');
  var box = document.getElementById('etypes_box');
  if (!obj || !box)
    return false;

  box.style.display = (obj.checked ? '' : 'none');
}
{/literal}
//]]>
</script>

<form action="survey.php" method="post" name="surveyeventsform">
<input type="hidden" name="section" value="maillist" />
<input type="hidden" name="mode" value="events" />
<input type="hidden" name="surveyid" value="{$surveyid}" />
<input type="hidden" name="delete_param" value="" />
<input type="hidden" name="go_to" value="" />

<table cellspacing="0" cellpadding="0">
<tr>
  <td><input type="checkbox" name="allow_events" id="allow_events" value="Y"{if $survey.event_type} checked="checked"{/if} onclick="javascript: changeEType();" /></td>
  <td style="padding-left: 3px;"><label for="allow_events">{$lng.lbl_survey_allow_events}</label></td>
</tr>
</table>

<div id="etypes_box"{if not $survey.event_type} style="display: none;"{/if}>

<br />
<b>{$lng.lbl_survey_auto_add_respondents_note}:</b>

<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_survey_event_type}

<table cellspacing="1" cellpadding="2">
<tr>
  <td valign="top" style="padding-top: 6px;">{$lng.lbl_survey_event}:</td>
  <td>

<table cellspacing="1" cellpadding="2">
{foreach from=$survey_events item=e key=eid}
<tr>
  <td><input type="radio" name="event_type" id="event_{$eid}" value="{$eid}"{if $survey.event_type eq $eid} checked="checked"{/if} /></td>
  <td><label for="event_{$eid}">{$e}</label></td>
</tr>
{/foreach}
</table>

  </td>
</tr>
</table>

<br />

{include file="main/subheader.tpl" title=$lng.lbl_survey_event_parameters}

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.lbl_survey_event_join_logic}:</td>
  <td>
<select name="event_logic">
  <option value="O">OR</option>
  <option value="A"{if $survey.event_logic eq 'A'} selected="selected"{/if}>AND</option>
</select>
  </td>
</tr>
</table>
<br />

{include file="modules/Survey/modify_event_total.tpl"}
<br />
{include file="modules/Survey/modify_event_products.tpl"}
<br />
{include file="modules/Survey/modify_event_categories.tpl"}

</div>

<br />

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

</form>
