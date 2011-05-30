{*
$Id: condition_points.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="3" cellspacing="1">
<tr>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="condition[`$condition.condition_type`][amount_min]" value=$condition.amount_min curr_symbol="&nbsp;" label="`$lng.lbl_sp_min_amount`:" extra="width='100%'" is_int="Y"}
</tr>
<tr>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="condition[`$condition.condition_type`][amount_max]" value=$condition.amount_max curr_symbol="&nbsp;" label="`$lng.lbl_sp_max_amount`:" extra="width='100%'" is_int="Y"}
</tr>
<tr>
  <td>&nbsp;</td>
  <td colspan="3">{$lng.lbl_sp_max_amount_note|substitute:"zero":$zero}</td>
</tr>
</table>

<table cellpadding="3" cellspacing="1">
<tr>
  <td><input type="checkbox" name="condition[{$condition.condition_type}][condition_data][reduce_bp]" value="Y"{if $condition.condition_data.reduce_bp eq "Y"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_sp_reduce_bp_on_complete}</td>
</tr>
</table>

<br />

<table cellpadding="3" cellspacing="1">
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" value=" {$lng.lbl_update} " /></td>
</tr>
</table>

