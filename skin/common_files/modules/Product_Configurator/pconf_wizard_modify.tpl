{*
$Id: pconf_wizard_modify.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.txt_pconf_wizard_modify_desc}
<br /><br />
<a name="list"></a>
{capture name=dialog}
<br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_pconf_modify_product_details href="product_modify.php?productid=`$product.productid`"}</div>

<form action="product_modify.php" method="post" name="stepslistform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="wizard" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="action" value="update" />

<table cellpadding="0" cellspacing="0" width="100%">

{if $wizards}
<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_pconf_wizard_steps}</td>
</tr>

<tr>
  <td colspan="2">
<table cellpadding="2" cellspacing="2" width="70%">
<tr class="TableHead">
  <th width="20"></th>
  <th width="20">{$lng.lbl_pos}</th>
  <th width="100%" align="left">{$lng.lbl_step}</th>
</tr>
{section name=wz loop=$wizards}
<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="posted_data[{$wizards[wz].stepid}][delete]" title="{$lng.lbl_pconf_step_del_hint|escape}" /></td>
  <td><input type="text" size="3" name="posted_data[{$wizards[wz].stepid}][orderby]" value="{$wizards[wz].orderby}" title="{$lng.lbl_pconf_step_pos_hint|escape}" /></td>
  <td>
  <a href="product_modify.php?productid={$product.productid}&amp;mode=pconf&amp;edit=wizard&amp;step={$wizards[wz].stepid}#list" title="{$lng.lbl_pconf_click_for_step_details_hint|escape}">{if $wizards[wz].stepid eq $step}<b>{/if}{$lng.lbl_step} {$wizards[wz].step_counter}: {$wizards[wz].step_name}{if $wizards[wz].stepid eq $step}</b>{/if}</a>
  </td>
</tr>
{/section}
<tr>
  <td>&nbsp;</td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td colspan="2">
{$lng.lbl_pconf_tick_delete_note}
<br /><br />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(document.stepslistform, new RegExp('^posted_data', 'gi'))) {ldelim}document.stepslistform.action.value='delete'; document.stepslistform.submit();{rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

<tr>
  <td><br />&nbsp;</td>
</tr>

{/if}

<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_pconf_step_add_new}</td>
</tr>

<tr>
  <td colspan="2">
{$lng.lbl_pconf_step_name}:
<input type="text" size="35" name="new_step" title="{$lng.lbl_pconf_step_name_hint|escape}" />
  </td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox">
  <input type="button" value="{$lng.lbl_pconf_add_step|strip_tags:false|escape}" onclick="javascript: document.stepslistform.step.value = ''; document.stepslistform.submit();"/>
  </td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_pconf_confwiz_management content=$smarty.capture.dialog extra='width="100%"'}

{if $wizard_data}
<br />
<a name="step"></a>
{capture name=dialog}

{include file="main/subheader.tpl" title="`$lng.lbl_step` `$wizard_data.step_counter`: `$wizard_data.step_name`"}
{include file="main/language_selector.tpl" script="product_modify.php?`$smarty.server.QUERY_STRING`&amp;" anchor="step"}<br />

<form action="product_modify.php" method="post" name="stepform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="wizard" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="action" value="update_step" />

<table cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td>{$lng.lbl_pconf_step_name}:</td>
  <td><input type="text" size="35" name="posted_data[step_name]" value="{$wizard_data.step_name|escape}" title="{$lng.lbl_pconf_step_name_hint|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_pconf_step_description}:</td>
  <td><textarea cols="35" rows="5" name="posted_data[step_descr]" title="{$lng.lbl_pconf_step_description_hint|escape}">{$wizard_data.step_descr}</textarea></td>
</tr>

<tr>
  <td>{$lng.lbl_orderby}:</td>
  <td><input type="text" size="5" name="posted_data[orderby]" value="{$wizard_data.orderby}" title="{$lng.lbl_pconf_step_pos_hint|escape}" /></td>
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

{if $wizard_data.slots}

<form action="product_modify.php" method="post" name="modifyslotsform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="wizard" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="action" value="update_slots" />

{include file="main/subheader.tpl" title=$lng.lbl_pconf_slots}
<table cellpadding="2" cellspacing="2" width="70%">
<tr class="TableHead">
  <th width="20"></th>
  <th width="20">{$lng.lbl_pos}</th>
  <th width="100%" align="left">{$lng.lbl_pconf_slot_name}</th>
  <th width="20">{$lng.lbl_status}</th>
</tr>

{section name=slt loop=$wizard_data.slots}
<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="posted_data[{$wizard_data.slots[slt].slotid}][delete]" title="{$lng.lbl_pconf_slot_del_hint|escape}" /></td>
  <td><input type="text" size="3" maxlength="5" name="posted_data[{$wizard_data.slots[slt].slotid}][orderby]" value="{$wizard_data.slots[slt].orderby}" title="{$lng.lbl_pconf_slot_orderby_hint|escape}" /></td>
  <td><a href="product_modify.php?productid={$product.productid}&amp;mode=pconf&amp;edit=slot&amp;slot={$wizard_data.slots[slt].slotid}" title="{$lng.lbl_pconf_click_for_slot_details_hint|escape}">{$wizard_data.slots[slt].slot_name}</a></td>
  <td>
  <select name="posted_data[{$wizard_data.slots[slt].slotid}][status]">
    <option value="O"{if $wizard_data.slots[slt].status eq "O"} selected="selected"{/if}>{$lng.lbl_pconf_slot_st_optional}</option>
    <option value="M"{if $wizard_data.slots[slt].status eq "M"} selected="selected"{/if}>{$lng.lbl_pconf_slot_st_mandatory}</option>
    <option value="N"{if $wizard_data.slots[slt].status eq "N"} selected="selected"{/if}>{$lng.lbl_pconf_slot_st_disabled}</option>
  </select>
  </td>
</tr>
{/section}
</table>

<br />
{$lng.lbl_pconf_click_for_slot_details_note}
<br /><br />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^posted_data', 'gi'))) {ldelim} this.form.action.value = 'delete_slots'; this.form.submit();{rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
{/if}

<br />
<br />
<br />

<form action="product_modify.php" method="post" name="addslotform">
<input type="hidden" name="mode" value="pconf" />
<input type="hidden" name="edit" value="wizard" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="step" value="{$step}" />
<input type="hidden" name="action" value="add_slot" />

{include file="main/subheader.tpl" title=$lng.lbl_pconf_add_new_slot}

{$lng.lbl_pconf_slot_name}:
<input type="text" size="35" name="new_slot" title="{$lng.lbl_pconf_slot_name_hint|escape}" /><br />
<br />
<input type="submit" value="{$lng.lbl_pconf_add_slot|strip_tags:false|escape}" />

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_pconf_wizard_modify_title|substitute:"counter":$wizard_data.step_counter content=$smarty.capture.dialog extra='width="100%"'}
{/if}
