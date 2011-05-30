{*
$Id: featured_products.tpl,v 1.2 2010/06/11 13:57:50 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/popup_product.js"></script>

<a name="featured"></a>

{$lng.txt_featured_products}

<br /><br />

{capture name=dialog}

{if $products ne ""}
{include file="main/check_all_row.tpl" style="line-height: 170%;" form="featuredproductsform" prefix="posted_data.+to_delete"}
{/if}

<form action="categories.php" method="post" name="featuredproductsform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="cat" value="{$f_cat}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="70%">{$lng.lbl_product_name}</td>
  <td width="15%" align="center">{$lng.lbl_pos}</td>
  <td width="15%" align="center">{$lng.lbl_active}</td>
</tr>

{if $products}

{section name=prod_num loop=$products}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="posted_data[{$products[prod_num].productid}][to_delete]" /></td>
  <td><b><a href="product.php?productid={$products[prod_num].productid}" target="_blank">{$products[prod_num].product}</a></b></td>
  <td align="center"><input type="text" name="posted_data[{$products[prod_num].productid}][product_order]" size="5" value="{$products[prod_num].product_order}" /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$products[prod_num].productid}][avail]"{if $products[prod_num].avail eq "Y"} checked="checked"{/if} /></td>
</tr>

{/section}

<tr>
  <td colspan="4" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('posted_data\\[[0-9]+\\]\\[to_delete\\]', 'ig'))) {ldelim}document.featuredproductsform.mode.value = 'delete'; document.featuredproductsform.submit();{rdelim}" />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

{else}

<tr>
<td colspan="4" align="center">{$lng.txt_no_featured_products}</td>
</tr>

{/if}

<tr>
<td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_add_product}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td>
    <input type="hidden" name="newproductid" />
    <input type="text" size="35" name="newproduct" disabled="disabled" />
    <input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_product('featuredproductsform.newproductid', 'featuredproductsform.newproduct');" />
  </td>
  <td align="center"><input type="text" name="neworder" size="5" /></td>
  <td align="center"><input type="checkbox" name="newavail" checked="checked" /></td>
</tr>

<tr>
  <td colspan="4" class="SubmitBox">
  <input type="submit" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: document.featuredproductsform.mode.value = 'add'; document.featuredproductsform.submit();"/>
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_featured_products content=$smarty.capture.dialog extra='width="100%"'}
