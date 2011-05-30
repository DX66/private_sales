{*
$Id: survey_modify.tpl,v 1.2 2010/07/26 07:08:26 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_survey_management}

<br />

{$lng.txt_survey_modify_top_text}

<br /><br />

{capture name="survey_menu_box"}

<table cellpadding="4" cellspacing="0" width="100%">
<tr>
  <td width="10%">&nbsp;</td>

  <td align="center" width="70%">
  <table cellpadding="1" cellspacing="1">
  <tr>
    <td><a href="survey.php?surveyid={$survey.surveyid}&amp;section=structure"{if $section eq "structure" or $section eq "question"} style="FONT-WEIGHT: bold;"{/if}>{$lng.lbl_survey_menu_structure}</a></td>
    <td><img src="{$ImagesDir}/spacer.gif" width="20" height="1" alt="" /></td>
    <td><a href="survey.php?surveyid={$survey.surveyid}"{if $section eq ""} style="FONT-WEIGHT: bold;"{/if}>{$lng.lbl_survey_menu_details}</a></td>
    <td><img src="{$ImagesDir}/spacer.gif" width="20" height="1" alt="" /></td>
    <td><a href="survey.php?surveyid={$survey.surveyid}&amp;section=maillist"{if $section eq "maillist"} style="FONT-WEIGHT: bold;"{/if}>{$lng.lbl_survey_invitations}</a></td>
    <td><img src="{$ImagesDir}/spacer.gif" width="20" height="1" alt="" /></td>
    <td><a href="survey.php?surveyid={$survey.surveyid}&amp;section=stats"{if $section eq "stats" or $section eq "instances"} style="FONT-WEIGHT: bold;"{/if}>{$lng.lbl_survey_menu_statistics}</a></td>
  </tr>
  </table>
  </td>
  
  <td width="20%" align="right">
  
  <table cellpadding="1" cellspacing="1">
  <tr>
    <td><a href="#" onclick="javascript:void(window.open('survey.php?section=preview&amp;surveyid={$survey.surveyid}', 'surveypreview', 'width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no'));">{$lng.lbl_preview}</a></td>
    <td style="padding-left: 20px;">
<a href="survey.php?surveyid={$survey.surveyid}&amp;section=status" style="color: {if $survey.valid eq ''}red{elseif $survey.error_messages ne ""}blue{else}green{/if}{if $section eq "status"} font-weight: bold;{/if}">
{if $survey.valid eq ''}
{$lng.lbl_survey_menu_status_inactive}
{elseif $survey.error_messages ne ""}
{$lng.lbl_survey_menu_status_warnings}
{else}
{$lng.lbl_survey_menu_status_active}
{/if}
</a>
    </td>
  </tr>
  </table>

  </td>

</tr>
</table>

<hr size="1" class="GreyLine" />
<br />

{/capture}

{if $section eq 'structure'}
{include file="modules/Survey/modify_structure.tpl"}

{elseif $section eq 'question'}
{include file="modules/Survey/modify_question.tpl"}

{elseif $section eq 'maillist'}
{include file="modules/Survey/modify_maillist.tpl"}

{elseif $section eq 'stats'}
{include file="modules/Survey/survey_stats.tpl"}

{elseif $section eq 'instances'}
{include file="modules/Survey/survey_instances.tpl"}

{elseif $section eq 'status'}
{include file="modules/Survey/survey_status.tpl"}

{else}
{include file="modules/Survey/modify_survey.tpl"}

{/if}
