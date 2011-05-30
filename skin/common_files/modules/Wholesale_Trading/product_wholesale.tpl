{*
$Id: product_wholesale.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Wholesale_Trading ne ""}

{$lng.txt_wholesales_top_text}

<br /><br />

{capture name=dialog}

<form action="product_modify.php" method="post" name="pricing_form">
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="mode" value="wholesales_modify" />
<input type="hidden" name="geid" value="{$geid}" />

<div id="wholesale_admin_note_small">
  <strong>{$lng.lbl_note}:</strong>
  {$lng.lbl_wholesale_admin_note_small}
  <a href="javascript:void(0);" onclick="javascript: document.getElementById('wholesale_admin_note_small').style.display = 'none'; document.getElementById('wholesale_admin_note').style.display = '';">{$lng.lbl_more}</a>
</div>
<div id="wholesale_admin_note" style="display: none;">
  <strong>{$lng.lbl_note}:</strong> {$lng.lbl_wholesale_admin_note}
</div>
<br />
<table {if $geid ne ''}cellspacing="0" cellpadding="4"{else}cellspacing="1" cellpadding="2"{/if} width="100%">

<tr class="TableHead">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="15" class="DataTable"><img src="{$ImagesDir}/spacer.gif" width="15" height="1" border="0" alt="" /></td>
  <td width="25%" class="DataTable">{$lng.lbl_quantity}</td>
  <td width="25%" class="DataTable">{$lng.lbl_price_per_item} ({$config.General.currency_symbol})</td>
  <td width="50%">{$lng.lbl_membership}</td>
</tr>

<tr style="height: 20px">
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td class="DataTable">&nbsp;</td>
  <td class="DataTable"><b>1</b></td>
  <td class="DataTable"><b>{$product.price|formatprice}</b></td>
  <td colspan="2"><b>{$lng.lbl_all}</b></td>
</tr>

{foreach from=$pricing item=v}
{if $v.membershipid gt 0 or $v.quantity gt 1}
<tr{cycle values=' class="TableSubHead",'}>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[w_price][{$v.priceid}]" /></td>{/if}
  <td width="15" class="DataTable"><input type="checkbox" value="Y" name="wpids[{$v.priceid}]" /></td>
  <td class="DataTable">{$v.quantity}</td>
  <td class="DataTable"><input type="text" maxlength="16" size="16" name="wprices[{$v.priceid}][price]" value="{$v.price|formatprice}" style="width: 100%" /></td>
  <td><select name="wprices[{$v.priceid}][membershipid]" style="width: 100%">
{if $v.quantity gt 1}<option value="">{$lng.lbl_all}</option>{/if}
{foreach from=$memberships item=m}
<option value="{$m.membershipid}"{if $v.membershipid eq $m.membershipid} selected="selected"{/if}>{$m.membership}</option>
{/foreach}
  </select></td>
</tr>
{/if}
{/foreach}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="4">&nbsp;</td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="4">{include file="main/subheader.tpl" title=$lng.lbl_add_new_price}</td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[new_w_price]" /></td>{/if}
  <td>&nbsp;</td>
  <td align="center"><input type="text" size="8" name="newquantity" style="width: 100%" /></td>
  <td><input type="text" size="16" name="newprice" value="{$zero}" style="width: 100%" /></td>
  <td width="40%"><select name="membershipid" style="width: 100%">
<option value="">{$lng.lbl_all}</option>
{foreach from=$memberships item=m}
<option value="{$m.membershipid}">{$m.membership}</option>
{/foreach}
  </select></td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="4"><br />
{if $pricing ne ''}
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if(checkMarks(this.form, new RegExp('wpids', 'gi'))) {ldelim}document.pricing_form.mode.value='wholesales_delete'; document.pricing_form.submit();{rdelim}" /> &nbsp;&nbsp;&nbsp;
{/if}
<input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_wholesale_prices content=$smarty.capture.dialog extra='width="100%"'}
{/if}
