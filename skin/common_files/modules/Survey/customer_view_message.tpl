{*
$Id: customer_view_message.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$survey.survey}</h1>

{capture name=dialog}

  {$survey.complete|default:$lng.txt_survey_default_complete_message}

  <br />

  {if $section ne "preview"}

    <br />

    {if $survey.publish_results eq 'Y'}
      {include file="customer/buttons/button.tpl" href="survey.php?surveyid=`$survey.surveyid`&amp;mode=view" button_title=$lng.lbl_survey_view_results}
      <br />
    {/if}

    {include file="customer/buttons/button.tpl" href="survey.php" button_title=$lng.lbl_survey_go2surveys}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_survey content=$smarty.capture.dialog noborder=true}

