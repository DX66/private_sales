{*
$Id: preview.tpl,v 1.1 2010/05/21 08:32:19 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $message}
{capture name=dialog}
{if $message eq "saved"}
{$lng.lbl_aom_order_updated}
{elseif $message eq "cancel"}
{$lng.lbl_aom_changes_canceled}
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_aom_confirmation content=$smarty.capture.dialog extra='width="100%"'}
<br />
{/if}
{capture name=dialog}
<table width="100%">
<tr> 
<td valign="top"> 
<br />
{include file="main/order_info.tpl"}
</td>
</tr>
<tr>
<td height="1" valign="top">
<hr noshade="noshade" size="1" />
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="40%">{$lng.lbl_status}:</td>
<td width="60%"><b>{include file="main/order_status.tpl" status=$order.status mode="static"}</b></td>
</tr>
<tr>
<td>{$lng.lbl_tracking_number}:</td>
<td>{$order.tracking}</td>
</tr>
</table>
<br />
{$lng.lbl_customer_notes}:<br />
{if $order.customer_notes ne ""}<div class="OrderNotes">{$order.customer_notes|nl2br}</div>{else}<i>{$lng.lbl_none}</i>{/if}
<br />
{$lng.lbl_order_details}:<br />
{if $order.details ne ""}<div class="OrderNotes">{$order.details|nl2br}</div>{else}<i>{$lng.lbl_none}</i>{/if}
<br />
{$lng.lbl_order_notes}:<br />
{if $order.notes ne ""}<div class="OrderNotes">{$order.notes|nl2br}</div>{else}<i>{$lng.lbl_none}</i>{/if}
</td>
</tr>
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_aom_preview_title|substitute:"orderid":$order.orderid content=$smarty.capture.dialog extra='width="100%"'}
