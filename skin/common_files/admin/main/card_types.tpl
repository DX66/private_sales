{*
$Id: card_types.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_edit_cc_types}

{$lng.txt_edit_cc_types_top_text|substitute:"path":$catalogs.admin}

<br /><br />

{capture name=dialog}

<form method="post" action="card_types.php" name="ccardsform">
<input type="hidden" name="mode" value="update" />

{if $card_types}
{include file="main/check_all_row.tpl" style="line-height: 170%;" form="ccardsform" prefix="posted_data.+to_delete"}
{/if}

<table cellpadding="3" cellspacing="1" width="90%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="20%">{$lng.lbl_card_code}</td>
  <td width="50%">{$lng.lbl_card_type}</td>
  <td width="10%" align="center">{$lng.lbl_cc_cvv2}*</td>
  <td width="10%" align="center">{$lng.lbl_active}</td>
</tr>

{if $card_types}
{foreach from=$card_types item="card" key="id"}

<tr{cycle values=", class='TableSubHead'"}>
  <td>
    <input type="checkbox" name="posted_data[{$id}][to_delete]" />
    <input type="hidden" name="posted_data[{$id}][code]" value="{$card.code|escape}" />
    <input type="hidden" name="posted_data[{$id}][old_name]" value="{$card.type|escape}" />
  </td>
  <td> {$card.code} </td>
  <td><input type="text" size="50" name="posted_data[{$id}][new_name]" value="{$card.type|escape}" /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$id}][new_cvv2]"{if $card.cvv2} checked="checked"{/if} /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$id}][new_active]"{if $card.active} checked="checked"{/if} /></td>
</tr>

{/foreach}

<tr>
  <td colspan="4" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, /posted_data.+to_delete/)) submitForm(document.ccardsform, 'delete');" />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

{else}

<tr>
<td colspan="4" align="center">{$lng.txt_no_cc_types}</td>
</tr>

{/if}

<tr>
<td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_add_cc_type}</td>
</tr>

<tr class="TableHead">
<td>&nbsp;</td>
<td>{$lng.lbl_card_code}</td>
<td>{$lng.lbl_card_type}</td>
<td align="center">{$lng.lbl_cc_cvv2}*</td>
<td align="center">{$lng.lbl_active}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><input type="text" size="10" name="code" /></td>
  <td><input type="text" size="50" name="new_name" /></td>
  <td align="center"><input type="checkbox" name="new_cvv2" /></td>
  <td align="center"><input type="checkbox" name="new_active" /></td>
</tr>

<tr>
  <td colspan="4" class="SubmitBox">
<input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'add');"/>
  </td>
</tr>

</table>

</form>

<br />
{$lng.txt_edit_cc_types_note}

<br /><br />

{/capture} 
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_edit_cc_types extra='width="100%"'}
