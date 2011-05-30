{*
$Id: admin_reviews.tpl,v 1.2.2.2 2011/04/28 13:10:36 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Customer_Reviews ne ""}

{$lng.txt_adm_reviews_top_text}

<br /><br />

{capture name=dialog}

{if $product_reviews}
  {include file="main/check_all_row.tpl" form="modifyreviews" prefix="rids"}
{/if}
<br />

<form action="product_modify.php" method="post" name="modifyreviews">
<input type="hidden" name="mode" value="update_reviews" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellspacing="0" cellpadding="3" width="100%">
<tr class="TableHead">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="15" class="DataTable">&nbsp;</td>
  <td width="30%" class="DataTable">{$lng.lbl_author}</td>
  <td width="70%">{$lng.lbl_message}</td>
</tr>

{if $product_reviews}

{foreach from=$product_reviews item=r}
<tr valign="top"{cycle values=', class="TableSubHead"'}>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[review][{$r.review_id}]" /></td>{/if}
  <td width="15" class="DataTable"><input type="checkbox" value="Y" name="rids[{$r.review_id}]" /></td>
  <td class="DataTable"><input type="text" size="32" name="reviews[{$r.review_id}][email]" value="{$r.email|default:$lng.lbl_unknown|amp}" style="width:100%" /></td>
  <td width="65%"><textarea cols="40" rows="6" name="reviews[{$r.review_id}][message]" style="width: 100%">{$r.message|amp}</textarea></td>
</tr>
{/foreach}

{else}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3" align="center">{$lng.txt_no_reviews}</td>
</tr>

{/if}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3">{include file="main/subheader.tpl" title=$lng.lbl_add_new_review}</td>
</tr>

<tr valign="top">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[new_review]" /></td>{/if}
  <td>&nbsp;</td>
  <td><input type="text" size="32" name="review_new[email]" value="" /></td>
  <td colspan="2"><textarea cols="40" rows="6" name="review_new[message]" style="width: 100%"></textarea></td>
</tr>

<tr valign="top">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3"><input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />
{if $product_reviews}
  &nbsp;&nbsp;&nbsp;
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('rids', 'ig'))) {ldelim} document.modifyreviews.mode.value='review_delete'; document.modifyreviews.submit();{rdelim}" />
{/if}
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_edit_reviews extra='width="100%"'}
{/if}
