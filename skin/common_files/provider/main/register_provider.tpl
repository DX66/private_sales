{*
$Id: register_provider.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{capture name=dialog}

{$lng.txt_seller_address_note}

<br />
<br />

{assign var="reg_error" value=$top_message.reg_error}

{if $config.Shipping.allow_change_seller_address ne "Y" and $main ne "user_profile"}

<table cellspacing="1" cellpadding="2" width="100%">

<tr>
<td align="right" nowrap="nowrap" width="40%">{$lng.lbl_address}:</td>
<td nowrap="nowrap">
{$userinfo.seller_address|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_address_2}:</td>
<td nowrap="nowrap">
{$userinfo.seller_address_2|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_city}:</td>
<td nowrap="nowrap">
{$userinfo.seller_city|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_state}:</td>
<td nowrap="nowrap">
{$userinfo.seller_statename|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_country}:</td>
<td nowrap="nowrap">
{$userinfo.seller_countryname|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_zip_code}:</td>
<td nowrap="nowrap">
{$userinfo.seller_zipcode|escape}
</td>
</tr>

{if $userinfo.need_arb_info eq "Y"}

<tr>
<td colspan="2" align="center">{include file="main/subheader.tpl" title=$lng.lbl_arb_provider_section}</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_id}:</td>
<td nowrap="nowrap">
{$userinfo.seller_arb_id|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_password}:</td>
<td nowrap="nowrap">
{$userinfo.seller_arb_password|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_account}:</td>
<td nowrap="nowrap">
{$userinfo.seller_arb_account|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_shipping_key}:</td>
<td nowrap="nowrap">
{$userinfo.seller_arb_shipping_key|escape}
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_shipping_key_intl}:</td>
<td nowrap="nowrap">
{$userinfo.seller_arb_shipping_key_intl|escape}
</td>
</tr>

{/if}

</table>

{else}

<form action="{$register_script_name}?{$smarty.server.QUERY_STRING|amp}" method="post" name="registerform" onsubmit="javascript: if (check_zip_code() && checkRequired(requiredFields)) return true; else return false;">
{if $config.Security.use_https_login eq "Y"}
<input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
{/if}

{if $smarty.get.mode eq "update"}
<input type="hidden" name="mode" value="update" />
{/if}
<input type="hidden" name="submode" value="seller_address" />

<table cellspacing="1" cellpadding="2" width="100%">

<tr>
<td align="right" width="40%">{$lng.lbl_address}</td>
<td width="1">{if $default_fields.address.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="address" name="address" size="32" maxlength="64" value="{$userinfo.seller_address|escape}" />
{if $reg_error ne "" and $default_fields.address.required eq 'Y' and $userinfo.seller_address eq ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_address_2}</td>
<td>{if $default_fields.address.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="address_2" name="address_2" size="32" maxlength="64" value="{$userinfo.seller_address_2|escape}" />
{if $reg_error ne "" and $default_fields.address_2.required eq 'Y' and $userinfo.seller_address_2 eq ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_city}</td>
<td>{if $default_fields.city.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="city" name="city" size="32" maxlength="64" value="{$userinfo.seller_city|escape}" />
{if $reg_error ne "" and $default_fields.city.required eq 'Y' and $userinfo.seller_city eq ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_state}</td>
<td>{if $default_fields.state.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
{include file="main/states.tpl" states=$states name="state" default=$userinfo.seller_state|default:$config.General.default_state default_country=$userinfo.seller_country|default:$config.General.default_country country_name="country"}
{if ($reg_error ne "" and $default_fields.state.required eq 'Y' and $userinfo.seller_state eq "" and $userinfo.s_display_states) or $error eq "statecode"}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_country}</td>
<td>{if $default_fields.country.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<select name="country" id="country" size="1" onchange="check_zip_code()">
{section name=country_idx loop=$countries}
<option value="{$countries[country_idx].country_code}"{if $userinfo.seller_country eq $countries[country_idx].country_code} selected="selected"{elseif $countries[country_idx].country_code eq $config.General.default_country and $userinfo.seller_country eq ""} selected="selected"{/if}>{$countries[country_idx].country|amp}</option>
{/section}
</select>
{if $reg_error ne "" and $default_fields.country.required eq 'Y' and $userinfo.seller_country eq ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr style="display: none;">
  <td>
  {include file="main/register_states.tpl" state_name="state" country_name="country" county_name="county" state_value=$userinfo.seller_state|default:$config.General.default_state county_value=$userinfo.seller_county}
   </td>
</tr>

<tr>
<td align="right">{$lng.lbl_zip_code}</td>
<td>{if $default_fields.zipcode.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="zipcode" name="zipcode" size="32" maxlength="32" value="{$userinfo.seller_zipcode|escape}" onchange="check_zip_code()" />
{if $reg_error ne "" and $default_fields.zipcode.required eq 'Y' and $userinfo.seller_zipcode eq ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr>
<td colspan="3">
{$lng.lbl_company_location_country_provider_note|substitute:"X":$config.Company.location_country_name}
{if $userinfo.seller_country ne $config.Company.location_country and $userinfo.seller_country ne ""}<br />
<font class="Star">
{$lng.lbl_company_location_country_provider_warning|substitute:"X":$config.Company.location_country_name}
</font>
{/if}
</td>
</tr>

{if $userinfo.need_arb_info eq "Y"}

<tr>
<td colspan="3" align="center">{include file="main/subheader.tpl" title=$lng.lbl_arb_provider_section}</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_id}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" name="arb_id" value="{$userinfo.seller_arb_id|escape}" />
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_password}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" name="arb_password" value="{$userinfo.seller_arb_password|escape}" />
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_account}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" name="arb_account" value="{$userinfo.seller_arb_account|escape}" />
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_shipping_key}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" name="arb_shipping_key" value="{$userinfo.seller_arb_shipping_key|escape}" />
</td>
</tr>

<tr>
<td align="right">{$lng.opt_ARB_shipping_key_intl}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" name="arb_shipping_key_intl" value="{$userinfo.seller_arb_shipping_key_intl|escape}" />
</td>
</tr>

<tr>
<td colspan="3" align="left"><b>{$lng.lbl_note}:</b> {$lng.lbl_arb_provider_note}</td>
</tr>

{/if}

<tr>
<td colspan="2">&nbsp;</td>
<td><br /><input type="submit" value=" {$lng.lbl_save} " /></td>
</tr>

</table>

</form>
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_seller_address content=$smarty.capture.dialog extra='width="100%"'}
