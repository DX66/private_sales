{*
$Id: tax_edit.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/tax_formula_js.tpl"}
{include file="page_title.tpl" title=$lng.lbl_tax_details}

{$lng.txt_tax_rates_general_note}

<br /><br />

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="70%">
<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="35%" class="FormButton">{$lng.lbl_tax_service_name}:</td>
  <td width="65%">{$tax_details.tax_name}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_display_name}:</td>
  <td>{$tax_details.tax_display_name}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_status}:</td>
  <td>{if $tax_details.active eq "Y"}{$lng.lbl_enabled}{else}{$lng.lbl_disabled}{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_apply_to}:</td>
  <td>{$tax_details.formula}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_tax_rates_depended_on}:</td>
  <td>{if $tax_details.address_type eq "S"}{$lng.lbl_shipping_address}{else}{$lng.lbl_billing_address}{/if}</td>
</tr>

{if $tax_details.price_includes_tax eq "Y"}
<tr>
  <td colspan="2">{$lng.txt_product_prices_include_tax|substitute:"tax_name":$tax_details.tax_name}</td>
</tr>
{/if}

</table>

  </td>
  <td align="right" valign="top">{include file="buttons/button.tpl" button_title=$lng.lbl_taxes_list href="taxes.php"}</td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_tax_details content=$smarty.capture.dialog extra='width="100%"'}

{if $tax_details.taxid ne ""}

<br /><br />

{include file="provider/main/tax_rates.tpl"}

{/if}

