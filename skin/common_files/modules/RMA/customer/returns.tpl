{*
$Id: returns.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_returns}</h1>

{$lng.txt_N_results_found|substitute:"items":$returns_count}

{if $returns ne ''}

{capture name=dialog}

<div class="right-box">
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_search_again href="returns.php" style="link"}
</div>

{include file="customer/check_all_row.tpl" form="returnsform" prefix="to_delete"}

<form action="returns.php" method="post" name="returnsform">
  <input type="hidden" name="mode" value="" />

  <table cellspacing="1" cellpadding="3" class="data-table" width="100%">

    <tr class="head-row center-row">
      <th class="data-checkbox-column">&nbsp;</th>
      <th>{$lng.lbl_returnid}</th>
      <th>{$lng.lbl_product}</th>
      <th>{$lng.lbl_order}</th>
      <th>{$lng.lbl_date}</th>
      <th>{$lng.lbl_status}</th>
    </tr>

{foreach from=$returns item=v name=returns}
    <tr{interline name=returns}>
      <td><input type="checkbox" name="to_delete[{$v.returnid}]" value="Y" /></td>
      <td><a href="returns.php?mode=modify&amp;returnid={$v.returnid}">RMA#{$v.returnid}</a></td>
      <td>
{strip}
{if $v.productid gt 0}
        <a href="product.php?productid={$v.productid}">{$v.product} ({$v.amount} {$lng.lbl_items})</a>
{else}
        {$v.product} ({$v.amount} {$lng.lbl_items})
{/if}
{/strip}
{if $v.product_options ne "" and $active_modules.Product_Options ne ''}
        <div class=".rma-product-options-box">
          {include file="modules/Product_Options/display_options.tpl" options_txt=$v.product_options force_product_options_txt=true}
        </div>
{/if}
      </td>
      <td class="data-right-column">
        <a href="order.php?orderid={$v.orderid}">{$v.orderid}</a>
      </td>
      <td>{$v.date|date_format:$config.Appearance.datetime_format}</td>
      <td>{include file="modules/RMA/return_status.tpl" mode="static" status=$v.status}</td>
    </tr>
{/foreach}
  </table>

{if $returns ne ''}
  <div class="button-row">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_delete_selected href="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'gi'))) submitForm(this, 'delete');"}
  </div>

  <div class="text-block text-pre-block">{$lng.txt_operation_for_first_selected_only}</div>

  <div class="button-row">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_modify href="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'gi'))) submitForm(this, 'modify');"}
  </div>
{/if}

</form>

{/capture}
{include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_returns noborder=true}

{/if}
