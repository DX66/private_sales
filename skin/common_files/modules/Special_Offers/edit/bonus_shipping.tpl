{*
$Id: bonus_shipping.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
function check_shipping_option(flag) {ldelim}

  shippng_box = document.wizardform.elements['new_param_S_shipping_ids[]'];

  if (shippng_box) {ldelim}
    shippng_box.disabled = flag ? '' : 'disabled';
  {rdelim}
{rdelim}
//]]>
</script>

<input type="hidden" name="bonus[{$bonus.bonus_type}][amount_min]" value="0" />

<table cellpadding="1" cellspacing="1" border="0">
<tr>
  <td><input type="checkbox" name="bonus[{$bonus.bonus_type}][bonus_data][apply_to_shipping]" value="Y"{if $bonus.bonus_data.apply_to_shipping eq "Y"} checked="checked"{/if} onclick="javascript: check_shipping_option(this.checked);" /></td>
  <td>{$lng.lbl_sp_apply_bonus_to_shipping_methods}</td>
</tr>
<tr>
  <td colspan="2">
  <select name="new_param_S_shipping_ids[]" size="5" multiple="multiple"{if $bonus.bonus_data.apply_to_shipping ne "Y"} disabled="disabled"{/if}>
  {foreach from=$shipping item=ship_method}
    <option value="{$ship_method.shippingid}"{if $selected_shipping[$ship_method.shippingid]} selected="selected"{/if}>{$ship_method.shipping|trademark} ({if $ship_method.destination eq "I"}{$lng.lbl_intl|wm_remove|escape}{else}{$lng.lbl_national|wm_remove|escape}{/if})</option>
  {/foreach}
  </select>
  </td>
</tr>
</table>

<br />

{$lng.lbl_sp_give_free_shipping_for}:
{include file="modules/Special_Offers/edit/product_n_category.tpl" item=$bonus item_type="B" empty_params_lbl=$lng.txt_sp_empty_params_bonus_shipping}
