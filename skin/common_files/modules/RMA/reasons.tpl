{*
$Id: reasons.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{include file="main/language_selector.tpl" script="returns.php?mode=reasons&"}

<form action="returns.php" method="post" name="reasons_form">

<input type="hidden" id="mode" name="mode" value="reasons" />
<input type="hidden" id="user_action" name="user_action" value="update" />

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="reasons_form" prefix="to_delete"}

<table>
{foreach from=$reasons item=v key=k}
<tr{cycle values=", class='TableSubHead'"}>
  <td width="1"><input type="checkbox" name="to_delete[{$k}]" value="{$k}" /></td>
  <td><input type="text" name="posted_data[{$k}]" value="{$v|escape}" size="32" /></td>
</tr>
{/foreach}
</tr>
<tr> 
  <td class="TopLabel" colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_new_reason}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="text" name="new" value="" size="32" /></td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td> 
</tr>
<tr>
  <td colspan="2">
    <input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />&nbsp;&nbsp;
    <input type="button" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'gi'))) {ldelim}this.form.user_action.value = 'delete'; this.form.submit();{rdelim}" value="{$lng.lbl_delete_selected}" />
  </td>
</tr>
</table>

</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_add_modify_reasons extra='width="100%"'}
