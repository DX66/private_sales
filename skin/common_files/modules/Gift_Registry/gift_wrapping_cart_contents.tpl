{*
$Id: gift_wrapping_cart_contents.tpl,v 1.3.2.3 2010/12/15 11:57:05 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<tr>
	<td class="total-name">
    <div>
      {$lng.lbl_giftreg_gift_wrapping}
      {if $cart.giftwrap_message ne ""}
        {capture name=gr assign=gr_text}
          <strong>{$lng.lbl_giftreg_greeting_message}:</strong>
          <br /><br />
          {$cart.giftwrap_message|nl2br}
        {/capture}
        {include file="main/tooltip_js.tpl" class="need-help-link" title=$lng.lbl_giftreg_incl_greeting text=$gr_text}
      {else}
      :
      {/if}
    </div>
  </td>
	<td class="total-value">{currency value=$cart.giftwrap_cost}</td>
  {if $need_alt_currency}
	  <td class="total-alt-value">{alter_currency value=$cart.giftwrap_cost}</td>
  {/if}
</tr>

