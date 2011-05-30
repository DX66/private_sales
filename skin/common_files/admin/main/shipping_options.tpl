{*
$Id: shipping_options.tpl,v 1.1.2.3 2011/04/25 08:42:23 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_shipping_options}

<br />

{$lng.txt_shipping_options_top_text}

<br /><br />

{include file="check_email_script.tpl"}
{include file="check_zipcode_js.tpl"}

{$lng.lbl_select_service}:
{section name=carrier loop=$carriers}
{if $carriers[carrier].0 eq $carrier}
<b>{$carriers[carrier].1}</b>
{else}
<a href="shipping_options.php?carrier={$carriers[carrier].0}">{$carriers[carrier].1}</a>
{/if}
{if not %carrier.last%}&nbsp;::&nbsp;{/if}
{/section}

<br /><br />

{if $carrier eq "FDX"}

{capture name=dialog}

<div align="right"><a href="shipping.php?carrier=FDX#rt">{$lng.lbl_X_shipping_methods|substitute:"service":"FedEx"}</a></div>

<br />

{if $config.Shipping.FEDEX_account_number ne ''}

{if $config.FEDEX_meter_number eq ""}

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="FDX" />

{$lng.txt_fedex_get_meter_number_note}

<br />
<br />

<table cellpadding="3" cellspacing="1">

<tr>
  <td width="30%" class="FormButton">{$lng.lbl_fedex_person_name}:</td>
  <td width="10" class="Star">*</td>
  <td><input type="text" size="35" maxlength="35" name="posted_data[person_name]" value="{$prepared_user_data.person_name|escape}" /></td>
  <td width="20" class="Star">{if $fill_error ne "" and $prepared_user_data.person_name eq ""}&lt;&lt;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_company_name}:</td>
  <td></td>
  <td><input type="text" size="35" maxlength="35" name="posted_data[company_name]" value="{$prepared_user_data.company_name|escape}" /></td>
  <td></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_phone}:</td>
  <td class="Star">*</td>
  <td><input type="text" size="35" maxlength="16" name="posted_data[phone_number]" value="{$prepared_user_data.phone_number|escape}" /></td>
  <td class="Star">{if $fill_error ne "" and $prepared_user_data.phone_number eq ""}&lt;&lt;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_pager_number}:</td>
  <td></td>
  <td><input type="text" size="35" maxlength="16" name="posted_data[pager_number]" value="{$prepared_user_data.pager_number|escape}" /></td>
  <td></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_fax}:</td>
  <td></td>
  <td><input type="text" size="35" maxlength="16" name="posted_data[fax_number]" value="{$prepared_user_data.fax_number|escape}" /></td>
  <td></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_email}:</td>
  <td></td>
  <td><input type="text" size="35" maxlength="120" name="posted_data[email]" value="{$prepared_user_data.email|escape}" onchange="javascript: checkEmailAddress(this);" /></td>
  <td></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_address}:</td>
  <td class="Star">*</td>
  <td><input type="text" size="35" maxlength="35" name="posted_data[address_1]" value="{$prepared_user_data.address_1|escape}" /></td>
  <td class="Star">{if $fill_error ne "" and $prepared_user_data.address_1 eq ""}&lt;&lt;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_address_2}:</td>
  <td></td>
  <td><input type="text" size="35" maxlength="35" name="posted_data[address_2]" value="{$prepared_user_data.address_2|escape}" /></td>
  <td></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_city}:</td>
  <td class="Star">*</td>
  <td><input type="text" size="35" maxlength="35" name="posted_data[city]" value="{$prepared_user_data.city|escape}" /></td>
  <td class="Star">{if $fill_error ne "" and $prepared_user_data.city eq ""}&lt;&lt;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_state}:</td>
  <td class="Star">*</td>
  <td>{include file="main/states.tpl" states=$states name="posted_data[state]" default=$prepared_user_data.state default_country=$prepared_user_data.country country_name="posted_data[country]"}</td>
  <td class="Star">{if $fill_error ne "" and $prepared_user_data.state eq ""}&lt;&lt;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_zipcode}:</td>
  <td class="Star">*</td>
  <td><input type="text" size="35" maxlength="16" name="posted_data[zipcode]" value="{$prepared_user_data.zipcode|escape}" onchange="javascript: check_zip_code_field(this.form['posted_data[country]'], this);" /></td>
  <td class="Star">{if $fill_error ne "" and $prepared_user_data.zipcode eq ""}&lt;&lt;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_fedex_country}:</td>
  <td class="Star">*</td>
  <td>
  <select name="posted_data[country]" id="posted_data_country" onchange="javascript: check_zip_code_field(this, this.form['posted_data[zipcode]']);">
  {section name=country_idx loop=$countries}
  <option value="{$countries[country_idx].country_code}"{if $prepared_user_data.country eq $countries[country_idx].country_code} selected="selected"{elseif $countries[country_idx].country_code eq $config.General.default_country and $prepared_user_data.country eq ""} selected="selected"{/if}>{$countries[country_idx].country|amp}</option>
  {/section}
  </select>
  </td>
  <td class="Star">{if $fill_error ne "" and $prepared_user_data.country eq ""}&lt;&lt;{/if}</td>
</tr>

</table>

<br />
<br />

<input type="submit" value="{$lng.lbl_fedex_get_meter_number|escape}" name="get_meter_number" onclick="javascript: checkEmailAddress(this.form['posted_data[email]']);" />

</form>

{include file="change_states_js.tpl"}
{include file="main/register_states.tpl" state_name="posted_data[state]" country_name="posted_data[country]" country_id="posted_data_country" state_value=$prepared_user_data.state}

{else}

{$lng.txt_fedex_clear_meter_number_note}

<br />
<br />

<b>{$lng.lbl_fedex_meter_number}:</b> {$config.FEDEX_meter_number|default:"n/a"}

<br />
<br />

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="FDX" />

<input type="submit" value="{$lng.lbl_fedex_clear_meter_number|escape}" name="clear_meter_number" />

</form>

<br />
<br />

{$lng.txt_fedex_options_note}

<br />
<br />

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="FDX" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="30%"><b>{$lng.lbl_fedex_carrier_type}:</b></td>
  <td width="70%">
    <select name="carrier_codes[]" multiple="multiple">
      <option value="FDXE"{if $shipping_options.fdx.carrier_codes.FDXE} selected="selected"{/if}>FedEx Express</option>
      <option value="FDXG"{if $shipping_options.fdx.carrier_codes.FDXG} selected="selected"{/if}>FedEx Ground</option>
      <option value="FXSP"{if $shipping_options.fdx.carrier_codes.FXSP} selected="selected"{/if}>FedEx SmartPost</option>
    </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_packaging}:</b></td>
  <td>
  <select name="packaging">
    <option value="FEDEX_ENVELOPE"{if $shipping_options.fdx.packaging eq "FEDEX_ENVELOPE"} selected="selected"{/if}>FedEx Envelope</option>
    <option value="FEDEX_PAK"{if $shipping_options.fdx.packaging eq "FEDEX_PAK"} selected="selected"{/if}>FedEx Pak</option>
    <option value="FEDEX_BOX"{if $shipping_options.fdx.packaging eq "FEDEX_BOX"} selected="selected"{/if}>FedEx Box</option>
    <option value="FEDEX_TUBE"{if $shipping_options.fdx.packaging eq "FEDEX_TUBE"} selected="selected"{/if}>FedEx Tube</option>
    <option value="FEDEX_10KG_BOX"{if $shipping_options.fdx.packaging eq "FEDEX_10KG_BOX"} selected="selected"{/if}>FedEx 10Kg Box</option>
    <option value="FEDEX_25KG_BOX"{if $shipping_options.fdx.packaging eq "FEDEX_25KG_BOX"} selected="selected"{/if}>FedEx 25Kg Box</option>
    <option value="YOUR_PACKAGING"{if $shipping_options.fdx.packaging eq "YOUR_PACKAGING"} selected="selected"{/if}>My packaging</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_fedex_dropoff_type}:</b></td>
  <td>
  <select name="dropoff_type">
    <option value="REGULAR_PICKUP"{if $shipping_options.fdx.dropoff_type eq "REGULAR_PICKUP"} selected="selected"{/if}>Regular pickup</option>
    <option value="REQUEST_COURIER"{if $shipping_options.fdx.dropoff_type eq "REQUEST_COURIER"} selected="selected"{/if}>Request courier</option>
    <option value="DROP_BOX"{if $shipping_options.fdx.dropoff_type eq "DROP_BOX"} selected="selected"{/if}>Drop box</option>
    <option value="BUSINESS_SERVICE_CENTER"{if $shipping_options.fdx.dropoff_type eq "BUSINESS_SERVICE_CENTER"} selected="selected"{/if}>Business Service Center</option>
    <option value="STATION"{if $shipping_options.fdx.dropoff_type eq "STATION"} selected="selected"{/if}>Station</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_fedex_ship_date}:</b></td>
  <td>
  <select name="ship_date">
    {section name=num loop=11 start=0}
    <option value="{$smarty.section.num.index}"{if $smarty.section.num.index eq $shipping_options.fdx.ship_date} selected="selected"{/if}>{$smarty.section.num.index}</option>
    {/section}
  </select>
  </td>
</tr>

<tr>
    <td><b>{$lng.lbl_fedex_currency}:</b></td>
    <td>
        <select name="currency_code">
      <option value="USD"{if $shipping_options.fdx.currency_code eq "USD" or $shipping_options.fdx.currency_code eq ""} selected="selected"{/if}>USD</option>
      <option value="CAD"{if $shipping_options.fdx.currency_code eq "CAD"} selected="selected"{/if}>CAD</option>
        </select>
    </td>
</tr>

<tr>
    <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_fedex_package_limits class="grey"}</td>
</tr>

<tr>
    <td colspan="2">{$lng.txt_fedex_limits_note}</td>
</tr>

<tr>
  <td>
    <b>{$lng.lbl_maximum_package_weight} ({$config.General.weight_symbol}):</b>
  </td>
  <td nowrap="nowrap">
    <input type="text" name="max_weight" value="{$shipping_options.fdx.max_weight|doubleval}" size="7" />
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_dimensions} ({$config.General.dimensions_symbol}):</b></td>
  <td nowrap="nowrap">
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="dim_length" value="{$shipping_options.fdx.dim_length}" size="6" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="dim_width" value="{$shipping_options.fdx.dim_width}" size="6" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="dim_height" value="{$shipping_options.fdx.dim_height}" size="6" /></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><label for="param01"><b>{$lng.lbl_fedex_pkg_no_use}:</b></label></td>
  <td><input type="checkbox" name="param01" id="param01" value="Y"{if $shipping_options.fdx.param01 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td><label for="param02"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="param02" id="param02" value="Y"{if $shipping_options.fdx.param02 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
    <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_fedex_cod class="grey"}</td>
</tr>

<tr>
    <td><b>{$lng.lbl_fedex_cod_value} ({$shipping_options.fdx.currency_code|default:"USD"}):</b></td>
    <td>
        <input type="text" name="cod_value" value="{$shipping_options.fdx.cod_value|default:"0.00"}" />
    </td>
</tr>

<tr>
    <td><b>{$lng.lbl_fedex_cod_type}:</b></td>
    <td>
        <select name="cod_type">
      <option value="ANY"{if $shipping_options.fdx.cod_type eq "ANY"} selected="selected"{/if}>{$lng.lbl_fedex_any}</option>
      <option value="GUARANTEED_FUNDS"{if $shipping_options.fdx.cod_type eq "GUARANTEED_FUNDS"} selected="selected"{/if}>{$lng.lbl_fedex_guaranteed_funds}</option>
      <option value="CASH"{if $shipping_options.fdx.cod_type eq "CASH"} selected="selected"{/if}>{$lng.lbl_fedex_cash}</option>
        </select>
    </td>
</tr>

<tr>
    <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_fedex_special_services class="grey"}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_fedex_dangerous_goods}:</b></td>
  <td>
  <select name="dg_accessibility">
    <option value=""{if $shipping_options.fdx.dg_accessibility eq ""} selected="selected"{/if}>&nbsp;</option>
    <option value="ACCESSIBLE"{if $shipping_options.fdx.dg_accessibility eq "ACCESSIBLE"} selected="selected"{/if}>{$lng.lbl_fedex_accessible}</option>
    <option value="INACCESSIBLE"{if $shipping_options.fdx.dg_accessibility eq "INACCESSIBLE"} selected="selected"{/if}>{$lng.lbl_fedex_inaccessible}</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_fedex_signature_option}:</b></td>
  <td>
  <select name="signature">
    <option value=""{if $shipping_options.fdx.signature eq ""} selected="selected"{/if}>&nbsp;</option>
    <option value="NO_SIGNATURE_REQUIRED"{if $shipping_options.fdx.signature eq "NO_SIGNATURE_REQUIRED"} selected="selected"{/if}>{$lng.lbl_fedex_no_signature}</option>
    <option value="INDIRECT"{if $shipping_options.fdx.signature eq "INDIRECT"} selected="selected"{/if}>{$lng.lbl_fedex_signature_indirect}</option>
    <option value="DIRECT"{if $shipping_options.fdx.signature eq "DIRECT"} selected="selected"{/if}>{$lng.lbl_fedex_signature_direct}</option>
    <option value="ADULT"{if $shipping_options.fdx.signature eq "ADULT"} selected="selected"{/if}>{$lng.lbl_fedex_signature_adult}</option>
  </select>
  </td>
</tr>

<tr>
  <td colspan="2">

  <table cellpadding="3" cellspacing="1">

  <tr>
    <td width="10"><input type="checkbox" name="dry_ice" id="dry_ice" value="Y"{if $shipping_options.fdx.dry_ice eq "Y"} checked="checked"{/if} /></td>
    <td width="50%"><b><label for="dry_ice">{$lng.lbl_fedex_dry_ice}</label></b></td>
    <td width="20">&nbsp;</td>
    <td width="10"><input type="checkbox" name="hold_at_location" id="hold_at_location" value="Y"{if $shipping_options.fdx.hold_at_location eq "Y"} checked="checked"{/if} /></td>
    <td width="50%"><b><label for="hold_at_location">{$lng.lbl_fedex_hold_at_location}</label></b></td>
  </tr>

  <tr>
    <td><input type="checkbox" name="inside_pickup" id="inside_pickup" value="Y"{if $shipping_options.fdx.inside_pickup eq "Y"} checked="checked"{/if} /></td>
    <td><b><label for="inside_pickup">{$lng.lbl_fedex_inside_pickup}</label></b></td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="inside_delivery" id="inside_delivery" value="Y"{if $shipping_options.fdx.inside_delivery eq "Y"} checked="checked"{/if} /></td>
    <td><b><label for="inside_delivery">{$lng.lbl_fedex_inside_delivery}</label></b></td>
  </tr>

  <tr>
    <td><input type="checkbox" name="saturday_pickup" id="saturday_pickup" value="Y"{if $shipping_options.fdx.saturday_pickup eq "Y"} checked="checked"{/if} /></td>
    <td><b><label for="saturday_pickup">{$lng.lbl_fedex_saturday_pickup}</label></b></td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="saturday_delivery" id="saturday_delivery" value="Y"{if $shipping_options.fdx.saturday_delivery eq "Y"} checked="checked"{/if} /></td>
    <td><b><label for="saturday_delivery">{$lng.lbl_fedex_saturday_delivery}</label></b></td>
  </tr>

  <tr>
    <td valign="top"><input type="checkbox" name="residential_delivery" id="residential_delivery" value="Y"{if $shipping_options.fdx.residential_delivery eq "Y"} checked="checked"{/if} /></td>
    <td><b><label for="residential_delivery">{$lng.lbl_fedex_residential_delivery}</label></b>
    <br />
    {$lng.lbl_fedex_residential_delivery_note}
    </td>
    <td>&nbsp;</td>
    <td valign="top"><input type="checkbox" name="nonstandard_container" id="nonstandard_container" value="Y"{if $shipping_options.fdx.nonstandard_container eq "Y"} checked="checked"{/if} /></td>
    <td valign="top"><b><label for="nonstandard_container">{$lng.lbl_fedex_nonstandard_container}</label></b></td>
  </tr>

  </table>

  </td>
</tr>

<tr>
    <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_fedex_handling class="grey"}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_fedex_handling_amount}:</b></td>
  <td>
  <input type="text" size="10" maxlength="10" name="handling_charges_amount" value="{$shipping_options.fdx.handling_charges_amount|default:"0.00"}" />
  {assign var="fdx_currency_code" value=$shipping_options.fdx.currency_code|default:"USD"}
  <select name="handling_charges_type">
    <option value="FIXED_AMOUNT"{if $shipping_options.fdx.handling_charges_type eq "FIXED_AMOUNT"} selected="selected"{/if}>{$fdx_currency_code}</option>
    <option value="PERCENTAGE_OF_NET_FREIGHT"{if $shipping_options.fdx.handling_charges_type eq "PERCENTAGE_OF_NET_FREIGHT"} selected="selected"{/if}>% of base</option>
    <option value="PERCENTAGE_OF_NET_CHARGE"{if $shipping_options.fdx.handling_charges_type eq "PERCENTAGE_OF_NET_CHARGE"} selected="selected"{/if}>% of net</option>
    <option value="PERCENTAGE_OF_NET_CHARGE_EXCLUDING_TAXES"{if $shipping_options.fdx.handling_charges_type eq "PERCENTAGE_OF_NET_CHARGE_EXCLUDING_TAXES"} selected="selected"{/if}>% of net (excluding taxes)</option>
  </select>

  {include file="main/tooltip_js.tpl" text=$lng.txt_fedex_help_charges_type|substitute:"currency_code":$fdx_currency_code type="img"}
  </td>
</tr>

</table>

<br />
<br />

<input type="submit" value="{$lng.lbl_apply|escape}" name="update_options" />

</form>
{/if}

{else}

{$lng.txt_fedex_disabled_note}

<br />
<br />

{/if}

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"FedEx"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "USPS"}

{capture name=dialog}

<div align="right"><a href="shipping.php?carrier=USPS#rt">{$lng.lbl_X_shipping_methods|substitute:"service":"U.S.P.S."}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="USPS" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td colspan="2"><h3>{$lng.lbl_international_usps}</h3></td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_type_of_mail}:</b></td>
  <td>
  <select name="mailtype">
    <option value="Package"{if $shipping_options.usps.param00 eq "Package"} selected="selected"{/if}>Package</option>
    <option value="Postcards or Aerogrammes"{if $shipping_options.usps.param00 eq "Postcards or Aerogrammes"} selected="selected"{/if}>Postcards or Aerogrammes</option>
    <option value="Envelope"{if $shipping_options.usps.param00 eq "Envelope"} selected="selected"{/if}>Envelope</option>
  </select>
  </td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_usps_value_of_content}:</b></td>
  <td>
  <input type="text" name="value_of_content" size="10" value="{$shipping_options.usps.param07|escape}" />
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_usps_container3}:</b></td>
  <td>
  <select name="container_intl">
    <option value="RECTANGULAR"{if $shipping_options.usps.param10 eq "RECTANGULAR"} selected="selected"{/if}>Rectangular</option>
    <option value="NONRECTANGULAR"{if $shipping_options.usps.param10 eq "NONRECTANGULAR"} selected="selected"{/if}>Non Rectangular</option>
  </select>
  </td>
</tr>

<tr>
  <td colspan="2"><hr /></td>
</tr>

<tr>
  <td colspan="2"><h3>{$lng.lbl_domestic_usps}</h3></td>
</tr>

<tr>
  <td><b>{$lng.lbl_package_size} {$lng.lbl_package_size_note}:</b></td>
  <td>
  <select name="package_size">
    <option value="REGULAR"{if $shipping_options.usps.param01 eq "REGULAR"} selected="selected"{/if}>Regular (Package dimensions are 12 or less)</option>
    <option value="LARGE"{if $shipping_options.usps.param01 eq "LARGE"} selected="selected"{/if}>Large (Any package dimension is larger than 12)</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_machinable}:</b></td>
  <td>
  <select name="machinable">
    <option value="FALSE"{if $shipping_options.usps.param02 eq "FALSE"} selected="selected"{/if}>{$lng.lbl_no}</option>
    <option value="TRUE"{if $shipping_options.usps.param02 eq "TRUE"} selected="selected"{/if}>{$lng.lbl_yes}</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_usps_container}:</b></td>
  <td>
  <select name="container_express">
    <option>{$lng.lbl_none}</option>
    <option value="Flat Rate Envelope"{if $shipping_options.usps.param03 eq "Flat Rate Envelope"} selected="selected"{/if}>Express Mail Flat Rate Envelope, 12.5 x 9.5</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_usps_container2}:</b></td>
  <td>
  <select name="container_priority">
    <option>{$lng.lbl_none}</option>
    <option value="Flat Rate Envelope"{if $shipping_options.usps.param04 eq "Flat Rate Envelope"} selected="selected"{/if}>Priority Mail Flat Rate Envelope, 12.5 x 9.5</option>
    <option value="Flat Rate Box"{if $shipping_options.usps.param04 eq "Flat Rate Box"} selected="selected"{/if}>Priority Mail Flat Rate Box, 14" x 12" x 3.5", 11.25" x 8.75" x 6"</option>
    <option value="RECTANGULAR"{if $shipping_options.usps.param04 eq "RECTANGULAR"} selected="selected"{/if}>Rectangular (Priority Mail Large)</option>
    <option value="NONRECTANGULAR"{if $shipping_options.usps.param04 eq "NONRECTANGULAR"} selected="selected"{/if}>Non Rectangular (Priority Mail Large)</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_weight} ({$config.General.weight_symbol})*:</b></td>
  <td>
    <input type="text" name="max_weight" size="6" value="{$shipping_options.usps.param08|doubleval}"/>
   </td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_dimensions} ({$config.General.dimensions_symbol})*:</b></td>
  <td nowrap="nowrap">
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="dim_length" size="6" value="{$shipping_options.usps.dim_length|doubleval}"/></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="dim_width" size="6" value="{$shipping_options.usps.dim_width|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="dim_height" size="6" value="{$shipping_options.usps.dim_height|doubleval}"/></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><label for="use_maximum_dimensions"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="use_maximum_dimensions" id="use_maximum_dimensions" value="Y"{if $shipping_options.usps.param09 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
  <font class="SmallText">{$lng.txt_shipping_cost_convertion_rate_us_dollars}</font>
  </td>
  <td valign="top"><input type="text" name="currency_rate" size="10" value="{$shipping_options.usps.currency_rate|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_usps_girth} ({$config.General.dimensions_symbol}):</b></td>
  <td nowrap="nowrap">
<input type="text" name="dim_girth" value="{$shipping_options.usps.dim_girth|escape}" size="7" />
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_usps_first_class_mail_type}:</b></td>
  <td>
  <select name="firstclassmailtype">
    <option value="LETTER"{if $shipping_options.usps.param05 eq "LETTER"} selected="selected"{/if}>Letter</option>
    <option value="FLAT"{if $shipping_options.usps.param05 eq "FLAT"} selected="selected"{/if}>Flat</option>
    <option value="PARCEL"{if $shipping_options.usps.param05 eq "PARCEL"} selected="selected"{/if}>Parcel</option>
    <option value="POSTCARD"{if $shipping_options.usps.param05 eq "POSTCARD"} selected="selected"{/if}>PostCard</option>
  </select>
  </td>
</tr>

<tr>
  <td colspan="2"><b>*</b> {$lng.txt_usps_limits_note}</td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"U.S.P.S."}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "Intershipper"}

{capture name=dialog}

<div align="right"><a href="shipping.php#rt">{$lng.lbl_manage_shipping_methods}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="Intershipper" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="40%"><b>{$lng.lbl_type_of_delivery}:</b></td>
  <td>
  <select name="delivery">
    <option value="COM"{if $shipping_options.intershipper.param00 eq "COM"} selected="selected"{/if}>Commercial delivery</option>
    <option value="RES"{if $shipping_options.intershipper.param00 eq "RES"} selected="selected"{/if}>Residential delivery</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_type_of_pickup}:</b></td>
  <td>
  <select name="shipmethod">
    <option value="DRP"{if $shipping_options.intershipper.param01 eq "DRP"} selected="selected" {/if}>Drop of at carrier location</option>
    <option value="SCD"{if $shipping_options.intershipper.param01 eq "SCD"} selected="selected" {/if}>Regularly Scheduled Pickup</option>
    <option value="PCK"{if $shipping_options.intershipper.param01 eq "PCK"} selected="selected" {/if}>Schedule A Special Pickup</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_package_type}:</b></td>
  <td>
  <select name="packaging">
    <option value="BOX"{if $shipping_options.intershipper.param06 eq "BOX"} selected="selected"{/if}>Customer-supplied Box</option>
    <option value="CBX"{if $shipping_options.intershipper.param06 eq "CBX"} selected="selected"{/if}>Carrier Box</option>
    <option value="CPK"{if $shipping_options.intershipper.param06 eq "CPK"} selected="selected"{/if}>Carrier Pak</option>
    <option value="ENV"{if $shipping_options.intershipper.param06 eq "ENV"} selected="selected"{/if}>Carrier Envelope</option>
    <option value="MEM"{if $shipping_options.intershipper.param06 eq "MEM"} selected="selected"{/if}>Media Mail</option>
    <option value="TUB"{if $shipping_options.intershipper.param06 eq "TUB"} selected="selected"{/if}>Carrier Tube</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_nature_of_shipment_contents}:</b></td>
  <td>
  <select name="contents">
    <option value="OTR"{if $shipping_options.intershipper.param07 eq "OTR"} selected="selected"{/if}>Other: Most shipments will use this code</option>
    <option value="LQD"{if $shipping_options.intershipper.param07 eq "LQD"} selected="selected"{/if}>Liquid</option>
    <option value="AHM"{if $shipping_options.intershipper.param07 eq "AHM"} selected="selected"{/if}>Accessible HazMat</option>
    <option value="IHM"{if $shipping_options.intershipper.param07 eq "IHM"} selected="selected"{/if}>Inaccessible HazMat</option>
  </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_package_cod_value}:</b></td>
  <td><input type="text" name="codvalue" size="10" value="{$shipping_options.intershipper.param08|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_optional_services}:</b></td>
  <td>
    <input type="checkbox" name="options[]" value="ADP" {if $shipping_options.intershipper.options.ADP ne ""} checked="checked" {/if}/>Additional Handling<br/>
    <input type="checkbox" name="options[]" value="SDP" {if $shipping_options.intershipper.options.SDP ne ""} checked="checked" {/if}/>Saturday Delivery <br/>
    <input type="checkbox" name="options[]" value="PDP" {if $shipping_options.intershipper.options.PDP ne ""} checked="checked" {/if}/>Proof of Delivery<br/>
  </td>
</tr>

<tr>
  <td>
    <b>{$lng.lbl_maximum_package_weight} ({$config.General.weight_symbol})*:</b>
  </td>
  <td>
    <input type="text" name="weight" size="6" value="{$shipping_options.intershipper.param09|doubleval}"/> ({$lng.lbl_should_not_exceed} {$max_intershipper_weight} {$config.General.weight_symbol})
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_dimensions} ({$config.General.dimensions_symbol})*:</b></td>
  <td>
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="length" size="6" value="{$shipping_options.intershipper.param02|doubleval}"/></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="width" size="6" value="{$shipping_options.intershipper.param03|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="height" size="6" value="{$shipping_options.intershipper.param04|doubleval}"/></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><label for="use_maximum_dimensions"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="use_maximum_dimensions" id="use_maximum_dimensions" value="Y"{if $shipping_options.intershipper.param10 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td colspan="2"><b>*</b> {$lng.txt_intershipper_limits_note}</td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"InterShipper"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "CPC"}

{capture name=dialog}

<div align="right"><a href="shipping.php?carrier=CPC#rt">{$lng.lbl_X_shipping_methods|substitute:"service":"Canada Post"}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="CPC" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="50%"><b>{$lng.lbl_item_description}:</b></td>
  <td><input type="text" name="descr" size="50" value="{$shipping_options.cpc.param00|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_cpc_package_insured_value}:</b></td>
  <td><input type="text" name="insvalue" size="10" value="{$shipping_options.cpc.param04|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
  <font class="SmallText">{$lng.txt_shipping_cost_convertion_rate}</font>
  </td>
  <td valign="top"><input type="text" name="currency_rate" size="10" value="{$shipping_options.cpc.currency_rate|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_dimensions} ({$config.General.dimensions_symbol})*:</b></td>
  <td>
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="length" size="6" value="{$shipping_options.cpc.param01|doubleval}"/></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="width" size="6" value="{$shipping_options.cpc.param02|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="height" size="6" value="{$shipping_options.cpc.param03|doubleval}"/></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><label for="use_maximum_dimensions"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="use_maximum_dimensions" id="use_maximum_dimensions" value="Y"{if $shipping_options.cpc.param07 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td>
    <b>{$lng.lbl_maximum_package_weight} ({$config.General.weight_symbol})*:</b>
  </td>
  <td>
    <input type="text" name="weight" size="6" value="{$shipping_options.cpc.param06|doubleval}"/> ({$lng.lbl_should_not_exceed} {$max_cpc_weight} {$config.General.weight_symbol})
  </td>
</tr>

<tr>
  <td colspan="2"><b>*</b> {$lng.txt_cpc_limits_note}</td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"Canada Post"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "ARB"}

{capture name=dialog}
<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="ARB" />

<table width="100%">

<tr>
  <td width="50%"><b>{$lng.lbl_arb_pkgtype}:</b></td>
  <td width="50%">
  <select name="param00">
    <option value="P"{if $shipping_options.arb.param00 eq "P"} selected="selected"{/if}>Package</option>
    <option value="L"{if $shipping_options.arb.param00 eq "L"} selected="selected"{/if}>Letter</option>
  </select>
  </td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_arb_shipdays}:</b></td>
  <td><input type="text" name="param01" size="10" value="{$shipping_options.arb.param01|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
  <font class="SmallText">{$lng.txt_shipping_cost_convertion_rate_us_dollars}</font>
  </td>
  <td valign="top"><input type="text" name="currency_rate" size="10" value="{$shipping_options.arb.currency_rate|escape}" /></td>
</tr>

<tr valign="top">
  <td width="50%"><b>{$lng.lbl_arb_ap_type}:</b></td>
  <td width="50%">
  <select name="param05">
    <option value="NR" {if $shipping_options.arb.param05 eq "NR"} selected="selected"{/if}>Not required</option>
    <option value="AP" {if $shipping_options.arb.param05 eq "AP"} selected="selected"{/if}>Asset Protection</option>
  </select>
  </td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_arb_ap_value}:</b></td>
  <td><input type="text" name="param06" size="10" value="{$shipping_options.arb.param06|escape}" /></td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_arb_haz}:</b></td>
  <td><input type="checkbox" name="opt_haz" value="Y"{if $shipping_options.arb.opt_haz eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_arb_codpmt}:</b></td>
  <td>
  <select name="param08">
    <option value="M"{if $shipping_options.arb.param08 eq "M"} selected="selected"{/if}>Cashier's Check or Money Order</option>
    <option value="P"{if $shipping_options.arb.param08 eq "P"} selected="selected"{/if}>Personal or Company Check</option>
  </select>
  </td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_arb_codval}:</b></td>
  <td><input type="text" name="param09" size="10" value="{$shipping_options.arb.param09|escape}" /></td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_arb_opt_own_account}:</b></td>
  <td><input type="checkbox" name="opt_own_account" value="Y"{if $shipping_options.arb.opt_own_account eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_maximum_package_weight} ({$config.General.weight_symbol})*:</b></td>
  <td><input type="text" name="param10" size="10" value="{$shipping_options.arb.param10|doubleval}" />({$lng.lbl_should_not_exceed} {$max_arb_weight} {$config.General.weight_symbol})</td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_dimensions} ({$config.General.dimensions_symbol})*:</b></td>
  <td>
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="param02" size="6" value="{$shipping_options.arb.param02|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="param03" size="6" value="{$shipping_options.arb.param03|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="param04" size="6" value="{$shipping_options.arb.param04|doubleval}" /></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><label for="param11"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="param11" id="param11" value="Y"{if $shipping_options.arb.param11 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td colspan="2"><b>*</b> {$lng.txt_arb_limits_note}</td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"Airborne / DHL"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "APOST"}

{capture name=dialog}
<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="APOST" />

<table width="100%">

<tr>
  <td>
    <b>{$lng.lbl_maximum_package_weight} ({$config.General.weight_symbol}):</b>
  </td>
  <td>
    <input type="text" name="param04" size="6" value="{$shipping_options.apost.param04|doubleval}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
  <font class="SmallText">{$lng.txt_shipping_cost_convertion_rate_au_dollars}</font>
  </td>
  <td valign="top"><input type="text" name="currency_rate" size="10" value="{$shipping_options.apost.currency_rate|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_maximum_package_dimensions} ({$config.General.dimensions_symbol}):</b></td>
  <td>
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="param00" size="6" value="{$shipping_options.apost.param00|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="param01" size="6" value="{$shipping_options.apost.param01|doubleval}" /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="param02" size="6" value="{$shipping_options.apost.param02|doubleval}" /></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_apost_pkg_no_use}:</b></td>
  <td><input type="checkbox" name="param03" value="Y"{if $shipping_options.apost.param03 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td><label for="param05"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="param05" id="param05" value="Y"{if $shipping_options.apost.param05 eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
    <td colspan="2"><b>{$lng.lbl_note}</b>: {$lng.txt_apost_limits_note}</td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"Australia Post"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}
