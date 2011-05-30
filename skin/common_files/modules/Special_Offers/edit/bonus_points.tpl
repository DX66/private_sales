{*
$Id: bonus_points.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table>
<tr>
  <td>
<script type="text/javascript">
//<![CDATA[
var bonus_{$bonus.bonus_type}_enable = {ldelim}
  F: ["bonus_{$bonus.bonus_type}_amount_min_F"],
  S: ["bonus_{$bonus.bonus_type}_amount_max_S","bonus_{$bonus.bonus_type}_amount_min_S","bonus_{$bonus.bonus_type}_total_type_S","bonus_{$bonus.bonus_type}_apply_to_cnd_sets_S"]
{rdelim};

function bonus_{$bonus.bonus_type}_click(active) {ldelim}
  var rules = bonus_{$bonus.bonus_type}_enable;
{literal}
  for (var label in rules) {
    for (var idx in rules[label]) {
      if (document.getElementById(rules[label][idx]))
        document.getElementById(rules[label][idx]).disabled = (label != active);
    }
  }
{/literal}
{rdelim}

var sp_total_types = [];
{foreach from=$sp_total_types key=type item=label}
sp_total_types['{$type}'] = '{$label}';
{/foreach}

var total_type_label = '{$lng.lbl_sp_points_per_total|substitute:"total":"to_replace"|wm_remove|escape:javascript}';

function total_type_change(type) {ldelim}

  label_box = document.getElementById('bonus_{$bonus.bonus_type}_type_S_label');
  if (label_box) {ldelim}
    label_box.innerHTML = total_type_label.replace('to_replace', sp_total_types[type]);
  {rdelim}
{rdelim}
//]]>
</script>
<input id="bonus_{$bonus.bonus_type}_type_F" type="radio" name="bonus[{$bonus.bonus_type}][amount_type]" onclick="bonus_{$bonus.bonus_type}_click('F')" value="F"{if $bonus.amount_type ne "S"} checked="checked"{/if} />
  </td>
  <td colspan="7">
<label for="bonus_{$bonus.bonus_type}_type_F">{$lng.lbl_sp_points_fixed_amount}</label>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>{$lng.lbl_sp_points}:</td>
  <td colspan="6"><input type="text" size="5" name="bonus[{$bonus.bonus_type}][amount_min]" id="bonus_{$bonus.bonus_type}_amount_min_F" value="{$bonus.amount_min|string_format:"%d"}" /></td>
</tr>
<tr>
  <td>
<input id="bonus_{$bonus.bonus_type}_type_S" type="radio" name="bonus[{$bonus.bonus_type}][amount_type]" onclick="bonus_{$bonus.bonus_type}_click('S')" value="S"{if $bonus.amount_type eq "S"} checked="checked"{/if} />
  </td>
  <td colspan="7">
    {assign var="total_type" value=$bonus.bonus_data.total_type}
    {if $total_type eq ""}
    {assign var="total_type" value="ST"}
    {/if}
    <label for="bonus_{$bonus.bonus_type}_type_S" id="bonus_{$bonus.bonus_type}_type_S_label">{$lng.lbl_sp_points_per_total|substitute:"total":$sp_total_types[$total_type]}</label>
  </td>
</tr>
<tr>
   <td>&nbsp;</td>
  <td>{$lng.lbl_sp_points}:</td>
  <td><input type="text" size="5" name="bonus[{$bonus.bonus_type}][amount_min]" id="bonus_{$bonus.bonus_type}_amount_min_S" value="{$bonus.amount_min|string_format:"%d"}" /></td>
  {include file="modules/Special_Offers/edit/currency_box.tpl" box_name="bonus[`$bonus.bonus_type`][amount_max]" value=$bonus.amount_max box_id="bonus_`$bonus.bonus_type`_amount_max_S" label=$lng.lbl_sp_points_per_}
  <td>{$lng.lbl_of}</td>
  <td>
    <select name="bonus[{$bonus.bonus_type}][bonus_data][total_type]" id="bonus_{$bonus.bonus_type}_total_type_S" onchange="javascript: total_type_change(this.value);">
      {foreach from=$sp_total_types key=type item=label}
      <option value="{$type}"{if $bonus.bonus_data.total_type eq $type} selected="selected"{/if}>{$label}</option>
      {/foreach}
    </select>
  </td>
</tr>
</table>

<table cellpadding="3" cellspacing="1">
<tr>
  <td><input type="checkbox" name="bonus[{$bonus.bonus_type}][bonus_data][apply_to_cnd_sets]" id="bonus_{$bonus.bonus_type}_apply_to_cnd_sets_S" value="Y"{if $bonus.bonus_data.apply_to_cnd_sets eq "Y"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_sp_apply_bonus_to_product_sets|substitute:"offerid":$offerid}</td>
</tr>
<tr>
  <td><input type="checkbox" name="bonus[{$bonus.bonus_type}][bonus_data][replace_bp]" value="Y"{if $bonus.bonus_data.replace_bp eq "Y"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_sp_replace_products_bp_amount}</td>
</tr>
</table>

<table cellpadding="3" cellspacing="1">
<tr>
  <td>&nbsp;</td>
  <td>
    <script type="text/javascript">
    //<![CDATA[
    setTimeout("bonus_{$bonus.bonus_type}_click('{$bonus.amount_type}')", 100);
    //]]>
    </script>
    <input type="submit" value=" {$lng.lbl_update} " />
  </td>
</tr>
</table>
