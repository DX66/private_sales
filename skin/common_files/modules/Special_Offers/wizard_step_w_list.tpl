{*
$Id: wizard_step_w_list.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/popup_product.js"></script>
<script type="text/javascript" src="{$SkinDir}/js/popup_category.js"></script>

{* declare js *}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var items_def = new Array();
{foreach name=items from=$items item=item key=item_type}
items_def['{$item_type}'] = new Array();
items_def['{$item_type}'][1] = 'item_cb_{$item_type}';
items_def['{$item_type}'][2] = 'item_lbl_{$item_type}';
items_def['{$item_type}'][3] = 'item_box_{$item_type}';
items_def['{$item_type}'][4] = 'item_status_{$item_type}';
{/foreach}

var active_row_id = 'item_row_{$last_item_type}';
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/modules/Special_Offers/wizard_step_w_list.js"></script>

<input type="hidden" name="last_item_type" value="{$last_item_type}" />

{if $mode eq "conditions"}
{assign var="item_def_file" value="modules/Special_Offers/condition_names.tpl"}
{else}
{assign var="item_def_file" value="modules/Special_Offers/bonus_names.tpl"}
{/if}

{* draw list *}
<table bgcolor="white" cellpadding="4" cellspacing="0" width="100%">
{foreach name=items from=$items item=item key=item_type}
{assign var="item_cb_id" value="item_cb_`$item_type`"}
<tr id="item_row_{$item_type}" onmouseover="javascript: select_row(this,true);" onmouseout="javascript: select_row(this,false);"{if $last_item_type eq $item_type} class="SubHeaderGreyLine" style="font-weight: bold;"{/if}>
  <td width="1%">
<input type="checkbox" id="{$item_cb_id}" name="{$item_cb_id}" onclick="javascript: select_item(1,'{$item_type}');"{if $item.avail eq "Y"} checked="checked"{/if} />
  </td>
  <td align="left" width="99%" onclick="javascript: select_item(2,'{$item_type}');">
<a class="VertMenuItems" href="javascript:void(0);" id="item_lbl_{$item_type}"{if $item.selected}style="font-weight: bold;" {/if}>
{include file=$item_def_file item_type=$item_type}
<span id="item_status_{$item_type}_box" {if $item.avail ne "Y" or $last_item_type eq $item_type}style="display: none;"{/if}>{if $item.valid eq ""}(<font style="color: red;">{$lng.lbl_sp_offer_status_fail}</font>){/if}</span>
</a>
  </td>
</tr>
{/foreach}
</table>

{* draw edit boxes *}
<table cellpadding="0" cellspacing="0" width="100%">
{foreach name=items from=$items item=item key=item_type}
<tr id="item_box_{$item_type}" {if $last_item_type ne $item_type}style="display: none;"{/if}>
  <td colspan="3">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td colspan="2"><hr class="Line" size="1" /></td>
</tr>
<tr>
  <td width="5">&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td width="100%">
    <br />
{include file="main/subheader.tpl" title=$lng.lbl_sp_edit_parameters class="grey"}

{include file=$item_def_file item_type=$item_type action="include" item=$item}
  </td>
</tr>
</table>

  </td>
</tr>

{/foreach}
</table>

