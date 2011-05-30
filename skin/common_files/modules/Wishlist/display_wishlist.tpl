{*
$Id: display_wishlist.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_wish_list}

{$lng.txt_admin_wishlists}

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="wishlists.php"}</div>

<b>{$lng.lbl_customer}:</b>

{$wishlist.0.firstname} {$wishlist.0.lastname} ({$wishlist.0.login})

<br /><br />

<table cellpadding="1" cellspacing="2" width="100%">
<tr class="TableHead">
  <td width="10%" nowrap="nowrap">{$lng.lbl_sku}</td>
  <td width="45%" nowrap="nowrap">{$lng.lbl_product}</td>
  <td width="35%" nowrap="nowrap">{$lng.lbl_selected_options}</td>
  <td width="10%" nowrap="nowrap">{$lng.lbl_quantity}</td>
</tr>

{foreach from=$wishlist item=v}
<tr{cycle name=c1 values=', class="TableSubHead"'}>
  <td><a href="product_modify.php?productid={$v.productid}">{$v.productcode}</a></td>
  <td><a href="product_modify.php?productid={$v.productid}">{$v.product|truncate:35:"...":false}</a></td>
  <td>{if $v.product_options ne ''}{include file="modules/Product_Options/display_options.tpl" options=$v.product_options}{/if}</td>
  <td align="center">{$v.amount}</td>
</tr>
{/foreach}

</table>
<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_wish_list content=$smarty.capture.dialog extra='width="100%"'}
