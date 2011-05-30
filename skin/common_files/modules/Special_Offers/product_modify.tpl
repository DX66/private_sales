{*
$Id: product_modify.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><hr /></td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[sp_data][sp_discount_avail]" /></td>{/if}
  <td class="FormButton">{$lng.lbl_sp_apply_offers_discounts}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="sp_data[sp_discount_avail]" value="Y"{if $product.productid eq "" or $product.sp_discount_avail eq "Y"} checked="checked"{/if} />
  </td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[sp_data][bonus_points]" /></td>{/if}
  <td class="FormButton">{$lng.lbl_sp_give_bp_for_each_item}:</td>
  <td class="ProductDetails">
  <input type="text" name="sp_data[bonus_points]" size="18" value="{$product.bonus_points|default:0}" />
  </td>
</tr>
