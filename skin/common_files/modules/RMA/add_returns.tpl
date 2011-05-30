{*
$Id: add_returns.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $return_products ne '' and not $rma_disable_form}
{capture name=dialog}
<form action="order.php" method="post" name="addreturnform">
<input type="hidden" name="orderid" value="{$order.orderid}" />
<input type="hidden" name="mode" value="add_returns" />
<table>
<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td>{$lng.lbl_product}</td>
  <td>{$lng.lbl_quantity}</td>
</tr>
{foreach from=$return_products item=v}
<tr{cycle values=', class="TableSubHead"'}>
  <td valign="top"><input type="checkbox" name="returns[{$v.itemid}][avail]" value="Y" /></td>
  <td valign="middle">{$v.product}
    {if $v.product_options ne ''}<div style="padding-left: 10px;">{include file="modules/Product_Options/display_options.tpl" options=$v.product_options}</div>{/if}
  </td>
  <td valign="top">
  <select name="returns[{$v.itemid}][amount]">
  {section name=i loop=$v.amount}
  {inc assign="cnt" value=%i.index%}
    <option value='{$cnt}'>{$cnt}</option>
  {/section}
  </select>
  </td>
</tr>
{/foreach}
</table>

<br />

{if $reasons ne ''}
{$lng.lbl_reason_for_returning}:<br />
<select name="return_reason">
{foreach from=$reasons item=v key=k}
<option value='{$k}'>{$v}</option>
{/foreach}
</select><br />

<br />
{/if}

{if $actions ne ''}
{$lng.lbl_what_you_would_like_us_to_do}:<br />
<select name="return_action">
{foreach from=$actions item=v key=k}
<option value='{$k}'>{$v}</option>
{/foreach}
</select><br />

<br />
{/if}

{$lng.lbl_comment}:<br />
<textarea rows="3" cols="60" name="return_comment"></textarea>

<br /><br />

<input type="submit" value="{$lng.lbl_create|strip_tags:false|escape}" />
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_create_return_request content=$smarty.capture.dialog extra='width="100%"'}
{/if}
