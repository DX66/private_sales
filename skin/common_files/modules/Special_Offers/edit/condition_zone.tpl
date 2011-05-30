{*
$Id: condition_zone.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<input type="hidden" name="condition[{$condition.condition_type}][condition_data]" value="" />
<table cellpadding="3" cellspacing="1" width="100%">
{if $condition.params eq ""}
<tr>
  <td colspan="3">{$lng.txt_sp_empty_params_generic_edit}</td>
</tr>
{else}
<tr class="TableHead">
  <th width="10">&nbsp;</th>
  <th width="70%">{$lng.lbl_zone_name}</th>
  <th width="30%">{$lng.lbl_sp_zone_type}</th>
</tr>
{foreach name=params from=$condition.params item=param}
{if $smarty.foreach.params.first ne 1}
<tr class="TableSubHead">
  <td colspan="3" align="center">{$lng.lbl_sp_or}</td>
</tr>
{/if}
<tr>
  <td>
<input type="hidden" name="param[{$param.paramid}][param_type]" value="{$param.param_type}" />
<input type="checkbox" name="param_del[{$param.paramid}]" />
  </td>
  <td>{$param.zone_name}</td>
  <td align="center">
  <select name="param[{$param.paramid}][param_arg]"]>
    <option value="B"{if $param.param_arg eq "B"} selected="selected"{/if}>{$lng.lbl_sp_zone_billing}</option>
    <option value="S"{if $param.param_arg eq "S"} selected="selected"{/if}>{$lng.lbl_sp_zone_shipping}</option>
  </select>
  </td>
</tr>
{/foreach}
<tr>
  <td colspan="3" class="SubmitBox">
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^param_del', 'gi'))) {ldelim}document.wizardform.action.value='delete';document.wizardform.submit();{rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>
{/if}

{* NEW PARAM *}
<tr>
 <td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_sp_add_param}</td>
</tr>

<tr>
 <td>&nbsp;</td>
 <td colspan="2" nowrap="nowrap">
 {$lng.lbl_zone}:
 <select name="new_param_{$condition.condition_type}_zoneid">
   <option value="">{$lng.lbl_please_select_one}</option>
{foreach from=$zones item=zone}
     <option value="{$zone.zoneid}">{$zone.zone_name}</option>
{/foreach}
 </select>
 &nbsp;&nbsp;<input type="button" value=" {$lng.lbl_add|strip_tags:false|escape} " onclick="document.wizardform.action.value='add';document.wizardform.submit();" />
 </td>
</tr>
</table>
