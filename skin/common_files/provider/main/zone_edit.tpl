{*
$Id: zone_edit.tpl,v 1.4 2010/07/16 06:20:54 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_zone_details}

{$lng.txt_destination_zones_note}

<br /><br />

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var zones = {ldelim}{rdelim};
{counter name='js' start='-1' print=false}
{foreach from=$rest_zones key=z item=v}
zones.{$z} = {ldelim}{foreach from=$v item=c key=k}{if $k gt 0},{/if}{$c}:'Y'{/foreach}{rdelim};
{/foreach}

var msg_err_zone_rename='{$lng.msg_err_zone_rename|escape}';
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/two_select_boxes.js"></script>
<script type="text/javascript" src="{$SkinDir}/js/zone_edit.js"></script>

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_zones_list href="zones.php"}</div>

<br />

<form action="zones.php" method="post" name="zoneform" onsubmit="javascript: if (this.zone_name.value == '') {ldelim} alert(msg_err_zone_rename); return false; {rdelim} else saveSelects(new Array('zone_countries','zone_states','zone_counties'));">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="zoneid" value="{$zoneid}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td colspan="3">
<font class="FormButton">{$lng.lbl_zone_name}:</font>
<input type="text" size="50" name="zone_name" value="{$zone.zone_name|escape}" />
<br /><br />
<input type="submit" value="{if $zoneid}{$lng.lbl_update|strip_tags:false|escape}{else}{$lng.lbl_create|strip_tags:false|escape}{/if}" />
&nbsp;&nbsp;
<input type="button" value=" {$lng.lbl_clone|strip_tags:false} " onclick="javascript: submitForm(this, 'clone');" />

<br /><br />

  </td>
</tr>

{* Countries *}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_countries}</td>
</tr>

<tr>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_set_val}</font></td>
  <td width="10%">&nbsp;</td>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_unset_val}</font></td>
</tr>

<tr>
  <td>
<input type="hidden" id="zone_countries_store" name="zone_countries_store" value="" />
<select id="zone_countries" multiple="multiple" style="width: 100%;" size="{$countries_box_size}">
{section name=cid loop=$zone_countries}
  <option value="{$zone_countries[cid].code}">{$zone_countries[cid].country}</option>
{/section}
<option value="">&nbsp;</option>
</select>
<script type="text/javascript">
//<![CDATA[
normalizeSelect('zone_countries');
//]]>
</script>
  </td>
  <td align="center">
<input type="button" value="&lt;&lt;" onclick="javascript: moveSelect(document.getElementById('zone_countries'), document.getElementById('rest_countries'), 'R');" />
<br /><br />
<input type="button" value="&gt;&gt;" onclick="javascript: moveSelect(document.getElementById('zone_countries'), document.getElementById('rest_countries'), 'L');" />
  </td>
  <td>
<select id="rest_countries" multiple="multiple" style="width: 100%;" size="{$countries_box_size}">
{section name=rcid loop=$rest_countries}
  <option value="{$rest_countries[rcid].code}">{$rest_countries[rcid].country}</option>
{/section}
</select>
  </td>
</tr>

<tr>
  <td colspan="3" align="right">
<table cellpadding="3" cellspacing="1" width="80%">
<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_quick_select}:</td>
  <td align="right">

<table cellpadding="3" cellspacing="1" width="100%">
{assign var="counter" value=0}
{section name=zid loop=$zones}
{if $counter eq 0}<tr>{/if}
  <td align="center" nowrap="nowrap"><a href="javascript:void(0);" onclick="javascript: checkZone('{$zones[zid].zone}', 'rest_countries')">{$zones[zid].title}</a></td>
{inc value=$counter assign="counter"}
{if $counter gt 2}{assign var="counter" value=0}{/if}
{if $counter eq 0}</tr>{/if}
{/section}
</table>

  </td>
</tr>
</table>
  </td>
</tr>

{* States *}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_states}</td>
</tr>

<tr>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_set_val}</font></td>
  <td width="10%">&nbsp;</td>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_unset_val}</font></td>
</tr>

<tr>
  <td>
<input type="hidden" id="zone_states_store" name="zone_states_store" value="" />
<select id="zone_states" multiple="multiple" style="width: 100%;" size="{$states_box_size}">
{section name=sid loop=$zone_states}
  <option value="{$zone_states[sid].country_code}_{$zone_states[sid].code}">{$zone_states[sid].country|truncate:"30":"..."}: {$zone_states[sid].state}</option>
{/section}
<option value="">&nbsp;</option>
</select>
<script type="text/javascript">
//<![CDATA[
normalizeSelect('zone_states');
//]]>
</script>
  </td>
  <td align="center">
<input type="button" value="&lt;&lt;" onclick="javascript: moveSelect(document.getElementById('zone_states'), document.getElementById('rest_states'), 'R');" />
<br /><br />
<input type="button" value="&gt;&gt;" onclick="javascript: moveSelect(document.getElementById('zone_states'), document.getElementById('rest_states'), 'L');" />
  </td>
  <td>
<select id="rest_states" name="rest_states" multiple="multiple" style="width: 100%;" size="{$states_box_size}">
{section name=rsid loop=$rest_states}
  <option value="{$rest_states[rsid].country_code}_{$rest_states[rsid].code|escape}">{$rest_states[rsid].country|truncate:"17":"...":true}: {$rest_states[rsid].state}</option>
{/section}
</select>
  </td>
</tr>

{* Counties *}

{if $config.General.use_counties eq "Y" and ($zone_counties or $rest_counties)}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_counties}</td>
</tr>

<tr>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_set_val}</font></td>
  <td width="10%">&nbsp;</td>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_unset_val}</font></td>
</tr>

<tr>
  <td>
<input type="hidden" id="zone_counties_store" name="zone_counties_store" value="" />
<select id="zone_counties" multiple="multiple" style="width: 100%;" size="{$counties_box_size}">
{section name=ctid loop=$zone_counties}
  <option value="{$zone_counties[ctid].countyid}">{$zone_counties[ctid].country}: {$zone_counties[ctid].state}: {$zone_counties[ctid].county}</option>
{/section}
  <option value="">&nbsp;</option>
</select>
<script type="text/javascript">
//<![CDATA[
normalizeSelect('zone_counties');
//]]>
</script>
  </td>
  <td align="center">
<input type="button" value="&lt;&lt;" onclick="javascript: moveSelect(document.getElementById('zone_counties'), document.getElementById('rest_counties'), 'R');" />
<br /><br />
<input type="button" value="&gt;&gt;" onclick="javascript: moveSelect(document.getElementById('zone_counties'), document.getElementById('rest_counties'), 'L');" />
  </td>
  <td>
<select id="rest_counties" name="rest_counties" multiple="multiple" style="width: 100%;" size="{$counties_box_size}">
{section name=rctid loop=$rest_counties}
  <option value="{$rest_counties[rctid].countyid}">{$rest_counties[rctid].country}: {$rest_counties[rctid].state}: {$rest_counties[rctid].county}</option>
{/section}
</select>
  </td>
</tr>

{/if}

{* City masks *}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_cities}</td>
</tr>

<tr>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_set_val}</font></td>
  <td width="10%">&nbsp;</td>
  <td width="45%"><font class="FormButton">{$lng.lbl_city_mask_examples}:</font></td>
</tr>

<tr>
  <td>{include file="provider/main/zone_element.tpl" name="zone_cities" field_type="T" box_size=$cities_box_size}</td>
  <td align="center">&nbsp;</td>
  <td>{$lng.txt_city_mask_examples}</td>
</tr>

{* Zip code masks *}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_zip_postal_codes}</td>
</tr>

<tr>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_set_val}</font></td>
  <td width="10%">&nbsp;</td>
  <td width="45%"><font class="FormButton">{$lng.lbl_zipcode_mask_examples}:</font></td>
</tr>

<tr>
  <td>{include file="provider/main/zone_element.tpl" name="zone_zipcodes" field_type="Z" box_size=$zipcodes_box_size}</td>
  <td align="center">&nbsp;</td>
  <td>{$lng.txt_zipcode_mask_examples}</td>
</tr>

{* Address masks *}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_addresses}</td>
</tr>

<tr>
  <td width="45%" align="center"><font class="FormButton">{$lng.lbl_set_val}</font></td>
  <td width="10%">&nbsp;</td>
  <td width="45%"><font class="FormButton">{$lng.lbl_address_mask_examples}:</font></td>
</tr>

<tr>
  <td>{include file="provider/main/zone_element.tpl" name="zone_addresses" field_type="A" box_size=$addresses_box_size}</td>
  <td align="center">&nbsp;</td>
  <td>{$lng.txt_address_mask_examples}</td>
</tr>
</table>

<br />

<div class="main-button">
  <input type="submit" class="big-main-button" value="{$lng.lbl_save_zone_details|strip_tags:false|escape}" />
</div>

</form>
