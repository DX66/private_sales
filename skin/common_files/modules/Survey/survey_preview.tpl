{*
$Id: survey_preview.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="modules/Survey/survey_js.tpl"}

{if not $survey.valid}
  <table cellspacing="1" cellpadding="2">
  <tr>
    <td valign="middle"><img src="{$ImagesDir}/icon_warning_small.gif" class="DialogInfoIcon" alt="" /></td>
    <td>{$lng.txt_survey_is_invalid_warning}</td>
  </tr>
  </table>
  <br />
{/if}

{capture name=dialog}

  <form name="surveyfillform" method="post" action="survey.php" onsubmit="javascript: return false;">
    <input type="hidden" name="surveyid" value="{$survey.surveyid}" />
    <input type="hidden" name="section" value="preview" />
    <input type="hidden" name="mode" value="fill" />

    {include file="modules/Survey/survey.tpl" block_image_verification=true}
    <br />
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_submit type="input"}

  </form>

{/capture}
{include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_survey}
