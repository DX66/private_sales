{*
$Id: display_result.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $question.answers_type eq 'N'}
<textarea readonly="readonly" rows="6" cols="60">{$question.comment}</textarea>
{else}
<table cellspacing="1" cellpadding="2">
{if not $question.answers}

<tr>
  <td align="center">{$lng.txt_survey_answers_list_is_empty}</td>
</tr>

{else}

{foreach from=$question.answers item=a key=qid}
<tr>
  <td width="15"><input type="{if $question.answers_type eq 'C'}checkbox{else}radio{/if}"{if $a.selected} checked="checked"{/if} disabled="disabled" /></td>
  <td>{$a.answer}</td>
</tr>
{if $a.textbox_type eq 'Y'}
<tr>
  <td colspan="2"><textarea rows="4" cols="40" readonly="readonly">{$a.comment}</textarea></td>
</tr>
{/if}
{/foreach}

{/if}
</table>
{/if}
