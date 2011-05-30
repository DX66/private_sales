{*
$Id: confirmation.tpl,v 1.2 2010/07/30 12:40:25 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $confirm_deletion eq "Y"}
{include file="modules/Advanced_Order_Management/confirm_deletion.tpl"}
{else}
<form action="order.php" method="post" name="confirmation_form">

<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="orderid" value="{$orderid}" />
<input type="hidden" name="confirmed" value="Y" />

<table cellpadding="3" cellspacing="1" width="100%">
<tr>
  <td colspan="2">
  {$lng.txt_aom_confirm_update_order}
  <br /><br />
  </td>
</tr>
<tr>
  <td width="1"><input type="checkbox" id="notify_customer" name="notify_customer" value="Y" /></td>
  <td width="100%"><label for="notify_customer">{$lng.lbl_aom_notify_customer}</label></td>
</tr>
{if not $single_mode}
<tr>
  <td width="1"><input type="checkbox" id="notify_provider" name="notify_provider" value="Y" checked="checked" /></td>
  <td width="100%"><label for="notify_provider">{$lng.lbl_aom_notify_provider}</label></td>
</tr>
{/if}
<tr>
  <td width="1"><input type="checkbox" id="notify_orders_dept" name="notify_orders_dept" value="Y" checked="checked" /></td>
  <td width="100%"><label for="notify_orders_dept">{$lng.lbl_aom_notify_orders_dept}</label></td>
</tr>
<tr>
  <td colspan="2">{include file="modules/Advanced_Order_Management/comment_form.tpl"}</td>
</tr>
<tr>
  <td colspan="2" class="SubmitBox">
<table cellspacing="0" cellpadding="0">
<tr>
  <td class="ButtonsRow">{include file="buttons/no.tpl" href="order.php?orderid=`$orderid`&mode=edit&show=preview" substyle="cancel"}</td>
  <td class="ButtonsRow">{include file="buttons/yes.tpl" href="javascript:document.confirmation_form.submit();" js_to_href="Y" substyle="confirm"}</td>
</tr>
</table>
  </td>
</tr>
</table>

</form>
{/if}
