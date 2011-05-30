{*
$Id: survey.tpl,v 1.1.2.1 2010/10/21 13:48:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if (not $is_menu) and (not $no_name)}
  <h1 class="survey-name">{$survey.survey|amp}</h1>
{/if}

{if $survey.header}
  <div class="survey-header">{$survey.header|amp}</div>
{/if}

{foreach from=$survey.questions item=question key=qid}

  <a name="question{$qid}"></a>
  <div class="survey-question">
{if $is_menu eq ""}
    {counter name="survey`$survey.surveyid`"}.
{/if}
    {$question.question}
{if $question.required eq "Y"}<font class="survey-required">*{if $mandatory_questions_err[$qid] ne ""}<span>&lt;&lt;</span>{/if}</font>{/if}
  </div>
  <div class="survey-answers">

{if $question.answers_type eq 'N'}

    <textarea class="survey-textarea" id="comment_{$qid}" name="data[{$qid}][comment]"{if $readonly} readonly="readonly"{/if} rows="4" cols="40">{$question.comment}</textarea>

{else}

{if $question.col lte 1}

{foreach from=$question.answers item=a key=aid}
    <div class="survey-answer">
      {include file="modules/Survey/customer_answer.tpl"}
{if $a.textbox_type eq 'Y'}
      {include file="modules/Survey/customer_answer_comment.tpl"}
{/if}
    </div>
{/foreach}

{else}

    <table cellspacing="0" width="100%">

{list2matrix assign="answers_matrix" assign_width="cell_width" list=$question.answers row_length=$question.col}

{if $answers_matrix}
{foreach from=$answers_matrix item=row name=answers_matrix}
      <tr>
{foreach from=$row item=a}
{if $a}
{assign var=aid value=$a.answerid}
        <td class="survey-answer" style="width: {$cell_width}%">
          {include file="modules/Survey/customer_answer.tpl" is_column=true}
{if $a.textbox_type eq 'Y'}
          {include file="modules/Survey/customer_answer_comment.tpl"}
{/if}
        </td>
{/if}
{/foreach}
      </tr>

{/foreach}
{/if}

    </table>

{/if}

{/if}
  </div>

{/foreach}

{if $survey.footer}
  <div class="survey-footer">{$survey.footer|amp}</div>
{/if}

{if $active_modules.Image_Verification and $show_antibot.on_surveys eq 'Y' and (not $block_image_verification)}
{if not $is_menu}
  {include file="modules/Image_Verification/spambot_arrest.tpl" mode="simple" id="`$antibot_sections.on_surveys`_`$survey.surveyid`" antibot_err=$antibot_survey_err}
{else}
  {include file="modules/Image_Verification/spambot_arrest.tpl" mode="simple_column" id="`$antibot_sections.on_surveys`_`$menu_survey.surveyid`"}
{/if}
{/if}
