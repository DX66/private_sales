{*
$Id: pconf_classification.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Product_Configurator ne ""}
<a name="product_classification"></a>

{$lng.txt_pconf_classification_top_text}

<br /><br />

{capture name=dialog}
<form action="product_modify.php" method="post" name="modifypclass">
<input type="hidden" name="mode" value="update_classification" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellpadding="3" cellspacing="1" width="100%">

{if $classes}
{assign var="specification_note_required" value=0}
{section name=cl loop=$classes}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead" valign="top" align="center" style="padding-top: 3px;"><input type="checkbox" value="Y" name="fields[classes][{$classes[cl].classid}]" /></td>{/if}
  <td>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td colspan="3">

<table cellpadding="0" cellspacing="1" width="100%">

<tr>
  <td width="10"><input type="checkbox" name="posted_data[{$classes[cl].classid}][delete]" title="{$lng.lbl_pconf_tick_to_unset|escape}" /></td>
  <td class="AdminTitle">{$classes[cl].ptype_name|escape}</td>
</tr>

<tr>
  <td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
  <td valign="top" colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

</table>

  </td>
</tr>

<tr>
  <td colspan="2" class="TableHead" nowrap="nowrap" width="50%"><b>{$lng.lbl_pconf_specifications_for_product}:</b></td>
  <td class="TableHead" nowrap="nowrap" width="50%"><b>{$lng.lbl_pconf_requirements_for_product}:</b></td>
</tr>

<tr>
  <td width="1%">&nbsp;</td>
  <td valign="top">
{if $classes[cl].specifications}
{assign var="select_box" value=1}
{assign var="specification_is_selected" value=0}
  <table>
  <tr>
    <td>
<select name="posted_data[{$classes[cl].classid}][specifications][]" multiple="multiple" size="{count value=$classes[cl].specifications}">
{foreach from=$classes[cl].specifications item=s}
  <option value="{$s.specid}"{if $s.selected} selected="selected"{/if}>{$s.spec_name}</option>
  {if $s.selected}
    {assign var="specification_is_selected" value=1}
  {/if}
{/foreach}
</select>
    </td>
{if not $specification_is_selected}
    <td valign="top">
    <font color="red">(*)</font> {$lng.lbl_pconf_see_note_below}.
    {assign var="specification_note_required" value=1}
    </td>
{/if}
  </tr>
  </table>
{else}
{$lng.txt_pconf_no_specifications}
{/if}
  </td>
  <td valign="top">

<table cellpadding="1" cellspacing="0" width="100%">

{if $classes[cl].req_types}

{section name=rt loop=$classes[cl].req_types}

<tr>
  <td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
  <td width="10"><input type="checkbox" name="posted_data[{$classes[cl].classid}][req_types][{$classes[cl].req_types[rt].ptypeid}][delete]" title="{$lng.lbl_pconf_tick_to_unset|escape}" /></td>
  <td><b>{$classes[cl].req_types[rt].ptype_name|escape}</b></td>
</tr>

<tr>
  <td colspan="2" bgcolor="#000000"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>

<tr>
  <td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>

</table>

  </td>
</tr>

<tr>
  <td>&nbsp;</td>
{if $classes[cl].req_types[rt].specifications}
  <td>
{assign var="select_box" value=1}
<input type="hidden" name="posted_data[{$classes[cl].classid}][req_types][{$classes[cl].req_types[rt].ptypeid}][ptypeid]" value="{$classes[cl].req_types[rt].ptypeid}" />
  <select name="posted_data[{$classes[cl].classid}][req_types][{$classes[cl].req_types[rt].ptypeid}][specifications][]" multiple="multiple" size="5">
{section name=sp loop=$classes[cl].req_types[rt].specifications}
    <option value="{$classes[cl].req_types[rt].specifications[sp].specid}"{if $classes[cl].req_types[rt].specifications[sp].selected} selected="selected"{/if}>{$classes[cl].req_types[rt].specifications[sp].spec_name}</option>
{/section}
  </select>
{else}
{$lng.txt_pconf_no_specifications}
{/if}
  </td>
</tr>

{/section}

{/if}

<tr>
  <td colspan="2"><br /><b>{$lng.lbl_pconf_add_product_type}:</b>
  <select name="posted_data[{$classes[cl].classid}][new_reqtype]">
    <option value="">&nbsp;</option>
{section name=pt loop=$product_types}
    <option value="{$product_types[pt].ptypeid}">{$product_types[pt].ptype_name|escape}</option>
{/section}
  </select>
  </td>
</tr>

</table>

  </td>
</tr>

<tr>
  <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>
</table>

  </td>
</tr>
{/section}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3">
    <b>{$lng.txt_notes}:</b><br />
    {$lng.txt_pconf_classifications_note_1}<br />
    {if $select_box}{$lng.txt_pconf_classifications_note_2}<br />{/if}
    <br />
  </td>
</tr>

{else}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead" valign="top" align="center" style="padding-top: 3px;">&nbsp;</td>{/if}
  <td>{$lng.txt_pconf_no_assigned_product_types}</td>
</tr>

{/if}

{if $specification_note_required}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2">
  <font color="red">(*)</font> {$lng.lbl_pconf_no_spec_warning}
  </td>
</tr>
{/if}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead" align="center"><input type="checkbox" value="Y" name="fields[new_type]" /></td>{/if}
  <td>
{if $product_types and $is_free_types eq "Y"}
<b>{$lng.lbl_pconf_add_product_type}:</b>
<select name="new_type">
  <option value="">&nbsp;</option>
{foreach from=$product_types item=v}
{if $v.is_exist ne 'Y'}
  <option value="{$v.ptypeid}">{$v.ptype_name|escape}</option>
{/if}
{/foreach}
</select>
{elseif not $product_types}
{if $usertype eq 'A'}
{$lng.txt_pconf_need_to_define_product_types_admin}
{else}
{$lng.txt_pconf_need_to_define_product_types|substitute:"link":"`$catalogs.provider`/pconf.php?mode=types"}
{/if}
{/if}
  </td>
</tr>

{if $product_types}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><br />
{$lng.txt_pconf_classifications_update_note}
<br /><br />
<input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
  </td>
</tr>
{/if}

</table>
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_pconf_product_classification extra='width="100%"'}
{/if}
