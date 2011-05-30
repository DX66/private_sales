{*
$Id: bonus_discount.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var currency_symbol = '{$config.General.currency_symbol|wm_remove|escape:javascript}';
{literal}
function discount_type_changed() {
  var type = document.getElementById('discount_type');
  if (type) {
    if (type.value == '%') {
      document.getElementById('da_primary').innerHTML = '%';
      document.getElementById('da_limit').innerHTML = currency_symbol;
    }
    else {
      document.getElementById('da_primary').innerHTML = currency_symbol;
      document.getElementById('da_limit').innerHTML = '%';
    }
  }
}
{/literal}
//]]>
</script>

<table cellpadding="3" cellspacing="1">
<tr>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="bonus[`$bonus.bonus_type`][amount_min]" value=$bonus.amount_min curr_id="da_primary" curr_symbol=$bonus.amount_type label="`$lng.lbl_sp_bonus_discount_amount`:" extra="width='100%'"}
</tr>
<tr>
  <td>{$lng.lbl_sp_bonus_discount_type}:</td>
  <td colspan="3">
  <select id="discount_type" name="bonus[{$bonus.bonus_type}][amount_type]" onchange="javascript: discount_type_changed()">
    <option value="$"{if $bonus.amount_type eq "$"} selected="selected"{/if}>{$lng.lbl_absolute}</option>
    <option value="%"{if $bonus.amount_type eq "%"} selected="selected"{/if}>{$lng.lbl_percent}</option>
  </select>
</td>
</tr>
<tr>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="bonus[`$bonus.bonus_type`][amount_max]" value=$bonus.amount_max curr_id="da_limit" curr_symbol=$bonus.amount_type label="`$lng.lbl_sp_bonus_discount_limit`:" extra="width='100%'"}
</tr>
<tr>
  <td width="35%">{$lng.lbl_sp_allow_apply_discounts}:</td>
  <td colspan="3">
  <select name="bonus[{$bonus.bonus_type}][bonus_data][is_discount_avail]">
    <option value="Y"{if $bonus.bonus_data.is_discount_avail ne "N"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    <option value="N"{if $bonus.bonus_data.is_discount_avail eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
  </td>
</tr>

{if $bonus.params eq ""}
<tr>
  <td colspan="5"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
{/if}{* $bonus.params eq "" *}

</table>

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
discount_type_changed();
//]]>
</script>

<br />
{$lng.lbl_sp_apply_discount_to}:
{include file="modules/Special_Offers/edit/product_n_category.tpl" item=$bonus item_type="B" empty_params_lbl=$lng.txt_sp_empty_params_bonus_discount}
