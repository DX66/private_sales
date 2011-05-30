{*
$Id: product_event_cart.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.event_data ne ""}
{assign var=creator value="`$product.event_data.creator_title` `$product.event_data.firstname` `$product.event_data.lastname`"}
<div class="event-info">
<input type="hidden" id="event_mark_{$product.cartid}" name="event_mark[{$product.cartid}]" value="Y" />
<table class="valign-middle">
<tr>
	<td><input type="checkbox" id="em_{$product.cartid}" name="em[{$product.cartid}]" checked="checked" onclick="javascript: _getById('event_mark_{$product.cartid}').value= (this.checked) ? 'Y' : 'N'" /></td>
  <td><label for="em_{$product.cartid}">{$lng.lbl_giftreg_buy_as_present|substitute:"event_name":$product.event_data.title:"eventid":$product.event_data.event_id:"creator":$creator}</label></td>
</tr>
</table>
</div>
{/if}
