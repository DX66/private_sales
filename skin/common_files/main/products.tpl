{*
$Id: products.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $products ne ""}
<br />
{include file="main/check_all_row.tpl" style="line-height: 170%;" form="processproductform" prefix="productids"}
<br />
<script type="text/javascript">
//<![CDATA[
var txt_pvariant_edit_note_list = "{$lng.txt_pvariant_edit_note_list|wm_remove|escape:javascript}";

{literal}
function pvAlert(obj) {
  if (obj.pvAlertFlag)
    return false;

  alert(txt_pvariant_edit_note_list);
  obj.pvAlertFlag = true;
  return true;
}
{/literal}
//]]>
</script>

<table cellpadding="2" cellspacing="1" width="100%">

{if $main eq "category_products"}
{assign var="url_to" value="category_products.php?cat=`$cat`&amp;page=`$navpage`"}
{else}
{assign var="url_to" value="search.php?mode=search&amp;page=`$navpage`"}
{/if}

<tr class="TableHead">
  <td width="5">&nbsp;</td>
  <td nowrap="nowrap">{if $search_prefilled.sort_field eq "productcode"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=productcode&amp;sort_direction={if $search_prefilled.sort_field eq "productcode"}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_sku}</a></td>
  <td width="100%" nowrap="nowrap">{if $search_prefilled.sort_field eq "title"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=title&amp;sort_direction={if $search_prefilled.sort_field eq "title"}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_product}</a></td>
{if $main eq "category_products"}
  <td nowrap="nowrap">{if $search_prefilled.sort_field eq "orderby"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=orderby&amp;sort_direction={if $search_prefilled.sort_field eq "orderby"}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_pos}</a></td>
{/if}
  <td nowrap="nowrap">{if $search_prefilled.sort_field eq "quantity"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=quantity&amp;sort_direction={if $search_prefilled.sort_field eq "quantity"}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_in_stock}</a></td>
  <td nowrap="nowrap">{if $search_prefilled.sort_field eq "price"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=price&amp;sort_direction={if $search_prefilled.sort_field eq "price"}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_price} ({$config.General.currency_symbol})</a></td>
</tr>

{section name=prod loop=$products}

<tr{cycle values=', class="TableSubHead"'}>
  <td width="5"><input type="checkbox" name="productids[{$products[prod].productid}]" /></td>
  <td><a href="product_modify.php?productid={$products[prod].productid}{if $navpage}&amp;page={$navpage}{/if}">{$products[prod].productcode}</a></td>
  <td width="100%">{if $products[prod].main eq "Y" or $main ne "category_products"}<b>{/if}<a href="product_modify.php?productid={$products[prod].productid}{if $navpage}&amp;page={$navpage}{/if}">{$products[prod].product}</a>{if $products[prod].main eq "Y" or $main ne "category_products"}</b>{/if}</td>
{if $main eq "category_products"}
  <td><input type="text" size="9" maxlength="10" name="posted_data[{$products[prod].productid}][orderby]" value="{$products[prod].orderby}" /></td>
{/if}
  <td align="center">
{if $products[prod].product_type ne 'C'}
<input type="text" size="9" maxlength="10" name="posted_data[{$products[prod].productid}][avail]" value="{$products[prod].avail}"{if $products[prod].is_variants eq 'Y'} readonly="readonly" onclick="javascript: pvAlert(this);"{/if} />
{/if}
  </td>
  <td>
{if $products[prod].product_type ne 'C'}
<input type="text" size="9" maxlength="15" name="posted_data[{$products[prod].productid}][price]" value="{$products[prod].price|formatprice}"{if $products[prod].is_variants eq 'Y'} readonly="readonly" onclick="javascript: pvAlert(this);"{/if} />
{/if}
  </td>

</tr>

{/section}

</table>
{/if}
