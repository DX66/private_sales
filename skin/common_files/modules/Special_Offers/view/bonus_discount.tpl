{*
$Id: bonus_discount.tpl,v 1.1.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%" cellspacing="3" cellpadding="0" border="0">
<tr>
  <td nowrap="nowrap">{$lng.lbl_sp_bonus_discount_amount}:</td>
  <td>&nbsp;</td>
  <td width="100%">
    {if $bonus.amount_type eq "%"}
    {$bonus.amount_min|default:0|string_format:"%.2f"} %
    {else}
    {currency value=$bonus.amount_min}
    {/if}
  </td>
</tr>
<tr>
  <td nowrap="nowrap">{$lng.lbl_sp_bonus_discount_limit}:</td>
  <td>&nbsp;</td>
  <td width="100%">
    {if $bonus.amount_max le 0}
    -
    {else}
    {if $bonus.amount_type eq "%"}
    {currency value=$bonus.amount_max}
    {else}
    {$bonus.amount_max|default:0|string_format:"%.2f"} %
    {/if}
    {/if}
  </td>
</tr>
</table>

<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
  <td colspan="3">{$lng.lbl_sp_apply_discount_to}:</td>
</tr>
<tr>
  <td colspan="3">
    {include file="modules/Special_Offers/view/product_n_category.tpl" item=$bonus item_type="B" empty_params_lbl=$lng.txt_sp_empty_params_bonus_discount}
  </td>
</tr>
</table>

