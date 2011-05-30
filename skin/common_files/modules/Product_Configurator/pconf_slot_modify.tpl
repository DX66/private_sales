{*
$Id: pconf_slot_modify.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{literal}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
  $("#multiple_flag").click(function(){
    if (this.checked) {
      $("#ranges").show();
    } else {
      $("#ranges").hide();
    }
  });

  $(".numeric").keyup(function(e){
    var tmp = parseInt(this.value);
    if (!isNaN(tmp)) {
      this.value = tmp;
      var def = parseInt($("#amount_def").val());
      var min = parseInt($("#amount_min").val());
      var max = parseInt($("#amount_max").val());
      def = def > max ? max : def < min ? min : def;
      $("#amount_def").val(def);
    } 
  });

});
//]]>
</script>
{/literal}

{include file="modules/Product_Configurator/popup_slot_products_js.tpl"}

{$lng.txt_pconf_slot_modify_title}
<br /><br />
<a name="slot"></a>

{capture name=dialog}
<br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_step_x_details|substitute:"step_counter":$wizard_data.step_counter href="product_modify.php?productid=`$product.productid`&amp;mode=pconf&amp;edit=wizard&amp;step=`$step`#step"}</div>

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_pconf_slot_details}</td>
</tr>

<tr>
  <td colspan="2">{include file="main/language_selector.tpl" script="product_modify.php?`$smarty.server.QUERY_STRING`&amp;" anchor="slot"}</td>
</tr>
</table>

<form action="product_modify.php" method="post" name="slotform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="slot" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="slot" value="{$slot}" />
<input type="hidden" name="action" value="update_slot" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
  <td width="30%">{$lng.lbl_pconf_slot_name}:</td>
  <td width="70%"><input type="text" size="35" name="posted_data[slot_name]" value="{$slot_data.slot_name|escape}" title="{$lng.lbl_pconf_slot_name_hint|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_pconf_slot_descr}:</td>
  <td><textarea cols="35" rows="5" name="posted_data[slot_descr]" title="{$lng.lbl_pconf_slot_descr_hint|escape}">{$slot_data.slot_descr|escape}</textarea></td>
</tr>

<tr>
  <td>{$lng.lbl_status}:</td>
  <td>
  <select name="posted_data[status]">
    <option value="O"{if $slot_data.status eq "O"} selected="selected"{/if}>{$lng.lbl_pconf_slot_st_optional|escape}</option>
    <option value="M"{if $slot_data.status eq "M"} selected="selected"{/if}>{$lng.lbl_pconf_slot_st_mandatory|escape}</option>
    <option value="N"{if $slot_data.status eq "N"} selected="selected"{/if}>{$lng.lbl_pconf_slot_st_disabled|escape}</option>
  </select>
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

<tr>
  <td>{$lng.lbl_pconf_slot_orderby}:</td>
  <td><input type="text" size="5" name="posted_data[orderby]" value="{$slot_data.orderby}" title="{$lng.lbl_pconf_slot_orderby_hint|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_step}:</td>
  <td>
  <select name="posted_data[stepid]">
{section name=wz loop=$wizards}
    <option value="{$wizards[wz].stepid}"{if $slot_data.stepid eq $wizards[wz].stepid} selected="selected"{/if}>{$lng.lbl_step} {$wizards[wz].step_counter}: {$wizards[wz].step_name|escape}</option>
{/section}
  </select>
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

<tr>
  <td colspan="2"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>

<tr>
  <td colspan="2"><br />&nbsp;</td>
</tr>
</table>
</form>

<br />

<form action="product_modify.php" method="post" name="slotrulesform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="slot" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="slot" value="{$slot}" />
<input type="hidden" name="action" value="update_rules" />

<table cellpadding="2" cellspacing="2" width="100%">
<tr>
  <td colspan="2"><a name="rules"></a>{include file="main/subheader.tpl" title=$lng.lbl_pconf_slot_rules}</td>
</tr>

{if $rules_by_or}
<tr>
  <td colspan="2">
{$lng.lbl_pconf_slot_can_contain_ptypes}:<br /><br />
<table cellpadding="2" cellspacing="2">
{section name=or loop=$rules_by_or}
<tr{cycle values=" class='TableSubHead',"}>
  <td><input type="checkbox" id="posted_data_{$rules_by_or[or].index_by_and}_delete" name="to_delete[{$rules_by_or[or].index_by_and}]" title="{$lng.lbl_pconf_tick_delete_hint|escape}" value="Y" /></td>
  <td>
  <label for="posted_data_{$rules_by_or[or].index_by_and}_delete">
{inc value=%or.index%}.
{section name=and loop=$rules_by_or[or].rules_by_and}
{$rules_by_or[or].rules_by_and[and].ptype_name|amp}
{if not %and.last%}
<b>&lt;{$lng.lbl_pconf_and}&gt;</b>
{/if}
{/section}
  </label>
  </td>
</tr>
{if not %or.last%}
<tr>
  <td>&nbsp;</td>
  <td><b>{$lng.lbl_pconf_or}</b></td>
</tr>
{/if}
{/section}
</table>
  </td>
</tr>

<tr>
  <td colspan="2"><br />
{$lng.lbl_pconf_tick_delete_note}
<br /><br />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(document.slotrulesform, new RegExp('to_delete', 'gi'))) {ldelim}document.slotrulesform.action.value='delete_rules'; document.slotrulesform.submit();{rdelim}" />
  </td>
</tr>

<tr>
  <td colspan="2"><br />&nbsp;</td>
</tr>

{/if}

{if $product_types}
<tr>
  <td colspan="2">{$lng.lbl_pconf_slot_add_allowable_types}:</td>
</tr>

<tr>
  <td colspan="2">
  <select name="add_rules[]" multiple="multiple" size="8">
{section name=pt loop=$product_types}
    <option value="{$product_types[pt].ptypeid}">{$product_types[pt].ptype_name|escape}</option>
{/section}
  </select>
<br /><br />
{$lng.lbl_pconf_slot_note_ctrl_types}
  </td>
</tr>

<tr>
  <td colspan="2"><input type="submit" value="{$lng.lbl_pconf_add_rule|strip_tags:false|escape}" /></td>
</tr>
{/if}

<tr>
  <td colspan="2"><hr /></td>
</tr>

<tr>
  <td>{$lng.lbl_pconf_multiple_items}:</td>
  <td>
<table>
<tr>
  <td><input type="checkbox" id="multiple_flag" name="posted_data[multiple]" value="Y"{if $slot_data.multiple eq "Y"} checked="checked"{/if} /></td>
  <td>
    {include file="main/tooltip_js.tpl" text=$lng.txt_pconf_multiple_items_hlp id="multiple_items_hlp"}
  </td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

<tr id="ranges"{if $slot_data.multiple ne "Y"} style="display: none;"{/if}>
  <td>&nbsp;</td>
  <td>
<table>
<tr>
  <td>{$lng.lbl_pconf_min}:</td>
  <td><input type="text" class="numeric" size="4" id="amount_min" name="posted_data[amount_min]" value="{$slot_data.amount_min|default:1}" /></td>
  <td>{$lng.lbl_pconf_max}:</td>
  <td><input type="text" class="numeric" size="4" id="amount_max" name="posted_data[amount_max]" value="{$slot_data.amount_max|default:0}" /></td>
  <td>{$lng.lbl_pconf_def}:</td>
  <td><input type="text" class="numeric" size="4" id="amount_def" name="posted_data[default_amount]" value="{$slot_data.default_amount|default:1}" /></td>
</tr>
</table>
  <br />
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

<tr>
  <td valign="top">{$lng.lbl_pconf_default_product}:</td>
  <td>
<input type="hidden" name="posted_data[default_productid]" id="def_pid" value="{$slot_data.default_productid}" />
<table>
<tr>
  <td><span id="def_name">{if $slot_data.default_product ne ""}<a href="product.php?productid={$slot_data.default_productid}" target="_blank">{$slot_data.default_product}</a>{else}{$lng.lbl_pconf_default_product_not_defined}{/if}</span></td>
  <td id="change_btn"{if $slot_data.default_product eq ""} style="display: none;"{/if} class="ButtonsRowRight">[&nbsp;<strong><a href="javascript:void(0);" onclick="javascript: popupSlotProducts('{$slot}','{$product.productid}');">{$lng.lbl_change}</a></strong>&nbsp;]</td>
  <td id="delete_btn"{if $slot_data.default_product eq ""} style="display: none;"{/if} class="ButtonsRowRight">[&nbsp;<a href="javascript:void(0);" onclick="javascript: removeDefaultProduct()"><strong><font class="Star">{$lng.lbl_delete}</font></strong></a>&nbsp;]</td>
  <td id="choose_btn"{if $slot_data.default_product ne ""} style="display: none;"{/if} class="ButtonsRowRight">[&nbsp;<strong><a href="javascript:void(0);" onclick="javascript: popupSlotProducts('{$slot}','{$product.productid}');">{$lng.lbl_choose_product}</a></strong>&nbsp;]</td>
  <td class="ButtonsRowRight">
    {include file="main/tooltip_js.tpl" id="help_default_product" text=$lng.txt_pconf_default_product_hlp}
  </td>
</tr>
</table>
<br />
<span id="save_msg"></span></center></td>
  </td>
</tr>

<tr>
  <td colspan="2">
    <input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: document.slotrulesform.action.value='update_slot_capacity'; document.slotrulesform.submit();" />
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

</table>

</form>

<br />

<form action="product_modify.php" method="post" name="pricemodifiersform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="slot" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="slot" value="{$slot}" />
<input type="hidden" name="action" value="update_markups" />

<table cellpadding="2" cellspacing="2" width="100%">
<tr>
  <td colspan="2"><a name="price"></a>{include file="main/subheader.tpl" title=$lng.lbl_pconf_slot_price_modifiers}</td>
</tr>

<tr>
  <td colspan="2">
{$lng.txt_pconf_pricemods_descr}
<br /><br />
<table cellpadding="2" cellspacing="2">

<tr class="TableHead">
  <th width="20">V</th>
  <th align="left">{$lng.lbl_pconf_pricemods_value}</th>
  <th align="left">{$lng.lbl_pconf_pricemods_type}</th>
  <th align="left">{$lng.lbl_pconf_pricemods_membership}</th>
</tr>

{if $markups}
{section name=mk loop=$markups}
<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="posted_data[{$markups[mk].markupid}][delete]" title="{$lng.lbl_pconf_pricemod_tick_del_hint|escape}" /></td>
  <td><input type="text" size="16" maxlength="12" name="posted_data[{$markups[mk].markupid}][markup]" value="{$markups[mk].markup|formatprice}" title="{$lng.lbl_pconf_pricemod_price_hint|escape}" />
  <td>
  <select name="posted_data[{$markups[mk].markupid}][markup_type]">
    <option value="%"{if $markups[mk].markup_type eq "%"} selected="selected"{/if}>{$lng.lbl_pconf_pricemod_markup_percent|escape} (%)</option>
    <option value="$"{if $markups[mk].markup_type eq "$"} selected="selected"{/if}>{$lng.lbl_pconf_pricemod_markup_absolute|escape} ({$config.General.currency_symbol})</option>
  </select>
  </td>
  <td>
  <select name="posted_data[{$markups[mk].markupid}][membershipid]">
    <option value="0">{$lng.lbl_all}</option>
{foreach from=$memberships item=m}
    <option value="{$m.membershipid}"{if $markups[mk].membershipid eq $m.membershipid} selected="selected"{/if}>{$m.membership|escape}</option>
{/foreach}
  </select>
  </td>
</tr>
{/section}

<tr>
<td colspan="4">
{$lng.lbl_pconf_tick_delete_note}
<br /><br />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('posted_data', 'gi'))) {ldelim}document.pricemodifiersform.action.value='delete_markups'; document.pricemodifiersform.submit();{rdelim}" />
<br /><br />
</td>
</tr>

<tr>
  <td colspan="4"><b>{$lng.lbl_pconf_slot_add_new_modifier}:</b></td>
</tr>
{/if}

<tr>
  <td>&nbsp;</td>
  <td><input type="text" size="16" maxlength="12" name="new_markup" title="{$lng.lbl_pconf_pricemod_price_hint|escape}" /></td>
  <td>
  <select name="new_markup_type"> 
    <option value="%">{$lng.lbl_pconf_pricemod_markup_percent} (%)</option>
    <option value="$">{$lng.lbl_pconf_pricemod_markup_absolute} ({$config.General.currency_symbol})</option> 
  </select>
  </td>
  <td>
  <select name="new_membershipid">
    <option value="0">{$lng.lbl_all}</option>
{foreach from=$memberships item=m}
    <option value="{$m.membershipid}">{$m.membership|escape}</option>
{/foreach}
  </select>
  </td>
</tr>

</table>
  </td>
</tr>

<tr>
  <td colspan="2">
<input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />
</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_pconf_slot_modify_title|substitute:"counter":$wizard_data.step_counter:"slotname":$slot_data.slot_name content=$smarty.capture.dialog extra='width="100%"'}
