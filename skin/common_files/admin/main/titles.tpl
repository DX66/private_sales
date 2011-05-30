{*
$Id: titles.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_titles_management}

<br />

{$lng.txt_titles_top_text}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="titles.php?"}
<br />

<form action="titles.php" method="post" name="titlesform">
<input type="hidden" name="mode" value="update" />

<table cellpadding="2" cellspacing="1" width="100%">
<tr class="TableHead">
  <td width="15">&nbsp;</td>
  <td width="70%">{$lng.lbl_titles}</td>
  <td width="15%">{$lng.lbl_orderby}</td>
  <td width="15%">{$lng.lbl_active}</td>
</tr>

{foreach from=$titles item=v}
<tr{cycle values=', class="TableSubHead"'}>
  <td><input type="checkbox" name="ids[]" value="{$v.titleid}" /></td>
  <td><input type="text" maxlength="64" name="data[{$v.titleid}][title]" value="{$v.title|escape}" /></td>
  <td align="center"><input type="text" size="5" maxlength="11" name="data[{$v.titleid}][orderby]" value="{$v.orderby}" /></td>
  <td align="center"><input type="checkbox" name="data[{$v.titleid}][active]" value="Y"{if $v.active eq 'Y'} checked="checked"{/if} /></td>
</tr>
{foreachelse}
<tr>
  <td colspan="4" align="center">{$lng.txt_no_titles_defined}</td>
</tr>
{/foreach}
{if $titles ne ''}
<tr>
  <td>&nbsp;</td>
  <td colspan="3" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('ids', 'ig'))) submitForm(this, 'delete');" />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>
{/if}

<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td colspan="4">{include file="main/subheader.tpl" title=$lng.lbl_add_new_title}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="text" maxlength="64" name="add[title]" value="" /></td>
  <td align="center"><input type="text" size="5" maxlength="11" name="add[orderby]" value="" /></td>
  <td align="center"><input type="checkbox" name="add[active]" value="Y" checked="checked" /></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td colspan="3"><input type="button" value="{$lng.lbl_add|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'add');" /></td>
</tr>

</table>
</form>

<br />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_titles content=$smarty.capture.dialog extra='width="100%"'}
