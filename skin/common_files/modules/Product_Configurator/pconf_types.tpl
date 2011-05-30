{*
$Id: pconf_types.tpl,v 1.3 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="main/multirow.tpl"}

<br />
{$lng.txt_pconf_types_desc}
<br />
{capture name=dialog}

<script type="text/javascript">
//<![CDATA[
var lbl_pconf_delete_selected_alert = "{$lng.lbl_pconf_delete_selected_alert|wm_remove|escape:javascript}"
//]]></script>

{if $product_types}
<div align="right">
<form action="pconf.php" method="post" name="prtypesform">
<input type="hidden" name="mode" value="types" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
<input type="hidden" name="action" value="update_page" />

{$lng.lbl_pconf_items_per_page}:
<select name="ptypes_per_page" onchange="javascript: this.form.submit()">
{section name=pp loop=100 step=10 start=10}
{assign var=cur_ind value=$smarty.section.pp.index}
<option value="{$cur_ind}"{if $cur_ind eq $ptypes_per_page} selected="selected"{/if}>{$cur_ind}</option>
{/section}
</select>
</form>
</div>

{include file="main/navigation.tpl"}
<br />
{/if}

<form action="pconf.php" method="post" name="prtypesform">
<input type="hidden" name="mode" value="types" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="flag_delete" value="" />

<table cellpadding="3" cellspacing="1" width="100%">

{if $product_types}

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="10"><b>{$lng.lbl_pos}</b></td>
  <td width="50%"><b>{$lng.lbl_pconf_product_types}</b></td>
  <td width="50%"><b>{$lng.lbl_pconf_specifications}</b></td>
</tr>
{section name=pt loop=$product_types}
<tr>
  <td colspan="4">&nbsp;</td>
</tr>

<tr{cycle values=', class="TableSubHead"'}>
  <td width="10" valign="top"><input type="checkbox" name="posted_types[{$product_types[pt].ptypeid}][delete]" title="{$lng.lbl_pconf_ptype_tick_del_hint|escape}" /></td>
  <td valign="top"><input type="text" size="3" name="posted_types[{$product_types[pt].ptypeid}][orderby]" value="{$product_types[pt].orderby}" title="{$lng.lbl_pconf_ptype_pos_hint|escape}" /></td>
  <td valign="top"><input type="text" size="35" name="posted_types[{$product_types[pt].ptypeid}][ptype_name]" value="{$product_types[pt].ptype_name|escape}" title="{$lng.lbl_pconf_ptype_name_hint|escape}" /></td>
  <td> 

<table cellpadding="1" cellspacing="1">
  <tr class="TableHead">
    <td width="10">&nbsp;</td>
    <td>{$lng.lbl_name}</td>
    <td>{$lng.lbl_orderby}</td>
  </tr>
{section name=sp loop=$product_types[pt].specifications}
{assign var="spec" value=$product_types[pt].specifications[sp]}
  <tr>
    <td><input type="checkbox" name="posted_types[{$product_types[pt].ptypeid}][specifications][{$spec.specid}][delete]" title="{$lng.lbl_pconf_spec_tick_del_hint|escape}" /></td>
    <td><input type="text" size="35" name="posted_types[{$product_types[pt].ptypeid}][specifications][{$spec.specid}][spec_name]" value="{$spec.spec_name|escape}" title="{$lng.lbl_pconf_spec_name_hint|escape}" /></td>
    <td><input type="text" size="3" name="posted_types[{$product_types[pt].ptypeid}][specifications][{$spec.specid}][orderby]" value="{$spec.orderby}" title="{$lng.lbl_pconf_spec_pos_hint|escape}" /></td>
  </tr>
{/section}
  <tr>
    <td colspan="3">
{if $product_types[pt].specifications ne ""}
      <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^posted_types', 'gi')) &amp;&amp;confirm(lbl_pconf_delete_selected_alert)) {ldelim} this.form.flag_delete.value='{$product_types[pt].ptypeid}'; this.form.action.value='delete'; this.form.submit(); {rdelim}" />
{else}
      <center>{$lng.lbl_pconf_specifications_list_empty}</center>
{/if}
  </td>
</tr>
<tr>
  <td colspan="4">&nbsp;</td>
</tr>
<tr>
  <td class="TopLabel" colspan="3">{include file="main/subheader.tpl" title=$lng.lbl_pconf_spec_new}</td>
</tr>
<tr>
  <td id="ps_box_1">&nbsp;</td>
  <td id="ps_box_2"><input type="text" name="new_list[{$product_types[pt].ptypeid}][spec_name][0]" size="35" title="{$lng.lbl_pconf_spec_name_hint|escape}" /></td>
  <td id="ps_box_3"><input type="text" name="new_list[{$product_types[pt].ptypeid}][orderby][0]" size="3" maxlength="11" title="{$lng.lbl_pconf_spec_pos_hint|escape}" /></td>
  <td>{include file="buttons/multirow_add.tpl" mark="ps" is_lined=true}</td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td colspan="4">&nbsp;</td>
</tr>
{/section}
{/if}
</tr>

<tr>
  <td class="TopLabel" colspan="4">{include file="main/subheader.tpl" title=$lng.lbl_pconf_ptype_add_new}</td>
</tr>

<tr>
  <td colspan="4">
<table cellpadding="1" cellspacing="1">
  <tr class="TableHead">
    <td width="10">&nbsp;</td>
    <td>{$lng.lbl_name}</td>
    <td>{$lng.lbl_orderby}</td>
  </tr>
  <tr>
    <td id="pt_box_1">&nbsp;</td>
    <td id="pt_box_2"><input type="text" size="35" name="new_types[ptype_name][0]" title="{$lng.lbl_pconf_ptype_name_hint|escape}" /></td>
    <td id="pt_box_3"><input type="text" name="new_types[orderby][0]" size="5" maxlength="11" title="{$lng.lbl_pconf_ptype_pos_hint|escape}" /></td>
    <td>{include file="buttons/multirow_add.tpl" mark="pt" is_lined=true}</td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td colspan="3"><br /><br />
{if $product_types}
{$lng.lbl_pconf_tick_delete_note}
<br /><br />
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^posted_types', 'gi')) &amp;&amp;confirm(lbl_pconf_delete_selected_alert)) {ldelim} this.form.action.value='delete'; this.form.submit(); {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />
{else}
    <input type="submit" value="{$lng.lbl_pconf_ptype_add|strip_tags:false|escape}" />
{/if}
  </td>
</tr>

</table>
</form>

{if $product_types}
<br />
{include file="main/navigation.tpl"}

<div align="right">
<form action="pconf.php" method="post" name="prtypesform">
<input type="hidden" name="mode" value="types" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
<input type="hidden" name="action" value="update_page" />
{$lng.lbl_pconf_items_per_page}:
<select name="ptypes_per_page" onchange="javascript: this.form.submit()">
{section name=pp loop=100 step=10 start=10}
{assign var=cur_ind value=$smarty.section.pp.index}
<option value="{$cur_ind}"{if $cur_ind eq $ptypes_per_page} selected="selected"{/if}>{$cur_ind}</option>
{/section}
</select>
</form>
</div>
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_pconf_product_types content=$smarty.capture.dialog extra='width="100%"'}
