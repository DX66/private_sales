{*
$Id: survey_status.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{$smarty.capture.survey_menu_box}

<br />
<br />

{if $survey.valid}
<div style="COLOR: green; FONT-WEIGHT: bold; FONT-SIZE: 12px; TEXT-ALIGN: center;">{$lng.lbl_survey_is_active}</div>
{else}
<div style="COLOR: red; FONT-WEIGHT: bold; FONT-SIZE: 12px; TEXT-ALIGN: center;">{$lng.lbl_survey_isnt_active}</div>
{/if}

<br />
<br />

{if $survey.error_messages ne ''}

{assign var="cnt" value=0}
{foreach from=$survey.error_messages item=msg}
{if not $msg.warning}
{if $cnt eq 0}
{include file="main/subheader.tpl" title=$lng.lbl_survey_errors}
<table cellspacing="1" cellpadding="3">

{/if}
{inc assign="cnt" value=$cnt}
<tr>
    <td style="padding-left: 20px">
{$cnt}. {$msg.label}
{if $msg.go_link ne ''}
  <a href="survey.php?surveyid={$surveyid}&amp;section={$msg.go_link}">{$lng.lbl_survey_go_and_correct}</a>
{/if}
  </td>
</tr>

{/if}
{/foreach}

{if $cnt gt 0}
</table>

<br />
<br />

{/if}

{assign var="cnt" value=0}
{foreach from=$survey.error_messages item=msg}
{if $msg.warning}
{if $cnt eq 0}
{include file="main/subheader.tpl" title=$lng.lbl_survey_warnings}
<table cellspacing="1" cellpadding="3">

{/if}
{inc assign="cnt" value=$cnt}
<tr>
    <td style="padding-left: 20px">{$cnt}. {$msg.label}</td>
</tr>

{/if}
{/foreach}

{if $cnt gt 0}
</table>

<br />
<br />

{/if}

{/if}

{/capture}
{include file="dialog.tpl" title="`$survey.survey`: `$lng.lbl_survey_status`" content=$smarty.capture.dialog extra='width="100%"'}
