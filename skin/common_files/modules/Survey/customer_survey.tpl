{*
$Id: customer_survey.tpl,v 1.1.2.1 2010/10/21 13:48:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$survey.survey|amp}</h1>

{include file="modules/Survey/survey_js.tpl"}

{capture name=dialog}
  <form name="surveyfillform" method="post" action="survey.php" onsubmit="javascript: return savePeriod(this);">
    <input type="hidden" name="surveyid" value="{$survey.surveyid}" />
    <input type="hidden" name="mode" value="fill" />
    <input type="hidden" name="survey_key" value="{$survey_key}" />

    {include file="modules/Survey/survey.tpl" no_name=true}

    <div class="button-row">
      {include file="customer/buttons/submit.tpl" type="input" additional_button_class="main-button"}
    </div>

  </form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_survey content=$smarty.capture.dialog noborder=true}
