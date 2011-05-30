{*
$Id: tax_edit.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/tax_formula_js.tpl"}
{include file="page_title.tpl" title=$lng.lbl_tax_details}

{$lng.txt_taxes_general_note}

<br /><br />

{$lng.txt_tax_details_general_note}

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_taxes_list href="taxes.php"}</div>

<br />

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
function ChangeDisplayAlsoStatus() {ldelim}
  document.taxdetailsform.display_info.disabled = !document.taxdetailsform.display_including_tax.checked;
{rdelim}
//]]>
</script>

<form action="taxes.php" method="post" name="taxdetailsform" onsubmit="javascript: return checkFormula('tax_formula');">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="taxid" value="{$tax_details.taxid}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="20%" class="FormButton">{$lng.lbl_tax_service_name}:</td>
  <td width="10"><font class="Star">*</font></td>
  <td width="80%"><input type="text" size="15" maxlength="10" name="tax_service_name" value="{$tax_details.tax_name|escape}" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_display_name}:</td>
  <td>&nbsp;</td>
  <td><input type="text" size="45" name="tax_display_name" value="{$tax_details.tax_display_name|escape}" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_regnumber}:</td>
  <td>&nbsp;</td>
  <td><input type="text" size="32" maxlength="32" name="tax_regnumber" value="{$tax_details.regnumber|escape}" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_priority}:</td>
  <td>&nbsp;</td>
  <td><input type="text" size="10" name="tax_priority" value="{$tax_details.priority|escape}" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_status}:</td>
  <td><font class="Star">*</font></td>
  <td>
  <select name="active">
    <option value="Y"{if $tax_details.active eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
    <option value="N"{if $tax_details.active eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
  </select>
  </td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_apply_to}:</td>
  <td><font class="Star">*</font></td>
  <td class="TableSubHead">{include file="main/tax_formula.tpl" name="tax_formula" value=$tax_details.formula}</td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_tax_rates_depended_on}:</td>
  <td><font class="Star">*</font></td>
  <td>
  <select name="address_type">
    <option value="S"{if $tax_details.address_type eq "S"} selected="selected"{/if}>{$lng.lbl_shipping_address}</option>
    <option value="B"{if $tax_details.address_type eq "B"} selected="selected"{/if}>{$lng.lbl_billing_address}</option>
  </select>
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td>

<table cellpadding="1" cellspacing="1">
<tr>
  <td><input type="checkbox" id="price_includes_tax" name="price_includes_tax" value="Y"{if $tax_details.price_includes_tax eq "Y"} checked="checked"{/if} /></td>
  <td class="FormButton"><label for="price_includes_tax">{$lng.lbl_price_includes_tax}</label></td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td>

<table cellpadding="1" cellspacing="1">
<tr>
  <td><input type="checkbox" id="display_including_tax" name="display_including_tax" value="Y" onclick="javascript: ChangeDisplayAlsoStatus();"{if $tax_details.display_including_tax eq "Y"} checked="checked"{/if} /></td>
  <td class="FormButton"><label for="display_including_tax">{$lng.lbl_display_including_tax}</label></td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td>

<table cellpadding="1" cellspacing="1">
<tr>
  <td class="FormButton">{$lng.lbl_display_also}:&nbsp;</td>
</tr>
<tr>
  <td>
  <select name="display_info"{if $tax_details.display_including_tax ne "Y"} disabled="disabled"{/if}>
    <option value="">{$lng.lbl_display_tax_none}</option>
    <option value="R"{if $tax_details.display_info eq "R"} selected="selected"{/if}>{$lng.lbl_display_tax_rate}</option>
    <option value="V"{if $tax_details.display_info eq "V"} selected="selected"{/if}>{$lng.lbl_display_tax_cost}</option>
    <option value="A"{if $tax_details.display_info eq "A"} selected="selected"{/if}>{$lng.lbl_display_tax_rate_and_cost}</option>
  </select>
  </td>
</tr>
</table>

  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td class="SubmitBox"><input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_tax_details content=$smarty.capture.dialog extra='width="100%"'}

{if $active_modules.Simple_Mode ne "" and $tax_details.taxid ne ""}

<br /><br />

{include file="provider/main/tax_rates.tpl"}

{/if}

