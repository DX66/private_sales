{*
$Id: gcheckout_order.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{if $gcheckout_enabled ne ""}

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_order_processing}

{dec value=$order.gcheckout_data.total dec=$order.gcheckout_data.refunded_amount assign="order_total_cost"}

<b>{$lng.lbl_gcheckout_order}: #{$order.gcheckout_data.goid}</b><br />
{$lng.lbl_total_amount}: {currency value=$order_total_cost}
{if $order.gcheckout_data.linked_orders}
<br />
{$lng.lbl_gcheckout_linked_orders}:
{section name=oids loop=$order.gcheckout_data.linked_orders}
{strip}
{if $order.gcheckout_data.linked_orders[oids].orderid eq $order.orderid}
<b>#{$order.gcheckout_data.linked_orders[oids].orderid} ({currency value=$order.gcheckout_data.linked_orders[oids].total})</b>
{else}
<a href="order.php?orderid={$order.gcheckout_data.linked_orders[oids].orderid}">#{$order.gcheckout_data.linked_orders[oids].orderid} ({currency value=$order.gcheckout_data.linked_orders[oids].total})</a>
{/if}
{if not $smarty.section.oids.last}, {/if}
{/strip}
{/section}
{/if}
<br />
{if $order.gcheckout_data.refunded_amount ne 0}
{$lng.lbl_refunded_amount}: {currency value=$order.gcheckout_data.refunded_amount}
<br />
({$lng.lbl_initial_total_cost}: {currency value=$order.gcheckout_data.total})
<br />
{/if}
{$lng.lbl_gcheckout_current_order_state}:
<br />
&nbsp;&nbsp;&nbsp;&nbsp;{$lng.lbl_gcheckout_fulfillment_state}: {$order.gcheckout_data.fulfillment_state} ({$order.gcheckout_data.fulfillment_state_date})
<br />
&nbsp;&nbsp;&nbsp;&nbsp;{$lng.lbl_gcheckout_financial_state}: {$order.gcheckout_data.financial_state} ({$order.gcheckout_data.financial_state_date})

<br />
<br />

Order state log:<br />
<textarea cols="70" rows="12" style="color: #666666; background-color:#EEEEEE; width: 520px;" readonly="readonly">{$order.gcheckout_data.state_log|escape:quotes}</textarea>

<br />
<br />

{$lng.txt_gcheckout_status_update_note}

<br />
<br />
<br />

<form action="order.php" method="post" name="gcheckoutform">

<input type="hidden" name="mode" value="gcheckout" />
<input type="hidden" name="gcmode" value="" />
<input type="hidden" name="orderid" value="{$order.orderid}" />

{if $gcheckout_admin eq 'Y'}

{if $order.gcheckout_data.financial_state eq "REVIEWING" or $order.gcheckout_data.financial_state eq "CHARGEABLE"}

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_charge_order class='black'}

{$lng.txt_gcheckout_charge_order_note}

<br />
<br />

{capture name=currency}
{currency value=$order.gcheckout_data.total plain_text_message='Y'}
{/capture}

<input type="button" value="{$lng.lbl_gcheckout_charge_order|escape}: {$smarty.capture.currency}" onclick="javascript: document.gcheckoutform.gcmode.value='charge'; document.gcheckoutform.submit();" style="FONT-SIZE: 11px; FONT-WEIGHT: bold;" />
<br />
<br />
<br />
{/if}

{if $order.gcheckout_data.financial_state eq "CHARGED (CONFIRMED)" or ($order.gcheckout_data.refunded_amount gt 0 and $order.gcheckout_data.refunded_amount ne $order.gcheckout_data.total)}

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_refund_order class='black'}

{$lng.txt_gcheckout_refund_order_note}

<br />
<br />

<table cellpadding="3" cellspacing="1">
<tr>
  <td>{$lng.lbl_gcheckout_refund_amount} ({$config.General.currency_symbol}):</td>
  <td><input type="text" name="refund_amount" size="10" maxlength="15" value="{$order_total_cost|escape}" /></td>
</tr>
<tr>
  <td>{$lng.lbl_gcheckout_reason}:</td>
  <td><input type="text" name="refund_reason" value="" size="80" maxlength="140" /></td>
</tr>
<tr>
  <td>{$lng.lbl_gcheckout_comment}:</td>
  <td><input type="text" name="refund_comment" value="" size="80" maxlength="140" /></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="button" value="{$lng.lbl_gcheckout_refund_order|escape}" onclick="javascript: document.gcheckoutform.gcmode.value='refund'; document.gcheckoutform.submit();" /></td>
</tr>

</table>

<br />
<br />
<br />
{/if}

{if $order.gcheckout_data.financial_state eq "PAYMENT_DECLINED" or $order.gcheckout_data.financial_state eq "CHARGEABLE" or $order_refunded}

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_cancel_order class='black'}

{$lng.txt_gcheckout_cancel_order_note}

<br />
<br />

<table cellpadding="3" cellspacing="1">

<tr>
  <td>{$lng.lbl_gcheckout_reason}:</td>
  <td>
  <select name="cancel_reason_sel" onchange="javascript: elm1 = document.getElementById('creason'); if (this.selectedIndex != 9) elm1.style.display='none'; else {ldelim} elm1.style.display=''; if (elm1.focus) elm1.focus(); {rdelim}">
  {foreach key=key item=_reason from=$gcheckout_cancel_reason_list}
    <option value="{$key}">{$_reason}</option>
  {/foreach}
  </select>
  <input type="text" id="creason" name="cancel_reason" value="" size="50" maxlength="140" style="display: none" /></td>
</tr>
<tr>
  <td>{$lng.lbl_gcheckout_comment}:</td>
  <td><input type="text" name="cancel_comment" value="" size="80" maxlength="140" /></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="button" value="{$lng.lbl_gcheckout_cancel_order|escape}" onclick="javascript: document.gcheckoutform.gcmode.value='cancel'; document.gcheckoutform.submit();" /></td>
</tr>

</table>

<br />
<br />

{/if}

{/if}

{if $order.gcheckout_data.financial_state ne "CANCELLED" and $order.gcheckout_data.financial_state ne "CANCELLED_BY_GOOGLE"}

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_fulfillment_commands class='black'}

{if $order.gcheckout_data.fulfillment_state eq "NEW"}

{$lng.txt_gcheckout_process_order_note}

<br />
<br />

<input type="button" value="{$lng.lbl_gcheckout_process_order|escape}" onclick="javascript: document.gcheckoutform.gcmode.value='process'; document.gcheckoutform.submit();" />
<br />
<br />
<br />
{/if}

{$lng.txt_gcheckout_deliver_order_note}

<br />
<br />

<table cellpadding="3" cellspacing="1">

<tr>
  <td colspan="2">
  <b>{$lng.lbl_carrier}:</b> {$order.shipping_carrier}
{if $order.shipping_carrier eq "Other"}
  {$lng.txt_gcheckout_valid_carriers}
{/if}
  <br />
  <b>{$lng.lbl_tracking_number}:</b> {$order.tracking|default:$lng.txt_not_available}
  </td>
</tr>

</table>

<input type="button" value="{$lng.lbl_gcheckout_add_tracking_data|escape}"{if $order.tracking eq ""} disabled="disabled"{/if} onclick="javascript: document.gcheckoutform.gcmode.value='add_tracking'; document.gcheckoutform.submit();" />

<br />
<br />

{if $order.gcheckout_data.fulfillment_state ne "DELIVERED"}

<table cellpadding="3" cellspacing="1">

<tr>
  <td><input type="checkbox" name="deliver_send_email" /></td>
  <td width="100%">{$lng.lbl_gcheckout_send_email}</td>
</tr>

</table>

{/if}

<input type="button" value="{$lng.lbl_gcheckout_deliver_order|escape}"{if $order.gcheckout_data.fulfillment_state eq "DELIVERED"} disabled="disabled"{/if} onclick="javascript: document.gcheckoutform.gcmode.value='deliver'; document.gcheckoutform.submit();" />
<br />
{if $order.gcheckout_data.fulfillment_state eq "DELIVERED"}
{$lng.txt_gcheckout_order_delivered}
{/if}

<br />
<br />
<br />

{/if}

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_send_message class='black'}

{$lng.txt_gcheckout_send_email_note}

<table cellpadding="3" cellspacing="1">

<tr>
  <td colspan="2">
  {$lng.lbl_message}:<br />
  <textarea name="message" cols="70" rows="4"></textarea>
  </td>
</tr>

<tr>
  <td><input type="checkbox" name="send_email_message" /></td>
  <td width="100%">{$lng.lbl_gcheckout_send_email}</td>
</tr>

</table>

<input type="button" value="{$lng.lbl_gcheckout_send_message|escape}" onclick="javascript: document.gcheckoutform.gcmode.value='send_message'; document.gcheckoutform.submit();" />

{if $gcheckout_admin eq 'Y'}

<br />
<br />
<br />

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_archiving_commands class='black'}

{$lng.txt_gcheckout_archiving_commands_note}

<br />
<br />

{if $order.gcheckout_data.archived eq 'N'}
<input type="button" value="{$lng.lbl_gcheckout_archive_order|escape}" onclick="javascript: document.gcheckoutform.gcmode.value='archive'; document.gcheckoutform.submit();" />
{else}
<input type="button" value="{$lng.lbl_gcheckout_unarchive_order|escape}" onclick="javascript: document.gcheckoutform.gcmode.value='unarchive'; document.gcheckoutform.submit();" />
{/if}

{/if}

<br />
<br />

</form>

{else}

<br />

{$lng.txt_gcheckout_disabled}

<br />

{/if}

{/capture}

{include file="dialog.tpl" title=$lng.lbl_gcheckout_order_processing content=$smarty.capture.dialog extra='width="100%"'}
