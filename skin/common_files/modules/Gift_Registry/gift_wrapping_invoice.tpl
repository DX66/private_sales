{*
$Id: gift_wrapping_invoice.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $show eq "message" and $order.giftwrap_message ne ""}
  <tr>
    <td colspan="3" {if $is_nomail eq 'Y'}class="invoice-giftwrap-notes"{else}style="padding-top: 30px;"{/if}>
      <p{if $is_nomail ne 'Y'} style="font-size: 14px; font-weight: bold; text-align: center;"{/if}>{$lng.lbl_giftreg_greeting_message}</p>
      <div{if $is_nomail ne 'Y'} style="border: 1px solid #cecfce; padding: 5px;"{/if}>{$order.giftwrap_message|nl2br}</div>
    </td>
  </tr>
{elseif $show eq "totals"}
  <tr>
    <td {if $is_nomail eq 'Y'}class="invoice-total-name"{else}style="padding-right: 3px; height: 20px; width: 100%; text-align: right;"{/if}><strong>{$lng.lbl_giftreg_gift_wrapping}{if $order.giftwrap_message ne ""} <span>{$lng.lbl_giftreg_incl_greeting}</span>{/if}:</strong></td>
    <td {if $is_nomail eq 'Y'}class="invoice-total-value"{else}style="white-space: nowrap; padding-right: 5px; height: 20px; text-align: right;"{/if}>{currency value=$order.giftwrap_cost}</td>
  </tr>
{/if}
