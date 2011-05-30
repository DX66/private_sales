{*
$Id: extra_fields.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_extra_fields_title}

{$lng.txt_extra_fields_desc}

<br /><br />

{capture name=dialog}

{if $extra_fields}

{include file="main/language_selector.tpl" script="extra_fields.php?"}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'extrafieldsform';
checkboxes = new Array({foreach from=$extra_fields item=v key=k}'posted_data[{$k}][to_delete]',{/foreach}'');
 
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="extra_fields.php" method="post" name="extrafieldsform">
<input type="hidden" name="mode" value="update" />

<table cellpadding="3" cellspacing="1">

<tr class="TableHead">
  <td>&nbsp;</td>
  <td>{$lng.lbl_extra_fields_name}</td>
  <td>{$lng.lbl_service_name}</td>
  <td>{$lng.lbl_extra_fields_default_value}</td>
  <td align="center">{$lng.lbl_extra_fields_show}</td>
  <td align="center">{$lng.lbl_orderby}</td>
</tr>

{if $extra_fields}

{foreach from=$extra_fields item=field key=id}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="posted_data[{$id}][to_delete]" /></td>
  <td><input type="text" name="posted_data[{$id}][field]" size="35" value="{$field.field|escape:"html"}" /></td>
  <td><input type="text" name="posted_data[{$id}][service_name]" maxlength="32" size="15" value="{$field.service_name|escape}" /></td>
  <td><input type="text" name="posted_data[{$id}][value]" size="20" value="{$field.value|escape:"html"}" /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$id}][active]" value="Y"{if $field.active eq "Y"} checked="checked"{/if} /></td>
  <td><input type="text" name="posted_data[{$id}][orderby]" size="3" value="{$field.orderby}" /></td>
</tr>

{/foreach}

<tr>
  <td colspan="4" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('posted_data\\[[0-9]+\\]\\[to_delete\\]', 'gi'))) submitForm(this, 'delete');" />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

{else}

<tr>
  <td colspan="4" align="center">{$lng.lbl_extra_fields_not_defined}</td>
</tr>

{/if}

{if $single_mode ne "" or $count_extra_fields lt $config.Extra_Fields.extra_fields_limit}

<tr>
  <td colspan="6"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_add_extra_field}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><input type="text" name="new[field]" size="35" /></td>
  <td><input type="text" name="new[service_name]" maxlength="32" size="15" value="{$max_service_name|escape}" /></td>
  <td><input type="text" name="new[value]" size="20" /></td>
  <td align="center"><input type="checkbox" name="new[active]" value="Y" checked="checked" /></td>
  <td><input type="text" name="new[orderby]" size="3" value="" /></td>
</tr>

<tr>
  <td colspan="4" class="SubmitBox"><input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'add');" /></td>
</tr>

{/if}

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_extra_fields_title content=$smarty.capture.dialog extra='width="100%"'}
