{*
$Id: counties.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_counties_management}

{$lng.txt_counties_management_top_text}

<br /><br />

{capture name=dialog}

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="HeadText">{$state.state} ({$state.country})</td>
<td align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_return_states_list href="states.php?country=`$state.country_code`"}</td>
</tr>
</table>

<br /><br />

{include file="main/navigation.tpl"}

{if $counties ne ""}
<form action="counties.php" method="post" name="countiesform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="stateid" value="{$stateid}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
{/if}

{if $counties ne ""}
{include file="main/check_all_row.tpl" style="line-height: 170%; text-align: left;" form="countiesform" prefix="selected"}
{/if}
<table cellpadding="3" cellspacing="1" width="50%">

<tr class="TableHead">
  <td width="1%" align="center">&nbsp;</td>
  <td nowrap="nowrap">{$lng.lbl_county}</td>
</tr>

{if $counties ne ""}

{section name=cnt loop=$counties}

<tr{cycle values=", class='TableSubHead'"}>
<td align="center"><input type="checkbox" name="selected[{$counties[cnt].countyid}]" /></td>
<td><input type="text" size="50" name="posted_data[{$counties[cnt].countyid}][county]" value="{$counties[cnt].county|escape}" /></td>
</tr>

{/section}

<tr>
<td colspan="4"><br />
{include file="main/navigation.tpl"}

<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('selected\\[[0-9]+\\]', 'ig'))) {ldelim} document.countiesform.mode.value = 'delete'; document.countiesform.submit();{rdelim}" />
&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</td>
</tr>

{else}

<tr>
<td colspan="4" align="center">{$lng.txt_no_counties}</td>
</tr>

{/if}

</table>

{if $counties ne ""}
</form>
{/if}

<br /><br />

<form action="counties.php" method="post">
<input type="hidden" name="stateid" value="{$stateid}" />
<input type="hidden" name="mode" value="add" />

<table cellpadding="2" cellspacing="1">

<tr>
<td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new_county}</td>
</tr>

<tr>
<td>{$lng.lbl_enter_county_name}:</td>
<td><input type="text" size="40" name="new_county_name" value="" /></td>
<td><input type="submit" value=" {$lng.lbl_add|strip_tags:false|escape} " /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_counties content=$smarty.capture.dialog extra='width="100%"'}
