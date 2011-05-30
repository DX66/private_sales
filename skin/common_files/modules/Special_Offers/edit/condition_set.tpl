{*
$Id: condition_set.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $condition.params eq ""}
{$lng.txt_sp_empty_params_generic_edit}
{else}
<table cellpadding="3" cellspacing="1">
<tr valign="middle">
  <td>{$lng.lbl_sp_condition_set_action}:</td>
  <td>
  <select name="condition[{$condition.condition_type}][amount_type]">
    <option value="E"{if $condition.amount_type eq "E"} selected="selected"{/if}>{$lng.lbl_sp_action_one}</option>
    <option value="N"{if $condition.amount_type eq "N"} selected="selected"{/if}>{$lng.lbl_sp_action_copy}</option>
  </select>
  </td>
</tr>
</table>
<b>{$lng.lbl_note}:</b> {$lng.lbl_offers_sp_action_note}<br />
<br />
{/if}
{include file="modules/Special_Offers/edit/product_n_category.tpl" item=$condition item_type="C" with_qnty="Y" join_type="and" promo_opt="Y"}
