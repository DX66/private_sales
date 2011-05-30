{*
$Id: discounts.tpl,v 1.3 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_discounts}

{$lng.txt_discounts_note}

{capture name=dialog}

{if $discounts}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'discountsform';
checkboxes = new Array({foreach from=$discounts item=v key=k}{if $k gt 0},{/if}'posted_data[{$v.discountid}][to_delete]'{/foreach});
 
//]]> 
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="discounts.php" method="post" name="discountsform">
<input type="hidden" name="mode" value="update" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="25%">{$lng.lbl_order_subtotal}</td>
  <td width="25%">{$lng.lbl_discount}</td>
  <td width="25%">{$lng.lbl_discount_type}</td>
  <td width="25%">{$lng.lbl_membership}</td>
</tr>

{if $discounts}

{foreach from=$discounts item=discount}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="posted_data[{$discount.discountid}][to_delete]" /></td>
  <td><input type="text" name="posted_data[{$discount.discountid}][minprice]" size="12" value="{$discount.minprice|formatprice}" /></td>
  <td><input type="text" name="posted_data[{$discount.discountid}][discount]" size="12" value="{$discount.discount|formatprice}" /></td>
  <td>
  <select name="posted_data[{$discount.discountid}][discount_type]">
    <option value="percent"{if $discount.discount_type eq "percent"} selected="selected"{/if}>{$lng.lbl_percent}, %</option>
    <option value="absolute"{if $discount.discount_type eq "absolute"} selected="selected"{/if}>{$lng.lbl_absolute}, {$config.General.currency_symbol}</option>
  </select>
  </td>
  <td>{include file="main/membership_selector.tpl" field="posted_data[`$discount.discountid`][membershipids][]" data=$discount is_short='Y'}</td>
</tr>

{/foreach}

<tr>
  <td colspan="5" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('posted_data\\[\\w+\\]\\[to_delete\\]', 'gi'))) submitForm(this, 'delete');" />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

{else}

<tr>
  <td colspan="5" align="center">{$lng.lbl_no_discounts_defined}</td>
</tr>

{/if}

</table>
</form>

<form action="discounts.php" method="post">
<input type="hidden" name="mode" value="add" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td colspan="5">{include file="main/subheader.tpl" title=$lng.lbl_add_new_discount}</td>
</tr>

<tr>
  <td width="10">&nbsp;</td>
  <td width="25%"><input type="text" name="minprice_new" size="12" value="{$zero}" /></td>
  <td width="25%"><input type="text" name="discount_new" size="12" value="{$zero}" /></td>
  <td width="25%">
  <select name="discount_type_new">
    <option value="percent">{$lng.lbl_percent}, %</option>
    <option value="absolute">{$lng.lbl_absolute}, {$config.General.currency_symbol}</option>
  </select>
  </td>
  <td width="25%">{include file="main/membership_selector.tpl" field="discount_membershipids_new[]" data="" is_short='Y'}</td>
</tr>

<tr>
  <td colspan="5" class="SubmitBox"><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_edit_purchase_discounts content=$smarty.capture.dialog extra='width="100%"'}
