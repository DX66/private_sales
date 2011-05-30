{*
$Id: order_delete_confirmation.tpl,v 1.2.2.2 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_delete_orders}

{capture name=dialog}

{if $mode ne "delete_all"}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_return_to_search_results href="orders.php?mode=search"}</div>
{else}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_go_back href="orders.php"}</div>
{/if}
<br />

<form action="process_order.php" method="post" name="processform">

<input type="hidden" name="mode" value="" />
<input type="hidden" name="confirmed" value="Y" />

<span class="Text">{$lng.lbl_order_delete_confirmation_header}</span>

<br /><br />

{if $mode eq "delete_all"}

<dl>
<dd>{$lng.txt_delete_N_orders_message|substitute:"count":$orders_count}</dd>
</dl>

{else}

<ul>
{section name=oid loop=$orders}
<li><span class="ProductPriceSmall">{$lng.lbl_order} #{$orders[oid].orderid} - {currency value=$orders[oid].total}</span>
<dl>
<dd>{$lng.lbl_date}: {$orders[oid].date|date_format:$config.Appearance.date_format}</dd>
<dd>{$lng.lbl_status}: {include file="main/order_status.tpl" status=$orders[oid].status mode="static"}</dd>
{if $orders[oid].provider_login}
<dd>{$lng.lbl_provider}: {$orders[oid].provider_login}</dd>
{/if}
</dl>
</li>
{/section}
</ul>

<br />

{/if}

{$lng.txt_operation_not_reverted_warning}

<br /><br />
{if $mode ne "delete_all"}
{assign var="button_href" value="delete"}
{assign var="button_href_no" value="?mode=search"}
{else}
{assign var="button_href" value="delete_all"}
{/if}
<table cellspacing="0" cellpadding="0">
<tr>
  <td>{$lng.txt_are_you_sure_to_proceed}</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/yes.tpl" href="javascript:document.processform.mode.value='`$button_href`';document.processform.submit()" js_to_href="Y"}</td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/no.tpl" href="orders.php`$button_href_no`"}</td>
</tr>
</table>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
