{*
$Id: condition_total.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
function promote_amount_click(flag) {ldelim}

  amount_box = document.getElementById('condition_{$condition.condition_type}_promo_amount');
  if (amount_box) {ldelim}
    amount_box.disabled = flag ? '' : 'disabled';
  {rdelim}
{rdelim}
//]]>
</script>

<table cellpadding="3" cellspacing="1">
<tr>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="condition[`$condition.condition_type`][amount_min]" value=$condition.amount_min label="`$lng.lbl_sp_min_amount`:" extra="width='100%'"}
</tr>
<tr>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="condition[`$condition.condition_type`][amount_max]" value=$condition.amount_max label="`$lng.lbl_sp_max_amount`:" extra="width='100%'"}
</tr>
<tr>
  <td>&nbsp;</td>
  <td colspan="3">{$lng.lbl_sp_max_amount_note|substitute:"zero":$zero}</td>
</tr>
</table>

<table cellpadding="3" cellspacing="1">
<tr>
  <td><input type="checkbox" name="condition[{$condition.condition_type}][condition_data][show_promo]" value="Y"{if $condition.condition_data.show_promo eq "Y"} checked="checked"{/if} onclick="javascript: promote_amount_click(this.checked);" /></td>
  {if $condition.condition_data.show_promo ne "Y"}  
  {assign var="box_disabled" value=true}
  {/if}
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="condition[`$condition.condition_type`][condition_data][promo_amount]" box_id="condition_`$condition.condition_type`_promo_amount" value=$condition.condition_data.promo_amount is_disabled=$box_disabled label=$lng.lbl_sp_display_promo_for_amount extra="width='100%'"}
</tr>
</table>

<br />

<table cellpadding="3" cellspacing="1">
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" value=" {$lng.lbl_update} " /></td>
</tr>
</table>
