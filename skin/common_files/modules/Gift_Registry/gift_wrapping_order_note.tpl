{*
$Id: gift_wrapping_order_note.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<tr>
  <td colspan="2"><strong>{$lng.lbl_giftreg_wrap_order_note}</strong></td>
</tr>
{if $order.giftwrap_message ne ""}
<tr>
  <td colspan="2">{$lng.lbl_giftreg_greeting_message}:
<div class="greeting-message">{$order.giftwrap_message|nl2br}</div>
  </td>
</tr>
{else}
<tr>
  <td colspan="2" height="10">&nbsp;</td>
</tr>
{/if}
