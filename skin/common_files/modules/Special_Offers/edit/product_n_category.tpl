{*
$Id: product_n_category.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/modules/Special_Offers/edit/product_n_category.js"></script>

{if $item_type eq "B" and $item.bonus_data.use_cnd_sets eq "Y"}
{assign var="params" value=$item.saved_params}
{else}
{assign var="params" value=$item.params}
{/if}

{assign var="form_name" value="wizardform"}

{if $item_type eq "B"}
{assign var="mainid" value=$item.bonus_type}
{elseif $item_type eq "C"}
{assign var="mainid" value=$item.condition_type}
{/if}

{assign var="product_sets_I" value=$item.product_sets.I}
{assign var="product_sets_E" value=$item.product_sets.E}
{if not $product_sets_I}
{assign var="product_sets_I" value=0}
{/if}

<br /><br />

<div id="product_sets_{$mainid}"{if $item.bonus_data.use_cnd_sets eq "Y"} style="display: none;"{/if}>

{foreach name="prod_sets" from=$product_sets_I item=set_id}
{if $set_id ne $fake_product_set_id}
{include file="modules/Special_Offers/edit/product_set.tpl"}
{/if}
{if $item_type eq "C" and not $smarty.foreach.prod_sets.last}
<div align="center" class="sp-product-set-box-sep-big"> - {$lng.lbl_or} - </div>
{/if}
{/foreach}

<br />

</div>

{if $item_type eq "B" and $offer.has_cnd_product_set ne "" and $item.bonus_type ne "P"}
<table cellpadding="1" cellspacing="1" border="0">
<tr>
  <td><input type="checkbox" name="bonus[{$item.bonus_type}][bonus_data][use_cnd_sets]" value="Y"{if $item.bonus_data.use_cnd_sets eq "Y"} checked="checked"{/if} onclick="javascript: use_offer_cnd_sets(this.checked, '{$mainid}');" /></td>
  <td>{$lng.lbl_sp_use_prod_sets_from_condition|substitute:"offerid":$offerid}</td>
</tr>
</table>
<br />
{/if}

<table cellpadding="5" cellspacing="1" border="0">
<tr>
  <td>
    <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
  {if $item_type eq "C"}
  <td width="100%" align="right">
    <input type="button" value="{$lng.lbl_sp_add_product_set}" onclick="javascript: add_set('{$form_name}');" />
  </td>
  {/if}
</tr>
</table>

{if $item_type eq "B" and $item.bonus_type eq "D" and $product_sets_E}
<br />
<div>{$lng.lbl_sp_bonus_not_apply_to_list}</div>
<br />
{foreach name="prod_sets" from=$product_sets_E item=set_id}
{include file="modules/Special_Offers/edit/product_set.tpl" params=$item.params viewonly="Y"}
{/foreach}
<br />
{$lng.txt_sp_excl_product_set_note}
{/if}

