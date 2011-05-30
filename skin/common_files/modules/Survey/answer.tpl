{*
$Id: answer.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<td class="survey-answer-mark{$survey_style_suffix}"{if $is_column} valign="top"{if $a.textbox_type ne 'Y'} rowspan="2"{/if}{/if}>
  <input {if $question.answers_type eq 'C'}type="checkbox" name="data[{$qid}][answers][]"{else}type="radio" name="data[{$qid}][answers]"{/if} id="ans_{$aid}" value="{$aid}"{if $a.selected} checked="checked"{/if}{if $readonly} readonly="readonly"{/if} />
</td>
<td class="survey-answer{$survey_style_suffix}"{if $is_column} valign="top"{if $a.textbox_type ne 'Y'} rowspan="2"{/if}{/if}>
  <label for="ans_{$aid}">{$a.answer}</label>
</td>
