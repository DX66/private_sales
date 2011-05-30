{*
$Id: edit_products.tpl,v 1.3.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/popup_product.js"></script>

{literal}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
  $(".option-choice").bind("change", function(e) {
    var reg = new RegExp(/^(keep|overwrite)_opts_/);
    $("#box_opts_" + $(this).attr('id').replace(reg, '') ).css('display', ($(this).val() == 'N') ? '' : 'none');
  });
});
//]]>
</script>
{/literal}

{capture name=dialog}
<form action="order.php" method="post" name="editpoduct_form">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="update_products" />
<input type="hidden" name="show" value="products" />
<input type="hidden" name="orderid" value="{$orderid}" />

{include file="main/subheader.tpl" title=$lng.lbl_product_info}

<table cellpadding="3" cellspacing="1" width="100%">

{if $total_products}
{section name=prod_num loop=$total_products start=0}
{assign var="product" value=$cart_products[prod_num]}
{assign var="orig_product" value=$orig_products[prod_num]}

<tr>
  <td colspan="3" height="18" class="{if $product.deleted}ProductTitleHidden{else}ProductTitle{/if}">
    {if $current_membership_flag ne 'FS' and $product.is_deleted ne "Y"}
      <a href="product_modify.php?productid={$product.productid}" target="viewproduct{$product.productid}">
    {/if}
    {$product.productid}. {$product.product}
    {if $current_membership_flag ne 'FS'}
      </a>
    {/if}

    {if $product.pconf_parent}
      <font class="FormButton">[<font class="AdminSmallMessage">{$lng.lbl_pconf_component_of}</font> {$product.pconf_parent}]</font>
    {/if}
    {if $product.deleted}
      &nbsp;&nbsp;&nbsp;[<font class="ErrorMessage">{$lng.lbl_aom_deleted}</font>]
    {/if}
    {if $product.is_deleted}
      &nbsp;&nbsp;&nbsp;<font class="ErrorMessage">[{$lng.lbl_aom_removed}]</font>]
    {/if}

  </td>
</tr>

<tr class="TableHead">
  <td height="16" align="left" width="40%">

<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="checkbox" name="product_details[{%prod_num.index%}][delete]" value="{$product.productid}" /></td>
  <td>{if $product.deleted}{$lng.lbl_aom_restore}{else}{$lng.lbl_aom_delete}{/if}</td>
</tr>
</table>

  </td>
  <th width="30%" height="16" align="left">{$lng.lbl_aom_current_value}</th>
  <th width="30%" height="16" align="left">{$lng.lbl_aom_original_value}</th>
</tr>
<tr>
  <td valign="top">{$lng.lbl_sku}</td>
  <td valign="top">{$product.productcode|default:"-"}</td>
  <td valign="top">{if $product.new}{else}{$orig_product.productcode|default:"-"}{/if}</td>
</tr>
<tr class="TableSubHead">
  <td valign="top">{$lng.lbl_provider}</td>
  <td valign="top">{$product.provider}</td>
  <td valign="top">{$orig_product.provider}</td>
</tr>
<tr class="TableSubHead">
  <td valign="top">{$lng.lbl_price}, {$config.General.currency_symbol}</td>
  <td valign="top">
    {if $product.deleted}
      {currency value=$product.price}
    {else}
      <input type="text" name="product_details[{%prod_num.index%}][price]" size="5" maxlength="15" value="{$product.price|formatprice}" />
      <br />{$lng.lbl_aom_catalog_price}: {if $product.is_deleted ne "Y"}{currency value=$product.catalog_price}{else}{$lng.txt_not_available}{/if}
    {/if}
  </td>
  <td valign="top">{if $product.new}{else}{currency value=$orig_product.price}{/if}</td>
</tr>
<tr>
  <td valign="top">{$lng.lbl_aom_quantity_items}
    {if $active_modules.RMA and $product.returns ne ""}
      <br /><br />
      <font class="Star">{include file="modules/RMA/item_returns_txt.tpl" product=$orig_product}</font>
    {/if}

    {if $config.General.unlimited_products ne "Y" and $product.is_deleted ne "Y" and $order.status ne "F" and $order.status ne "D" and $order.status ne "I" and not ($active_modules.Egoods and $product.distribution ne '')}
      <br /><br />
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td><input type="checkbox" id="stock_upd_{%prod_num.index%}" name="product_details[{%prod_num.index%}][stock_update]" value="Y"{if $product.stock_update ne "N"} checked="checked"{/if} /></td>
          <td><label for="stock_upd_{%prod_num.index%}">{$lng.lbl_aom_update_stock_level}</label></td>
        </tr>
      </table>
    {/if}

  </td>

  <td valign="top">
    {if $product.deleted or ($active_modules.Egoods and $product.distribution ne '')}
      {$product.amount}
    {else}
      <input type="text" name="product_details[{%prod_num.index%}][amount]" size="5" maxlength="5" value="{$product.amount|escape}" />
      {if $config.General.unlimited_products ne "Y"}
        <br />
        {$lng.lbl_aom_quantity_stock_items}: {if $product.is_deleted ne "Y"}{$product.items_in_stock}{else}{$lng.txt_not_available}{/if}
      {/if}
    {/if}

  </td>
  <td valign="top">{$orig_product.amount}</td>
</tr>

{if $active_modules.Product_Options ne "" and ($orig_product.product_options ne "" or $product.display_options ne "")}
<tr>
  <td valign="top">{$lng.lbl_selected_options}<br />{$lng.lbl_aom_considered_in_price}</td>
  <td valign="top">
  {if $product.adv_option_choice eq "Y"}
    <p>{$lng.lbl_aom_options_changed_msg}</p>
    <table cellpadding="0" cellspacing="0">
    <tr>
      <td><input type="radio" class="option-choice" id="keep_opts_{%prod_num.index%}" name="product_details[{%prod_num.index%}][keep_options]" value="Y"{if $product.keep_options neq "N"} checked="checked"{/if} /></td>
      <td><label for="keep_opts_{%prod_num.index%}">{$lng.lbl_aom_keep_orig_options}</label>
    </tr>
    <tr>
      <td><input type="radio" class="option-choice" id="overwrite_opts_{%prod_num.index%}" name="product_details[{%prod_num.index%}][keep_options]" value="N"{if $product.keep_options eq "N"} checked="checked"{/if}{if $product.is_deleted eq "Y" or $product.display_options eq ""} disabled="disabled"{/if} /></td>
      <td><label for="overwrite_opts_{%prod_num.index%}">{$lng.lbl_aom_select_new_options}</label></td>
    </tr>
    </table>
    <br />

    {if $product.is_deleted eq "Y"}
      <font class="Star">{$lng.lbl_aom_product_missing}</font>
    {elseif $product.display_options eq ""}
      <font class="Star">{$lng.lbl_aom_no_options_avail}</font>
    {/if}
  {/if}
    
  {if $product.is_deleted ne "Y" and $product.display_options ne ""}
    <table cellpadding="3" cellspacing="1" id="box_opts_{%prod_num.index%}"{if $product.adv_option_choice eq "Y" and $product.keep_options ne "N"} style="display:none;"{/if} width="100%">
      {assign var="cname" value="product_details[`$smarty.section.prod_num.index`][product_options]}
      {include file="modules/Product_Options/customer_options.tpl" product_options=$product.display_options cname=$cname disable=$product.deleted nojs='Y'}
    </table>
  {/if}
  </td>

  <td valign="top">{include file="modules/Product_Options/display_options.tpl" options=$orig_product.product_options options_txt=$orig_product.product_options_txt force_product_options_txt=$orig_product.force_product_options_txt}</td>
</tr>
{/if}
<tr>
  <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>
{/section}
{else}
<tr>
  <th colspan="3">{$lng.lbl_aom_no_products_ordered}</th>
</tr>
{/if}
<tr>
  <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>
<tr>
  <td valign="top" colspan="3">{include file="main/subheader.tpl" title=$lng.lbl_add_product}</td>
</tr>
<tr>
  <td colspan="3">

<table cellpadding="0" cellspacing="0">
<tr>
  <th>#</th>
  <td><input type="text" size="7" name="newproductid" readonly="readonly" /></td>
  <td><input type="text" size="32" name="newproduct" readonly="readonly" /></td>
  <td><input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="popup_product('editpoduct_form.newproductid', 'editpoduct_form.newproduct');" /></td>
</tr>
</table>

  </td>
</tr>

<tr>
<td colspan="3"><br />
<input type="submit" value="{$lng.lbl_update}" />
<br /><br />
</td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_aom_edit_products_title|substitute:"orderid":$order.orderid content=$smarty.capture.dialog extra='width="100%"'}
