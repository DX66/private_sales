{*
$Id: lookup_window.tpl,v 1.1 2010/05/21 08:32:19 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name="dialog"}
<form action="{$catalogs.admin}/anti_fraud.php" method="get" name="lookupform">
<input type="hidden" name="mode" value="popup" />
<input type="hidden" name="resolve" value="" />

<table>
<tr valign="top">
<td>
  <table>
  <tr><td>IP:</td><td>&nbsp;</td><td><input type="text" name="ip" value="{$ip}" /></td></tr>
  <tr><td>Proxy IP:</td><td>&nbsp;</td><td><input type="text" name="proxy_ip" value="{$proxy_ip}" /></td></tr>
{if $address_resolved}
  <tr><td>{$lng.lbl_city}</td><td>&nbsp;</td><td>{$resolved.city}</td><td></tr>
  <tr><td>{$lng.lbl_state}</td><td>&nbsp;</td><td>{$resolved.state}</td></tr>
  <tr><td>{$lng.lbl_country}</td><td>&nbsp;</td><td>{$resolved.country}</td></tr>
  <tr><td>{$lng.lbl_zip_code}</td><td>&nbsp;</td><td>{$resolved.zipcode|default:$lng.lbl_unknown}</td></tr>
{/if}
  </table>
</td>
<td>
  <table>
  <tr><td>{$lng.lbl_city}:</td><td class="Star">*</td><td><input type="text" name="city" value="{$address.city|escape}" /></td></tr>
  <tr><td>{$lng.lbl_state}:</td><td class="Star">*</td><td><input type="text" name="state" value="{$address.state|escape}" /></td></tr>
  <tr><td>{$lng.lbl_country}:</td><td class="Star">*</td><td><input type="text" name="country" value="{$address.country|escape}" /></td></tr>
  <tr><td>{$lng.lbl_zip_code}:</td><td class="Star">*</td><td><input type="text" name="zipcode" value="{$address.zipcode|escape}" /></td></tr>
{if $distance_resolved}
  <tr><td colspan="3">&nbsp;</td></tr>
  <tr><td>Distance:</td><td>&nbsp;</td><td>{$resolved.distance} km</td></tr>
{/if}
  </table>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
  <td valign="middle"><input type="button" value="{$lng.lbl_af_lookup_address|strip_tags:false|escape}" onclick="document.lookupform.resolve.value='address';document.lookupform.submit();" /></td>
  <td valign="middle"><input type="button" value="{$lng.lbl_af_deasure_distance|strip_tags:false|escape}" onclick="document.lookupform.resolve.value='distance';document.lookupform.submit();" /><br />
  {$lng.txt_fields_are_mandatory}
  </td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_af_lookup_address extra='width="100%"'}
