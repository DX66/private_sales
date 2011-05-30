{*
$Id: ups_rss.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
//<![CDATA[
{literal}
function switchUPSDim(s) {
  s.form.length.disabled = s.form.width.disabled = s.form.height.disabled = s.options[s.selectedIndex].value != '02';
  s.form.use_maximum_dimensions.disabled = s.options[s.selectedIndex].value != '02';
}
$.event.add(
  window,
  'load',
  function() {
    switchUPSDim(document.upsconfigureform.packaging_type);
  }
);
{/literal}
//]]>
</script>
{capture name=dialog}
<br />
{if $countries}
<script type="text/javascript" src="{$SkinDir}/js/two_select_boxes.js"></script>
{/if}

<!-- UPS OnLineTools has been registered in country: {$orig_country} -->

<form action="ups.php" method="post" name="upsconfigureform" {if $countries}onsubmit="javascript: return saveSelects(new Array('_lbs_countries'));"{/if}>
<input type="hidden" name="mode" value="{$mode}" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
  <td width="95" align="center" valign="top">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
  <td>&nbsp;</td>
  <td width="100%">

{if $config.Shipping.realtime_shipping ne "Y" or $config.Shipping.use_intershipper eq "Y"}
<font class="ErrorMessage">{$lng.txt_ups_rss_warning}</font>
<br /><br />
{/if}

<table width="100%" cellpadding="3" cellspacing="1">

<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_ups_rss_tool}</td>
</tr>

<tr>
  <td colspan="2">{$lng.txt_fields_are_mandatory}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_ups_shipper_number}:</b></td>
  <td>
    <input type="text" name="shipper_number" value="{$shipping_options.rss.shipper_number|escape}" />
  </td>
</tr>

<tr>
  <td width="50%"><b>{$lng.lbl_ups_rss_merchant_pickup_type}:<font class="Star">*</font></b><br />
  <a href="javascript:void(0);" onclick="javascript:window.open('popup_info.php?action=UPS','UPS_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" title="{$lng.lbl_open_new_window|escape}" class="SmallNote">{$lng.lbl_click_here_for_help}</a></td>
  <td width="50%">
    <select name="pickup_type" size="6" style="width: 200px;">
      <option value="01" {if $shipping_options.rss.pickup_type eq "01" or $shipping_options.rss.pickup_type eq ""} selected="selected"{/if}>Daily Pickup</option>
      <option value="03" {if $shipping_options.rss.pickup_type eq "03"} selected="selected"{/if}>Customer counter</option>
      <option value="06" {if $shipping_options.rss.pickup_type eq "06"} selected="selected"{/if}>One time pickup</option>
      <option value="07" {if $shipping_options.rss.pickup_type eq "07"} selected="selected"{/if}>On call air</option>
      <option value="11" {if $shipping_options.rss.pickup_type eq "11"} selected="selected"{/if}>Suggested retail rates</option>
      <option value="19" {if $shipping_options.rss.pickup_type eq "19"} selected="selected"{/if}>Letter center</option>
      <option value="20" {if $shipping_options.rss.pickup_type eq "20"} selected="selected"{/if}>Air service center</option>
    </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_ups_destination_type}:<font class="Star">*</font></b></td>
  <td>
    <table cellpadding="1" cellspacing="1">
    <tr>
      <td><input type="radio" name="residential" value="Y" {if $shipping_options.rss.residential eq "Y" or $shipping_options.rss.residential eq ""} checked="checked"{/if} /></td>
      <td>{$lng.lbl_ups_residential_address}</td>
      <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td><input type="radio" name="residential" value="N" {if $shipping_options.rss.residential eq "N"} checked="checked"{/if} /></td>
      <td>{$lng.lbl_ups_commercial_address}</td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_packaging_type}:</b></td>
  <td>
    <select name="packaging_type" style="width: 200px;" onchange="javascript: switchUPSDim(this);">
    {foreach from=$ups_packages key=key item=package}
      <option value="{$key}" {if $shipping_options.rss.packaging_type eq $key} selected="selected"{/if}>{$package.name}</option>
    {/foreach}
    </select>
  </td>
</tr>

<tr>
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_ups_package_limits class="grey"}</td>
</tr>

<tr>
  <td colspan="2">{$lng.txt_ups_limits_note}</td>
</tr>

<tr>
  <td>
      <b>{$lng.lbl_maximum_package_weight} ({$shipping_options.rss.weight_units}):</b><br />
  </td>
  <td valign="top">
    <input type="text" name="weight" value="{$shipping_options.rss.weight}" style="width: 200px;" /> 
  </td>
</tr>

<tr>
  <td>
      <b>{$lng.lbl_maximum_package_dimensions} ({$shipping_options.rss.dim_units}):</b><br />
  </td>
  <td valign="top">
    <table cellpadding="0" cellspacing="1" border="0">
    <tr>
      <td>{$lng.lbl_length}</td>
      <td></td>
      <td>{$lng.lbl_width}</td>
      <td></td>
      <td>{$lng.lbl_height}</td>
    </tr>
    <tr>
      <td><input type="text" name="length" value="{$shipping_options.rss.length}" size="6"{if $require_dimensions ne "Y"} disabled="disabled"{/if} /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="width" value="{$shipping_options.rss.width}" size="6"{if $require_dimensions ne "Y"} disabled="disabled"{/if} /></td>
      <td>&nbsp;x&nbsp;</td>
      <td><input type="text" name="height" value="{$shipping_options.rss.height}" size="6"{if $require_dimensions ne "Y"} disabled="disabled"{/if} /></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td><label for="use_maximum_dimensions"><b>{$lng.lbl_use_maximum_dimensions}:</b></label></td>
  <td><input type="checkbox" name="use_maximum_dimensions" id="use_maximum_dimensions" value="Y"{if $shipping_options.rss.use_maximum_dimensions eq "Y"} checked="checked"{/if} {if $require_dimensions ne "Y"} disabled="disabled"{/if} /></td>
</tr>

<tr>
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_service_options class="grey"}</td>
</tr>

<tr>
  <td colspan="2">
    <table cellpadding="2" cellspacing="1">
    <tr>
      <td><input type="checkbox" name="upsoptions[]" value="AH" {if $shipping_options.rss.AH eq "Y"} checked="checked"{/if} /></td>
      <td><b>{$lng.lbl_ups_additional_handling}</b></td>
    </tr>
    <tr>  
      <td><input type="checkbox" name="upsoptions[]" value="SP" {if $shipping_options.rss.SP eq "Y"} checked="checked"{/if} /></td>
      <td><b>{$lng.lbl_ups_saturday_pickup}</b></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="upsoptions[]" value="SD" {if $shipping_options.rss.SD eq "Y"} checked="checked"{/if} /></td>
      <td><b>{$lng.lbl_ups_saturday_delivery}</b></td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_delivery_confirmation class="grey"}</td>
</tr>

<tr>
  <td colspan="2">{$lng.txt_ups_delivery_notification_note}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_delivery_confirmation}:</b></td>
  <td>
    <select name="delivery_conf" style="width: 200px;">
    <option value="0">{$lng.lbl_ups_no_confirmation}</option>
    <option value="1"{if $shipping_options.rss.delivery_conf eq 1} selected="selected"{/if}>{$lng.lbl_ups_no_signature}</option>
    <option value="2"{if $shipping_options.rss.delivery_conf eq 2} selected="selected"{/if}>{$lng.lbl_ups_signature_required}</option>
    <option value="3"{if $shipping_options.rss.delivery_conf eq 3} selected="selected"{/if}>{$lng.lbl_ups_adult_signature}</option>
    </select>
  </td>
</tr>

<tr>
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_ups_negotiated_rates class="grey"}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_ups_use_negotiated_rates}:</b></td>
  <td><input type="checkbox" name="negotiated_rates" value="Y" {if $shipping_options.rss.negotiated_rates eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_ups_convertion_rate class="grey"}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
  <font class="SmallText">{$lng.txt_shipping_conversion_rate}</font>
  </td>
  <td valign="top">
    <input type="text" name="conversion_rate" value="{$shipping_options.rss.conversion_rate|default:"1"}" style="width: 200px;" />
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_shipping_cost_currency}:</b><br />
  <font class="SmallText">{$lng.txt_shipping_cost_currency}</font>
  </td>
  <td valign="top">
    <b>{$shipping_options.rss.currency_code|default:"Unknown"}</b>
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

{if $countries}
<tr>
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_ups_units class="grey"}</td>
</tr>

<tr>
  <td colspan="2">{$lng.txt_ups_units_note}</td>
</tr>

<tr>
<td colspan="2">
<table cellpadding="3" cellspacing="1" width="100%">
<tr>
    <td><b>{$lng.lbl_ups_lbs_countries}:</b></td>
    <td>&nbsp;</td>
  <td><b>{$lng.lbl_ups_kg_countries}:</b></td>
</tr>

<tr>
    <td width="45%;">
<input type="hidden" id="_lbs_countries_store" name="lbs_countries" value="" />
<select id="_lbs_countries" multiple="multiple" style="width:100%" size="10">
{section name=cid loop=$lbs_countries}
    <option value="{$lbs_countries[cid].country_code}">{$lbs_countries[cid].country}</option>
{/section}
    <option value="">&nbsp;</option>
</select>
<script type="text/javascript">
//<![CDATA[
normalizeSelect('_lbs_countries');
//]]>
</script>
    </td>
    <td align="center" width="10%">
<input type="button" value="&lt;&lt;" onclick="javascript: moveSelect(document.getElementById('_lbs_countries'), document.getElementById('rest_countries'), 'R');" />
<br /><br />
<input type="button" value="&gt;&gt;" onclick="javascript: moveSelect(document.getElementById('_lbs_countries'), document.getElementById('rest_countries'), 'L');" />
    </td>
    <td width="45%">
<select id="rest_countries" multiple="multiple" style="width:100%" size="10">
{section name=rcid loop=$rest_countries}
    <option value="{$rest_countries[rcid].country_code}">{$rest_countries[rcid].country}</option>
{/section}
</select>
    </td>
</tr>
</table>
</td>
</tr>
{/if}

<tr>
  <td colspan="2">&nbsp;</td>
</tr>

<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_ups_av}</td>
</tr>

<tr>
  <td><b>{$lng.lbl_ups_av_status}:</b></td>
  <td>
    <select name="av_status" style="width: 200px;">
    <option value="Y"{if $shipping_options.rss.av_status eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
    <option value="N"{if $shipping_options.rss.av_status ne "Y"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
    </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_ups_av_quality}:</b></td>
  <td>
    <select name="av_quality" style="width: 200px;">
    <option value="exact"{if $shipping_options.rss.av_quality eq "exact"} selected="selected"{/if}>{$lng.lbl_exact_match}</option>
    <option value="very_close"{if $shipping_options.rss.av_quality eq "very_close"} selected="selected"{/if}>{$lng.lbl_very_close_match}</option>
    <option value="close"{if $shipping_options.rss.av_quality eq "close"} selected="selected"{/if}>{$lng.lbl_close_match}</option>
    <option value="possible"{if $shipping_options.rss.av_quality eq "possible"} selected="selected"{/if}>{$lng.lbl_possible_match}</option>
    <option value="poor"{if $shipping_options.rss.av_quality eq "poor"} selected="selected"{/if}>{$lng.lbl_poor_match}</option>
    </select>
  </td>
</tr>

<tr>
  <td colspan="2">{$lng.txt_ups_av_note}</td>
</tr>

<tr>
  <td colspan="2"><br /><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>

<br /><br />

{$lng.txt_ups_av_note2}

<br /><br />

<div align="right">
<table>
<tr>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_main_page title='' style="button" href="ups.php"}</td>
  <td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_test title='' style="button" href="`$catalogs.admin`/test_realtime_shipping.php" target="_blank"}</td>
  <td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
</tr>
</table>
</div>

  </td>
</tr>

</table>
</form>

<br />
<hr />
<div align="center">
{$lng.txt_ups_trademark_text}
</div>
{/capture}
{include file="modules/UPS_OnLine_Tools/dialog.tpl" title=$title content=$smarty.capture.dialog extra='width="100%"'}
