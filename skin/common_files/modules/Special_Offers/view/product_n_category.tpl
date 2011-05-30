{*
$Id: product_n_category.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $item_type eq "B" and $item.bonus_data.use_cnd_sets eq "Y"}
{foreach from=$offer.conditions item=condition}
{if $condition.condition_type eq "S"}
{assign var="params" value=$condition.params}
{assign var="force_display" value="Y"}
{/if}
{/foreach}
{else}
{assign var="params" value=$item.params}
{/if}

{assign var="product_sets_I" value=$item.product_sets.I}
{assign var="product_sets_E" value=$item.product_sets.E}
{if not $product_sets_I or $force_display eq "Y"}
{assign var="product_sets_I" value=0}
{/if}

<table width="100%" cellspacing="3" cellpadding="3" border="0">
{foreach name="prod_sets" from=$product_sets_I item=set_id}
{if ($set_id ne $fake_product_set_id) or ($force_display eq "Y")}
<tr>
  <td>{include file="modules/Special_Offers/view/param_set.tpl"}</td>
</tr>
{/if}
{if $item_type eq "C" and not $smarty.foreach.prod_sets.last}
<tr>
  <td class="sp-product-set-box-sep-small"> - {$lng.lbl_or} - </td>
</tr>
{/if}
{/foreach}
</table>

{if $item_type eq "B" and $item.bonus_type eq "D" and $product_sets_E}
<br />
{$lng.lbl_sp_bonus_not_apply_to_list}
<table width="100%" cellspacing="3" cellpadding="3" border="0">
{foreach name="prod_sets" from=$product_sets_E item=set_id}
<tr>
  <td>{include file="modules/Special_Offers/view/param_set.tpl" params=$item.params force_display="N"}</td>
</tr>
{/foreach}
</table>
{/if}

