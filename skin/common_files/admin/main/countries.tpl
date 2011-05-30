{*
$Id: countries.tpl,v 1.2 2010/07/08 12:01:52 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_countries_management}

{$lng.txt_countries_management_top_text}

<br /><br />

{capture name=dialog}

{section name=zone loop=$zones}
<span style="white-space: nowrap; padding-right: 20px;">
{if $zones[zone].zone eq $zone or ($zones[zone].zone eq "ALL" and $zone eq "")}
<b>{$zones[zone].title}</b>
{else}
<a href="countries.php{if $zones[zone].zone}?zone={$zones[zone].zone}{/if}">{$zones[zone].title}</a>
{/if}
</span>
 &nbsp; 
{/section}

<br /><br />

{include file="main/navigation.tpl"}

{include file="main/check_all_row.tpl" style="line-height: 170%; text-align: right;" form="editcountriesform" prefix="posted_data.+active"}

<form action="countries.php" method="post" name="editcountriesform">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
<input type="hidden" name="zone" value="{$zone}" />

<table cellpadding="2" cellspacing="1" width="100%">

<tr class="TableHead">
  <td nowrap="nowrap">{$lng.lbl_code}</td>
  <td width="80%" nowrap="nowrap">{$lng.lbl_country}</td>
  <td width="10%" nowrap="nowrap">{$lng.lbl_has_states}</td>
  <td nowrap="nowrap">{$lng.lbl_active}</td>
</tr>

{section name=country loop=$countries}

<tr{if $countries[country].code eq $config.Company.location_country} class="TableHead"{else}{cycle values=', class="TableSubHead"'}{/if}>
  <td nowrap="nowrap" align="center">{$countries[country].code}</td>
  <td><input type="text" size="34" maxlength="50" name="posted_data[{$countries[country].code}][country]" value="{$countries[country].country}" style="width: 99%;" /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$countries[country].code}][display_states]" value="Y"{if $countries[country].display_states eq "Y"} checked="checked"{/if} /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$countries[country].code}][active]" value="Y"{if $countries[country].active eq "Y"} checked="checked"{/if} /></td>
</tr>
{/section}

<tr>
<td colspan="5"><br />

{include file="main/navigation.tpl"}

<div class="main-button">
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</div>
<br />
<input type="button" value="{$lng.lbl_deactivate_all|strip_tags:false|escape}" onclick="javascript: document.editcountriesform.mode.value = 'deactivate_all'; document.editcountriesform.submit();" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_activate_all|strip_tags:false|escape}" onclick="javascript: document.editcountriesform.mode.value = 'activate_all'; document.editcountriesform.submit();" />
</td>
</tr>

</table>
</form>

<br /><br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_countries content=$smarty.capture.dialog extra='width="100%"'}
