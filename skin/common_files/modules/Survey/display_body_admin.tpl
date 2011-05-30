{*
$Id: display_body_admin.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $question.answers_type eq 'N'}
<textarea readonly="readonly" rows="6" cols="60"></textarea>
{else}
<table cellspacing="1" cellpadding="4">
{if not $question.answers}

<tr>
  <td align="center">{$lng.txt_survey_answers_list_is_empty}</td>
</tr>

{elseif $question.col <= 1}

{foreach from=$question.answers item=a key=aid}
<tr>
  <td width="15"><input type="{if $question.answers_type eq 'C'}checkbox{else}radio{/if}" id="answer_{$aid}" name="preview_{$question.questionid}" /></td>
  <td width="100%"><label for="answer_{$aid}">{$a.answer}</label></td>
</tr>
{if $a.textbox_type eq 'Y'}
<tr>
  <td colspan="2"><textarea rows="4" cols="40" readonly="readonly"></textarea></td>
</tr>
{/if}
{/foreach}

{else}

{list2matrix assign="answers_matrix" assign_width="cell_width" list=$question.answers row_length=$question.col}

{if $answers_matrix}
{foreach from=$answers_matrix item=row name=answers_matrix}
  <tr>
{assign var="answer_exists" value="N"}
{foreach from=$row item=a}
{if $a}
  <td width="15"{if $a.textbox_type ne 'Y'} rowspan="2"{/if}><input type="{if $question.answers_type eq 'C'}checkbox{else}radio{/if}" id="answer_{$aid}" name="preview_{$question.questionid}" /></td>
  <td{if $a.textbox_type ne 'Y'} rowspan="2"{/if}><label for="answer_{$aid}">{$a.answer}</label></td>
{assign var="answer_exists" value="Y"}
{/if}  
{/foreach}
{if $answer_exists eq "Y"}
</tr>
{else}
  <td>&nbsp;</td>
</tr>
{/if}
<tr>
{assign var="answer_exists" value="N"}
{foreach from=$row item=a}
{if $a and $a.textbox_type eq 'Y'}
  <td colspan="2" valign="top"><textarea rows="2" cols="20" readonly="readonly"></textarea></td>
  {assign var="answer_exists" value="Y"}  
{/if}
{/foreach}
{if $answer_exists eq "Y"}
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
