{*
$Id: customer_answer.tpl,v 1.2 2010/06/21 13:19:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<label for="ans_{$aid|escape}"{if not $readonly} onclick="javascript: checkSelectedAnswer('{$question.answers_type|escape:"javascript"}','{$qid|escape:"javascript"}','{$aid|escape:"javascript"}')"{/if}>
  <input id="ans_{$aid|escape}" {if $question.answers_type eq 'C'}type="checkbox" name="data[{$qid|escape}][answers][]"{else}type="radio" name="data[{$qid|escape}][answers]"{/if} value="{$aid|escape}"{if $a.selected} checked="checked"{/if}{if $readonly} readonly="readonly"{else}{/if} />
  {$a.answer|escape}
</label>
