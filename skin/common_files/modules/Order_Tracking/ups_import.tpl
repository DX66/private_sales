{*
$Id: ups_import.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*$Id: ups_import.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $*}
{include file="page_title.tpl" title=$lng.lbl_import_trackingid_log}

{capture name=dialog}

{if $log}

{$lng.txt_import_trackingid_note}

<br />
<br />

<table cellspacing="1" cellpadding="3" width="80%">

<tr class="TableHead">
  <td><b>OrderId</b></td>
  <td><b>TrackingId</b></td>
  <td><b>{$lng.lbl_status}</b></td>
</tr>

{foreach from=$log item=log_item}

<tr{cycle values=", class='TableSubHead'"}>
  <td align="center">{if $log_item.status}<a href="order.php?orderid={$log_item.orderid}">#{$log_item.orderid}</a>{else}#{$log_item.orderid}{/if}</td>
  <td align="center">{$log_item.trackingid}</td>
  <td align="center"><font color="{if $log_item.status}green">{$lng.lbl_success}{else}red">{$lng.lbl_failed}{/if}</font></td>
</tr>

{/foreach}

</table>

{/if}

<br /><br />

{include file="buttons/button.tpl" href="orders.php" button_title=$lng.lbl_go_back}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_import_trackingid_log content=$smarty.capture.dialog extra='width="100%"'}
