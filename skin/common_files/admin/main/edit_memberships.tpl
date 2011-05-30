{*
$Id: edit_memberships.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<a name="mem_lvl_{$type}"></a>
<form method="post" action="memberships.php" name="form{$type}">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="add[area]" value="{$type}" />

<table cellpadding="3" cellspacing="1" width="90%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="50%">{$lng.lbl_membership}</td>
  <td width="10%">{$lng.lbl_active}</td>
  <td width="10%">{$lng.lbl_orderby}</td>
  {if $type eq 'A' or $type eq 'P'}<td>{$lng.lbl_m_type}</td>{/if}
  <td width="30%" nowrap="nowrap" align="center">{$lng.lbl_assigned_users}</td>
</tr>

{foreach from=$levels item=v}
<tr{cycle name=$type values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="to_delete[]" value="{$v.membershipid}" /></td>
  <td><input type="text" size="30" name="posted_data[{$v.membershipid}][membership]" value="{$v.membership|escape}" /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$v.membershipid}][active]" value="Y"{if $v.active eq 'Y'} checked="checked"{/if} /></td>
  <td align="center"><input type="text" size="5" name="posted_data[{$v.membershipid}][orderby]" value="{$v.orderby}" /></td>
  {if $type eq 'A' or $type eq 'P'}
  <td>
  <select name="posted_data[{$v.membershipid}][flag]">
    <option value="">{$lng.lbl_none}</option>
{if $type eq 'A' or ($type eq 'P' and $active_modules.Simple_Mode)}
    <option value="FS"{if $v.flag eq 'FS'} selected="selected"{/if}>{$lng.lbl_subtype_FS}</option>
{/if}
{if $type eq 'P' and not $active_modules.Simple_Mode}
    <option value="RP"{if $v.flag eq 'RP'} selected="selected"{/if}>{$lng.lbl_subtype_RP}</option>
{/if}
  </select>
  </td>
  {/if}
  <td align="center">{$v.users|default:$lng.txt_not_available}</td>
</tr>
{/foreach}

{if $levels ne ''}

<tr>
  <td colspan="{if $type eq 'A' or $type eq 'P'}6{else}5{/if}" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("to_delete", "ig"))) {ldelim}document.form{$type}.mode.value="delete"; document.form{$type}.submit();{rdelim}' />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

{else}

<tr>
  <td colspan="{if $type eq 'A' or $type eq 'P'}6{else}5{/if}" align="center">{$lng.txt_no_memberships_defined}</td>
</tr>

{/if}

<tr>
  <td colspan="{if $type eq 'A' or $type eq 'P'}6{else}5{/if}"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><input type="text" size="30" name="add[membership]" /></td>
  <td align="center"><input type="checkbox" name="add[active]" value="Y" checked="checked" /></td>
  <td align="center"><input type="text" size="5" name="add[orderby]" value="" /></td>
  {if $type eq 'A' or $type eq 'P'}
  <td>
  <select name="add[flag]">
    <option value="">{$lng.lbl_none}</option>
{if $type eq 'A' or ($type eq 'P' and $active_modules.Simple_Mode)}
    <option value="FS">{$lng.lbl_subtype_FS}</option>
{/if}
{if $type eq 'P' and not $active_modules.Simple_Mode}
    <option value="RP">{$lng.lbl_subtype_RP}</option>
{/if}
  </select>
  </td>
  {/if}
  <td><input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'add');" /></td>
</tr>

</table>

</form>

{/capture} 
{include file="dialog.tpl" content=$smarty.capture.dialog title=$title extra='width="100%"'}
