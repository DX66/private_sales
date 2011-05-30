{*
$Id: states.tpl,v 1.4 2010/07/08 12:01:52 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var addStateRF = [
  ['new_state_code', "{$lng.lbl_code|wm_remove|escape:javascript}"],
  ['new_state_name', "{$lng.lbl_state|wm_remove|escape:javascript}"]
];
//]]>
</script>
{include file="check_required_fields_js.tpl"}
{include file="page_title.tpl" title=$lng.lbl_states_management}

{$lng.txt_states_management_top_text}

<br /><br />

{capture name=dialog}

{if $states_for_countries}

{section name=ds loop=$states_for_countries}
<span style="white-space: nowrap; padding-right: 20px;">
{if $states_for_countries[ds].country_code eq $country}
<b>{$states_for_countries[ds].country}</b>
{else}
<a href="states.php?country={$states_for_countries[ds].country_code}">{$states_for_countries[ds].country}</a>
{/if}
</span>
&nbsp;
{/section}

<br /><br />
{/if}

{if $config.General.use_counties eq "Y"}
{assign var="colspan" value="4"}
{else}
{assign var="colspan" value="3"}
{/if}

{include file="main/navigation.tpl"}

{if $states ne ""}
<form action="states.php" method="post" name="statesform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="country" value="{$country|escape}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

{if $states ne ""}

<tr>
  <td colspan="{$colspan}" align="right">
{include file="main/check_all_row.tpl" style="line-height: 170%; text-align: left;" form="statesform" prefix="selected"}
  </td>
</tr>

{/if}

<tr class="TableHead">
  <td width="1%">&nbsp;</td>
  <td width="10%">{$lng.lbl_code}</td>
  <td width="60%">{$lng.lbl_state}</td>
{if $config.General.use_counties eq "Y"}
  <td width="20%" align="center">{$lng.lbl_counties}</td>
{/if}
</tr>

{if $states ne ""}

{assign var="current_country" value="NONE"}

{section name=state loop=$states}

{if $states[state].country_code ne $current_country}
{assign var="current_country" value=$states[state].country_code}
<tr class="TableSubHead">
  <th colspan="{$colspan}" align="center">{$states[state].country|default:$lng.lbl_unknown_country}</th>
</tr>
{/if}

<tr{cycle values=", class='TableSubHead'"}>
  <td align="center"><input type="checkbox" name="selected[{$states[state].stateid}]" /></td>
  <td align="center"><input type="text" size="6" name="posted_data[{$states[state].stateid}][code]" value="{$states[state].code|escape}" /></td>
  <td><input type="text" size="30" name="posted_data[{$states[state].stateid}][state]" value="{$states[state].state}" style="width: 99%;" /></td>
{if $config.General.use_counties eq "Y"}
  <td align="center"><a href="counties.php?stateid={$states[state].stateid}">{if $states[state].counties gt 0}{$lng.lbl_N_counties|substitute:"items":$states[state].counties}{else}{$lng.txt_not_available}{/if}</a></td>
{/if}
</tr>

{/section}

<tr>
  <td colspan="{$colspan}"><br />
{include file="main/navigation.tpl"}

<div class="main-button" style="float:left">
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</div>

<div style="float:right">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('selected\\[[0-9]+\\]', 'ig'))) submitForm(this, 'delete');" />
</div>
&nbsp;&nbsp;&nbsp;
</td>
</tr>

{else}

<tr>
  <td colspan="{$colspan}" align="center">{$lng.txt_no_states}</td>
</tr>

{/if}

</table>

{if $states ne ""}
</form>
{/if}

<br /><br />

{if $countries ne ""}

{include file="main/subheader.tpl" title=$lng.lbl_add_new_state}

<form action="states.php" method="post" onsubmit="javascript: return checkRequired(addStateRF);">
<input type="hidden" name="country" value="{$country|escape}" />
<input type="hidden" name="mode" value="add" />

<table cellpadding="2" cellspacing="1" width="100%">

<tr class="TableHead">
  <td>&nbsp;</td>
  <td>{$lng.lbl_code}</td>
  <td width="60%">{$lng.lbl_state}</td>
  <td width="30%">{$lng.lbl_country}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><input type="text" size="6" id="new_state_code" name="new_state_code" value="" /></td>
  <td><input type="text" size="30" id="new_state_name" name="new_state_name" value="" style="width: 99%;" /></td>
  <td>
  <select name="new_country_code">
{section name=country_idx loop=$countries}
    <option value="{$countries[country_idx].country_code}"{if $countries[country_idx].country_code eq $country} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
  </select>
  </td>
</tr>

<tr>
  <td colspan="3"><input type="submit" value="{$lng.lbl_add_state|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{else}

{$lng.txt_cant_create_state_from_country}

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_states content=$smarty.capture.dialog extra='width="100%"'}
