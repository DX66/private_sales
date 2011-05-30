{*
$Id: add_returns.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $return_products ne '' and not $rma_disable_form}

{capture name=dialog}

<form action="order.php" method="post" name="addreturnform">
  <input type="hidden" name="orderid" value="{$order.orderid}" />
  <input type="hidden" name="mode" value="add_returns" />

  <table cellspacing="1" class="data-table" summary="{$lng.lbl_create_return_request|escape}">

    <tr class="head-row">
      <th class="data-checkbox-column">&nbsp;</th>
      <th>{$lng.lbl_product}</th>
      <th>{$lng.lbl_quantity}</th>
    </tr>

{foreach from=$return_products item=v name=returns}

    <tr{interline name=returns}>
      <td>
        <input type="checkbox" name="returns[{$v.itemid}][avail]" value="Y" />
      </td>
      <td>
        {$v.product}
{if $v.product_options ne ''}
        <div class="rma-product-options-box">
          {include file="modules/Product_Options/display_options.tpl" options=$v.product_options}
        </div>
{/if}
      </td>
      <td>

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

{if $reasons ne ''}
  <label class="input-block plain-box">
    <span class="label-title">{$lng.lbl_reason_for_returning}:</span>
    <select name="return_reason">
{foreach from=$reasons item=v key=k}
      <option value='{$k}'>{$v}</option>
{/foreach}
    </select>
  </label>
{/if}

{if $actions ne ''}
  <label class="input-block plain-box">
    <span class="label-title">{$lng.lbl_what_you_would_like_us_to_do}:</span>
    <select name="return_action">
{foreach from=$actions item=v key=k}
      <option value='{$k}'>{$v}</option>
{/foreach}
    </select>
  </label>
{/if}

  <label class="input-block plain-box">
    <span class="label-title">{$lng.lbl_comment}:</span>
    <textarea rows="3" cols="60" name="return_comment"></textarea>
  </label>

  <div class="button-row">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_create type="input"}
  </div>

</form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_create_return_request content=$smarty.capture.dialog}

{/if}
