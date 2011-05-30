{*
$Id: speed_bar.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_speed_bar_management}

{$lng.txt_speed_bar_management_top_text}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="speed_bar.php?"}

<br />

<form action="speed_bar.php" method="post" name="speedbarform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="id" value="" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="1%">&nbsp;</td>
  <td width="5%">{$lng.lbl_pos}</td>
  <td width="40%">{$lng.lbl_link_title}</td>
  <td width="40%">{$lng.lbl_url}</td>
  <td width="5%">{$lng.lbl_active}</td>
</tr>

{if $speed_bar}

{section name=sb loop=$speed_bar}
<tr{cycle values=", class='TableSubHead'"}>
  <td width="1%"><input type="checkbox" name="to_delete[{$speed_bar[sb].id}]" value="Y" /></td>
  <td>
  <input type="hidden" name="posted_data[{%sb.index%}][id]" value="{$speed_bar[sb].id}" />
  <input type="text" size="3" maxlength="5" name="posted_data[{%sb.index%}][orderby]" value="{$speed_bar[sb].orderby}" />
  </td>
  <td><input type="text" size="45" name="posted_data[{%sb.index%}][title]" value="{$speed_bar[sb].title|escape}" /></td>
  <td><input type="text" size="45" name="posted_data[{%sb.index%}][link]" value="{$speed_bar[sb].link|escape}" /></td>
  <td align="center"><input type="checkbox" name="posted_data[{%sb.index%}][active]" value="Y"{if $speed_bar[sb].active eq "Y"} checked="checked"{/if} /></td>
</tr>
{/section}

<tr>
  <td colspan="5">&nbsp;</td>
</tr>

<tr>
  <td colspan="5" nowrap="nowrap"><input type="button" value="{$lng.lbl_delete_selected}" onclick="javascript: if(checkMarks(this.form, new RegExp('to_delete', 'gi'))) submitForm(this, 'delete')" />&nbsp;<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>

{else}

<tr>
  <td colspan="5" align="center">{$lng.lbl_no_links_defined}</td>
</tr>

{/if}

<tr>
  <td colspan="5">&nbsp;</td>
</tr>

<tr>
  <td colspan="5">{include file="main/subheader.tpl" title=$lng.lbl_add_link}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><input type="text" size="3" maxlength="5" name="new_orderby" /></td>
  <td><input type="text" size="45" name="new_title" /></td>
  <td><input type="text" size="45" name="new_link" value="{$http_location}/" /></td>
  <td align="center"><input type="checkbox" name="new_active" value="Y" checked="checked" /></td>
</tr>

<tr>
  <td colspan="5" class="SubmitBox"><input type="button" value="{$lng.lbl_add}" onclick="javascript: submitForm(this, 'add')" /></td>
</tr>

</table>
</form>

{/capture} 
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_speed_bar_management extra='width="100%"'}
