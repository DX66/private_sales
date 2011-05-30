{*
$Id: states.tpl,v 1.2 2010/07/28 11:02:15 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $states ne ""}
<select name="{$name}" id="{$id|default:$name|replace:"[":"_"|replace:"]":""}" {$style}>
{if $required eq "N"}
  <option value="">[{$lng.lbl_please_select_one|wm_remove|escape}]</option>
{/if}
  <option value="{if $value_for_other ne "no"}Other{/if}"{if $default eq "Other"} selected="selected"{/if}>{$lng.lbl_other}</option>
{section name=state_idx loop=$states}
{if $config.General.default_country eq $states[state_idx].country_code or $country_name eq '' or $default_fields.$country_name.avail eq 'Y'}
  <option value="{$states[state_idx].state_code|escape}"{if $default eq $states[state_idx].state_code and (not $default_country or $default_country eq $states[state_idx].country_code)} selected="selected"{/if}>{$states[state_idx].country_code}: {$states[state_idx].state|amp}</option>
{/if}
{/section}
</select>
{else}
<input type="text"{if $name ne ''} id="{$id|default:$name|replace:"[":"_"|replace:"]":""}"{/if} size="32" maxlength="65" name="{$name}" value="{$default|escape}" />
{/if}
