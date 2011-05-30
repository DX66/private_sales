{*
$Id: zones.tpl,v 1.4 2010/07/16 06:20:54 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_destination_zones}

{$lng.txt_destination_zones_note}

<br /><br />

{if $zones}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'zonesform';
checkboxes = new Array({foreach from=$zones item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.zoneid}]'{/foreach});

//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="zones.php" method="post" name="zonesform">
<input type="hidden" name="mode" value="delete" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="20%">{$lng.lbl_zone_name}</td>
  <td width="80%" align="center">{$lng.txt_note}</td>
</tr>

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" disabled="disabled" /></td>
  <td class="FormButton">{$lng.lbl_zone_default}</td>
  <td>{$lng.lbl_all_addresses}</td>
</tr>

{if $zones}

{section name=zone loop=$zones}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="to_delete[{$zones[zone].zoneid}]" /></td>
  <td class="FormButton"><a href="zones.php?zoneid={$zones[zone].zoneid}">{$zones[zone].zone_name}</a></td>
  <td>
{if  $zones[zone].elements}
{section name=el loop=$zones[zone].elements}
{strip}
{if $zones[zone].elements[el].element_name ne ""}
{$zones[zone].elements[el].element_name}
{else}
{if $zones[zone].elements[el].element_type eq "C"}{$lng.lbl_countries}:
{elseif $zones[zone].elements[el].element_type eq "S"}{$lng.lbl_states}:
{elseif $zones[zone].elements[el].element_type eq "G"}{$lng.lbl_counties}:
{elseif $zones[zone].elements[el].element_type eq "T"}{$lng.lbl_city_masks}:
{elseif $zones[zone].elements[el].element_type eq "Z"}{$lng.lbl_zipcode_masks}:
{elseif $zones[zone].elements[el].element_type eq "A"}{$lng.lbl_address_masks}:
{/if}
{$zones[zone].elements[el].counter}
{/if}
{if not %el.last%},{/if}
{/strip}
{/section}
{else}
{$lng.txt_zone_is_empty}
{/if}
<br />
  </td>
</tr>

{/section}

<tr>
  <td colspan="3" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete\\[[0-9]+\\]', 'gi'))) document.zonesform.submit();" />
  </td>
</tr>

{/if}

</table>
</form>

<br /><br />

<input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='zones.php?mode=add';" />
