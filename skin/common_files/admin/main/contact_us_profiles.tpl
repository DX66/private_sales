{*
$Id: contact_us_profiles.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="3" cellspacing="1" width="100%">

<tr>
<td>

<form action="configuration.php" method="post">
<input type="hidden" name="option" value="{$option}" />
<input type="hidden" name="mode" value="update_status" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td rowspan="2" width="30%" nowrap="nowrap">{$lng.lbl_field_name}</td>
{foreach from=$usertypes_array item=to_disable key=utype}
  <td align="center">
  {if $utype eq "B"}{$lng.lbl_partner}{elseif $utype eq "P"}{$lng.lbl_provider}{else}{$lng.lbl_customer}{/if}
  </td>
{/foreach}
</tr>

<tr class="TableHeadLevel2">
{foreach from=$usertypes_array item=to_disable key=utype}
  <td width="{$col_width}%" align="center" nowrap="nowrap">{$lng.lbl_active} / {$lng.lbl_required}</td>
{/foreach}
</tr>

{foreach from=$default_fields item=item key=field}

<tr{cycle values=", class='TableSubHead'"}>
  <td>
  {$item.title}
  <input type="hidden" name="default_data[{$item.field}][flag]" value="Y" />
  </td>
{foreach from=$usertypes_array item=to_disable key=utype}
  <td align="center">
  <input type="checkbox" onclick="javascript: document.getElementById('dr_{$item.field}_{$utype}').disabled = !this.checked;" name="default_data[{$item.field}][avail][{$utype}]"{if $item.avail.$utype eq "Y"} checked="checked"{/if} />
  &nbsp;/&nbsp;
  <input type="checkbox" id="dr_{$item.field}_{$utype}" name="default_data[{$item.field}][required][{$utype}]"{if $item.required.$utype eq "Y"} checked="checked"{/if}{if $item.avail.$utype ne "Y"} disabled="disabled"{/if} />
  </td>
{/foreach}
</tr>

{/foreach}

{if $additional_fields ne ''}
<tr> 
  <td colspan="{$colspan}"><br />{include file="main/subheader.tpl" title=$lng.lbl_additional_information class="grey"}</td>
</tr> 
{foreach from=$additional_fields item=v key=k}
<tr{cycle values=", class='TableSubHead'"}>
  <td>{$v.title|default:$v.field}</td>
{foreach from=$usertypes_array item=to_disable key=utype}
  <td align="center">  
  <input type="checkbox" onclick="javascript: document.getElementById('ar_{$v.fieldid}_{$utype}').disabled = !this.checked;" name="add_data[{$v.fieldid}][avail][{$utype}]"{if $v.avail.$utype eq "Y"} checked="checked"{/if} />
  &nbsp;/&nbsp;
  <input id="ar_{$v.fieldid}_{$utype}" type="checkbox" name="add_data[{$v.fieldid}][required][{$utype}]"{if $v.required.$utype eq "Y"} checked="checked"{/if}{if $v.avail.$utype ne "Y"} disabled="disabled"{/if} />
  </td>
{/foreach}
</tr>
{/foreach}
{/if}

<tr>
  <td colspan="{$colspan}"><br />
  <input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " />
  </td>
</tr>

</table>
</form>
<br /><br />

<form action="configuration.php" method="post" name="fieldsform">
<input type="hidden" name="option" value="{$option}" />
<input type="hidden" name="mode" value="update_fields" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td colspan="4"><br />{include file="main/subheader.tpl" title=$lng.lbl_additional_fields}</td>
</tr>

<tr class="TableHead">
  <td>&nbsp;</td>
  <td nowrap="nowrap">{$lng.lbl_field_name}</td>
  <td>{$lng.lbl_type}</td>
  <td nowrap="nowrap">{$lng.lbl_pos}</td>
</tr>

{if $additional_fields}
{foreach from=$additional_fields item=v}
<tr>
  <td><input type="checkbox" name="fields[{$v.fieldid}]" value="Y" /></td>
  <td><input type="text" size="30" maxlength="100" name="update[{$v.fieldid}][field]" value="{$v.title|default:$v.field}" /></td>
  <td><select name="update[{$v.fieldid}][type]">
  {foreach from=$types item=t key=k}
  <option value="{$k}"{if $v.type eq $k} selected="selected"{/if}>{$t}</option>
  {/foreach}
  </select></td>
  <td><input type="text" name="update[{$v.fieldid}][orderby]" value="{$v.orderby}" size="5" /></td>
</tr>
{if $v.type eq 'S'}
<tr>
    <td>&nbsp;</td>
    <td colspan="3"><input type="text" size="60" name="update[{$v.fieldid}][variants]" value="{$v.variants}" /></td>
</tr>
{/if}
{/foreach}

<tr>
  <td colspan="4"><br />
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('fields\\[[0-9]+\\]', 'ig'))) {ldelim} document.fieldsform.mode.value='delete';document.fieldsform.submit();{rdelim}" />
  </td>
</tr>

{else}

<tr>
  <td colspan="4" align="center">{$lng.txt_no_additional_fields}</td>
</tr>

{/if}

<tr>
  <td colspan="4"><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new_field}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><input type="text" name="newfield" size="30" maxlength="100" /></td>
  <td>
  <select name="newfield_type">
  {foreach from=$types item=v key=k}
  <option value="{$k}">{$v}</option>
  {/foreach}
  </select>
  </td>
  <td><input type="text" size="5" name="newfield_orderby" /></td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td colspan="3">{$lng.lbl_variants_for_selectbox}:</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td colspan="3"><input type="text" size="60" name="newfield_variants" /></td>
</tr> 

<tr>
  <td colspan="4"><br />
  <input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>

</td>
</tr>
</table>

