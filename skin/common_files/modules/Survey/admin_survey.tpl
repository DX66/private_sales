{*
$Id: admin_survey.tpl,v 1.1.2.1 2011/03/09 07:13:04 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if not $is_menu}
<h1 class="survey-name">{$survey.survey}</h1>
{assign var="survey_style_suffix" value=""}
{else}
{assign var="survey_style_suffix" value="Menu"}
{/if}

{if $survey.header}
<span class="survey-header{$survey_style_suffix}">{$survey.header}</span><br /><br />
{/if}

<table cellspacing="1" summary="{$survey.survey|escape}">
{foreach from=$survey.questions item=question key=qid}
<tr>
  <td class="survey-question{$survey_style_suffix}">
{if $is_menu eq ""}
{counter name="survey`$survey.surveyid`"}.
{/if}
{$question.question}
  </td>
</tr>
<tr>
  <td class="survey-answers{$survey_style_suffix}">

{if $question.answers_type eq 'N'}
<textarea {if $is_menu}rows="3" cols="20"{else}rows="6" cols="60"{/if} class="Survey" name="data[{$qid}][comment]"{if $readonly} readonly="readonly"{/if}>{$question.comment}</textarea>
{else}
<table cellspacing="1" summary="{$question.question|escape}">
{if $question.col lte 1}

{foreach from=$question.answers item=a key=aid}
<tr>
{include file="modules/Survey/answer.tpl"}
</tr>
{if $a.textbox_type eq 'Y'}
<tr>
{include file="modules/Survey/answer_comment.tpl"}
</tr>
{/if}
{/foreach}

{else}

{list2matrix assign="answers_matrix" assign_width="cell_width" list=$question.answers row_length=$question.col}

{if $answers_matrix}
{foreach from=$answers_matrix item=row name=answers_matrix}
<tr>
{foreach from=$row item=a}
{if $a}
{assign var=aid value=$a.answerid}
{include file="modules/Survey/answer.tpl" is_column=true}
{/if}
{/foreach}
</tr>
<tr>
{assign var="comment_exists" value="N"}
{foreach from=$row item=a}
{if $a}
{assign var=aid value=$a.answerid}
{if $a.textbox_type eq 'Y'}
{include file="modules/Survey/answer_comment.tpl" is_column=true}
{assign var="comment_exists" value="Y"}
{/if}
{/if}
{/foreach}
{if $comment_exists eq "Y"}
</tr>
{else}
  <td>&nbsp;</td>
</tr>
{/if}
{/foreach}
{/if}

{/if}
</table>
{/if}

  </td>
</tr>
{/foreach}
</table>
{if $survey.footer}
<span class="survey-footer{$survey_style_suffix}">{$survey.footer}</span><br />
{/if}

{if $active_modules.Image_Verification and $show_antibot.on_surveys eq 'Y' and not $block_image_verification}
{if not $is_menu}
  {include file="modules/Image_Verification/spambot_arrest.tpl" mode="simple" id="`$antibot_sections.on_surveys`_`$survey.surveyid`" antibot_err=$antibot_survey_err antibot_name_prefix='_survey'}
{else}
  {include file="modules/Image_Verification/spambot_arrest.tpl" mode="simple_column" id="`$antibot_sections.on_surveys`_`$menu_survey.surveyid`" antibot_name_prefix='_survey'}
{/if}
{/if}
