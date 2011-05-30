{*
$Id: test_shippings.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.lbl_test_destination_shipping_address|wm_remove|escape}</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>
{include file="head_admin.tpl"}

<!-- main area -->

{include file="check_zipcode_js.tpl"}

<script type="text/javascript">
//<![CDATA[
{literal}
function check_zip_code_local(flag) {
  if (!flag) {
    return check_zip_code_field(document.forms["registerform"].s_country, document.forms["registerform"].s_zipcode); 
  } else {
    return check_zip_code_field(document.getElementById('o_country'), document.getElementById('o_zipcode'));
  }
}
//]]>
{/literal}
</script>

{if $config.Shipping.realtime_shipping ne "Y"}

<table width="100%" cellpadding="10" cellspacing="10">
<tr>
  <td><h2>{$lng.txt_realtime_calc_is_disabled}</h2></td>
</tr>
</table>

{else}

<table width="100%" cellpadding="0" cellspacing="10">
<tr>
  <td align="left">

{capture name=dialog}
<form action="test_realtime_shipping.php" method="post" name="registerform" onsubmit="javascript: return check_zip_code()">

<table cellpadding="1" cellspacing="1" width="400">

<tr valign="middle">
  <td height="20" colspan="3"><b>{$lng.lbl_origin_address}:</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
  <td align="right" width="25%">{$lng.lbl_address}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap" width="75%"><input type="text" name="origin[address]" size="32" maxlength="64" value="{$orig_address.address}" /></td>
</tr>

<tr valign="middle">
  <td align="right" width="25%">{$lng.lbl_city}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap" width="75%"><input type="text" name="origin[city]" size="32" maxlength="64" value="{$orig_address.city}" /></td>
</tr>

<tr valign="middle">
  <td align="right">{$lng.lbl_state}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap">{include file="main/states.tpl" states=$states name="origin[state]" default=$orig_address.state default_country=$orig_address.country}</td>
</tr>

<tr valign="middle">
  <td align="right">{$lng.lbl_country}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap">
  <select id="o_country" name="origin[country]" size="1" onchange="javascript: check_zip_code_local(true);">
{foreach from=$countries item=c}
    <option value="{$c.country_code|escape}"{if $orig_address.country eq $c.country_code} selected="selected"{/if}>{$c.country}</option>
{/foreach}
  </select>
  </td>
</tr>

<tr valign="middle">
  <td align="right">{$lng.lbl_zip_code}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap">
    {include file="main/zipcode.tpl" name="origin[zipcode]" id="o_zipcode" val=$orig_address.zipcode zip4=$orig_address.zip4}
  </td>
</tr>

<tr valign="middle">
  <td height="20" colspan="3">&nbsp;</td>
</tr>

<tr valign="middle">
  <td height="20" colspan="3"><b>{$lng.lbl_destination_address}:</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
  <td align="right" width="25%">{$lng.lbl_address}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap" width="75%">
  <input type="text" name="s_address" size="32" maxlength="64" value="{$userinfo.s_address|escape}" />
  </td>
</tr>

<tr valign="middle">
  <td align="right" width="25%">{$lng.lbl_city}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap" width="75%">
  <input type="text" name="s_city" size="32" maxlength="64" value="{$userinfo.s_city|escape}" />
  </td>
</tr>

<tr valign="middle">
  <td align="right">{$lng.lbl_state}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap">
  {include file="main/states.tpl" states=$states name="s_state" default=$userinfo.s_state default_country=$userinfo.s_country}
  </td>
</tr>

<tr valign="middle">
  <td align="right">{$lng.lbl_country}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap">
  <select name="s_country" size="1" onchange="check_zip_code_local()">
{section name=country_idx loop=$countries}
    <option value="{$countries[country_idx].country_code}"{if $userinfo.s_country eq $countries[country_idx].country_code or ($countries[country_idx].country_code eq $orig_address.country and $userinfo.s_country eq "")} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
  </select>
  </td>
</tr>

<tr valign="middle">
  <td align="right">{$lng.lbl_zip_code}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap">
    {include file="main/zipcode.tpl" name="s_zipcode" id="s_zipcode" val=$userinfo.s_zipcode zip4=$userinfo.zip4}
  </td>
</tr>

<tr valign="middle">
  <td height="20" colspan="3"><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
  <td align="right" nowrap="nowrap">{$lng.lbl_weight} {if $config.General.weight_symbol}({$config.General.weight_symbol}){/if}:</td>
  <td>&nbsp;</td>
  <td nowrap="nowrap" width="75%"><input type="text" name="weight" value="{$weight}" /></td>
</tr>

{if $active_modules.UPS_OnLine_Tools and $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y"}
<tr>
  <td align="right" nowrap="nowrap">{$lng.lbl_shipping_carrier}:</td>
  <td>&nbsp;</td>
  <td>
  {include file="main/select_carrier.tpl" name="selected_carrier"}
  </td>
</tr>
{/if}

<tr valign="middle">
  <td colspan="2">&nbsp;</td>
  <td nowrap="nowrap"><br />
{include file="buttons/submit.tpl" href="javascript:if(check_zip_code_local())document.registerform.submit();" style="button"}
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_test_destination_shipping_address content=$smarty.capture.dialog}
  </td>
</tr>
<tr>
  <td><hr noshade="noshade" style="COLOR: #FF8600; HEIGHT: 1px;" /></td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="10">
<tr>
  <td>{$content}</td>
</tr>
</table>

{/if}
<!-- main area -->
</body>
</html>
