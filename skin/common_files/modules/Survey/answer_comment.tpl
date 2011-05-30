{*
$Id: answer_comment.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<td colspan="2"{if $is_column} valign="top"{/if} class="SurveyAnswerComment{$survey_style_suffix}">
<textarea {if $is_column or $is_menu}rows="2" cols="20"{else}rows="4" cols="40"{/if} class="Survey" name="data[{$qid}][comment][{$aid}]"{if $readonly} readonly="readonly"{/if} onblur="javascript: if (!this.oldTxt) this.oldTxt = ''; var changed = this.oldTxt not = this.value; this.oldTxt = this.value; if (changed &amp;&amp; document.getElementById('ans_{$aid}')) document.getElementById('ans_{$aid}').checked = true;">{$a.comment}</textarea>
</td>

