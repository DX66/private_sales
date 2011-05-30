{*
$Id: product_set.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="TplImages" value="`$SkinDir`/modules/Special_Offers/images"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="sp-product-set-box">
<tr>
  <td>
    <table cellpadding="6" cellspacing="3" border="0" width="100%" class="sp-product-set-box-head">
    <tr>
      {if $viewonly eq "Y"}
      <td>&nbsp;</td>
      {else}
      <td nowrap="nowrap">
        <img src="{$TplImages}/save_changes.gif" width="10" height="10" alt="" />&nbsp;
        <a href="javascript:save_changes('{$set_id}', '{$form_name}');">{$lng.lbl_sp_save_changes}</a>
      </td>
      <td>&nbsp;</td>
      <td nowrap="nowrap">
        <img src="{$TplImages}/add_product.gif" width="17" height="12" alt="" />&nbsp;
        {assign var="param_box" value="new_param_`$mainid`_productid_`$set_id`"}
        {if $item_type eq "B"}
        {assign var="only_regular" value="Y"}
        {/if}
        <input type="hidden" name="{$param_box}" value="" />
        <a href="javascript:add_param('{$param_box}', '{$form_name}', '{$set_id}', 'P', '{$only_regular}');">{$lng.lbl_add_product}</a>
      </td>
      <td>&nbsp;</td>
      <td nowrap="nowrap">
        <img src="{$TplImages}/add_category.gif" width="16" height="10" alt="" />&nbsp;
        {assign var="param_box" value="new_param_`$mainid`_categoryid_`$set_id`"}
        <input type="hidden" name="{$param_box}" value="" />
        <a href="javascript:add_param('{$param_box}', '{$form_name}', '{$set_id}', 'C');">{$lng.lbl_add_category}</a>
      </td>
      <td width="100%" align="right">
      {if $item_type ne "B"}
        <img src="{$TplImages}/delete_set.gif" width="7" height="7" alt="" />&nbsp;
        <a class="sp-product-set-box-del" href="javascript: delete_set('{$set_id}', '{$form_name}');">{$lng.lbl_sp_delete_product_set}</a>
      {else}
        &nbsp;
      {/if}
      </td>
      {/if}
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td style="padding: 10px;">
    <table cellpadding="5" cellspacing="1" border="0" width="100%">
    {assign var="is_first" value=true}
    {if $params}
    {foreach name="set_params" from=$params item=param}
    {if $param.setid eq $set_id and $param.param_type ne "S"}
    {if not $is_first}
    <tr>
      <td class="sp-product-set-box-sep-small"> - {if $join_type eq "and"}{$lng.lbl_sp_and}{else}{$lng.lbl_sp_or}{/if} - </td>
    </tr>
    {else}
    <tr>
      <td class="FormButton" width="100%" nowrap="nowrap">{$lng.lbl_products}</td>
      {if $with_qnty eq "Y"}
      <td class="FormButton" nowrap="nowrap">{$lng.lbl_quantity}</td>
      {/if}
      {if $promo_opt eq "Y"}
      <td class="FormButton" nowrap="nowrap">
        {include file="main/tooltip_js.tpl" id="help_`$set_id`_promote" title=$lng.lbl_sp_promote_item text=$lng.txt_sp_item_promo_note}
      </td>
      {/if}
      <td>&nbsp;</td>
    </tr>
    {assign var="is_first" value=false}
    {/if}
    <tr class="TableSubHead" style="height: 30px;">
      <td>
        <table cellpadding="1" cellspacing="1" border="0" width="100%">
        <tr>
        {if $param.param_type eq "P"}
          <td nowrap="nowrap">{$lng.lbl_product}: ({$param.productcode}) <a href="product_modify.php?productid={$param.param_id}">{$param.product}</a></td>
        {elseif $param.param_type eq "C"}
          {if $param.param_arg eq "Y"}
          {assign var="subcats" value="&amp;subcats=Y"}
          {else}
          {assign var="subcats" value=""}
          {/if}
          <td nowrap="nowrap">{$lng.lbl_sp_products_from_cat_s|substitute:"cat":"<a href=\"search.php?mode=search&amp;cat=`$param.param_id``$subcats`\">`$param.category`</a>"}</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td>
            {assign var="param_box" value="param[`$param.paramid`][param_arg]"}
            <input type="hidden" name="{$param_box}" value="{$param.param_arg}" />
            <input type="checkbox"{if $param.param_arg eq "Y"} checked="checked"{/if} value="Y" onclick="javascript: set_hidden_value('{$param_box}', '{$form_name}', this.checked);" />
          </td>
          <td width="100%" nowrap="nowrap">{$lng.lbl_sp_with_subcategories}</td>
        {/if}
        </tr>
        </table>
      </td>
      {if $with_qnty eq "Y"}
      <td align="center">
        <input type="text" name="param[{$param.paramid}][param_qnty]" value="{$param.param_qnty}" size="4" />
      </td>
      {/if}
      {if $promo_opt eq "Y"}
      <td align="center">
        {assign var="param_box" value="param[`$param.paramid`][param_promo]"}
        <input type="hidden" name="{$param_box}" value="{$param.param_promo}" />
        <input type="checkbox"{if $param.param_promo eq "Y"} checked="checked"{/if} value="Y" onclick="javascript: set_hidden_value('{$param_box}', '{$form_name}', this.checked);" />
      </td>
      {/if}
      {if $viewonly ne "Y"}
      <td align="center">
        <input type="checkbox" name="param_del[{$param.paramid}]" value="Y" style="display: none;" />
        <a class="sp-product-set-box-del" href="javascript: delete_param('{$param.paramid}', '{$form_name}');">{$lng.lbl_delete}</a>
      </td>
      {/if}
    </tr>
    {/if}
    {/foreach}
    {/if}
    {if $is_first}
    <tr>
      <td colspan="4" align="center">{if $item_type eq "B" and $empty_params_lbl ne ""}{$empty_params_lbl}{else}{$lng.lbl_no_products_selected}{/if}</td>
    </tr>
    {/if}
    </table>
  </td>
</tr>
</table>

