{*
$Id: counties.tpl,v 1.2 2010/07/28 11:02:15 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $counties ne ""}
<select name="{$name}" id="{$id|default:$name}" {$style}>
{if $required eq "N"}
<option value="">[{$lng.lbl_please_select_one|wm_remove|escape}]</option>
{/if}
<option value="{if $value_for_other ne "no"}Other{/if}"{if $default eq "Other"} selected="selected"{/if}>{$lng.lbl_other}</option>
{section name=county_idx loop=$counties}
{if $config.General.default_country eq $counties[county_idx].country_code or $country_name eq '' or $default_fields.$country_name.avail eq 'Y'}
<option value="{$counties[county_idx].countyid}"{if $default eq $counties[county_idx].countyid} selected="selected"{/if}>{$counties[county_idx].state}: {$counties[county_idx].county}</option>
{/if}
{/section}
</select>
{else}
<input type="text" id="{$id|default:$name}" size="32" maxlength="65" name="{$name}" value="{$default|escape}" />
{/if}
