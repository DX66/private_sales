{*
$Id: add_product_options.tpl,v 1.4 2010/06/08 07:32:34 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Product_Options ne ""}
<script type="text/javascript">
//<![CDATA[
var requiredFieldsPO = new Array();
requiredFieldsPO[0] = new Array('add_class', '{$lng.lbl_option_group_name|replace:"'":"\'"}');
requiredFieldsPO[1] = new Array('add_classtext', '{$lng.lbl_option_text|replace:"'":"\'"}');

{literal}
function visibleTA(obj) {
  var objTA = document.getElementById('product_options_list');
  if (!obj || !objTA)
    return false;

  objTA.disabled = (obj.options[obj.selectedIndex].value == 'T');
}
{/literal}
//]]>
</script>
{include file="check_required_fields_js.tpl"}
{if $script_name eq ''}{assign var="script_name" value="product_modify.php"}{/if}

{capture name=dialog}
{if $product_options ne ''}
<table cellspacing="0" cellpadding="0">
<tr>
  <td class="ButtonsRow">
{include file="buttons/button.tpl" href="product_modify.php?mode=return&section=options&productid=`$product.productid``$redirect_geid`" button_title=$lng.lbl_back_to_option_groups_list}
  </td>
{if $product_option ne ''}
  <td>
{include file="buttons/button.tpl" href="product_modify.php?submode=product_options_add&section=options&productid=`$product.productid``$redirect_geid`" button_title=$lng.lbl_add_option}
  </td>
{/if}
</tr>
</table>
{/if}
<form action="{$script_name}" method="post" name="optionform" onsubmit="javascript: return checkRequired(requiredFieldsPO);">
<input type="hidden" name="section" value="options" />
<input type="hidden" name="mode" value="product_options_add" />
<input type="hidden" name="classid" value="{$product_option.classid}" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

{if $product_option ne ''}
<div align="right">
{include file="main/language_selector.tpl" script="`$script_name`?productid=`$product.productid`&section=options&classid=`$product_option.classid`&"}
</div>
{/if}

<table width="100%" cellspacing="0" cellpadding="3">
<tr>
{if $geid ne ''}
{if $product_option ne ''}
<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[class]" /></td>
{else}
<td width="15" class="TableSubHead" rowspan="13"><input type="checkbox" value="Y" name="fields[new_class]" /></td>
{/if}
{/if}
  <td><b>{$lng.lbl_option_group_name}:</b></td>
  <td><font class="Star">*</font></td>
  <td><input type="text" size="50" maxlength="128" id="add_class" name="add[class]" value="{$product_option.class|escape}" /></td>
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3" class="DataField">{$lng.txt_option_group_name_note}</td>
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[classtext]" /></td>{/if}
    <td><b>{$lng.lbl_option_text}:</b></td>
  <td><font class="Star">*</font></td>
    <td><input type="text" size="50" maxlength="255" id="add_classtext" name="add[classtext]" value="{$product_option.classtext|escape}" /></td> 
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3" class="DataField">{$lng.txt_option_group_comment_note}</td>
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[is_modifier]" /></td>{/if}
    <td class="DataField"><b>{$lng.lbl_option_group_type}:</b></td>
  <td>&nbsp;</td>
    <td class="DataField"><select name="add[is_modifier]"{if $product_option eq ''} onchange="javascript: visibleTA(this);"{/if}>
  <option value='Y'{if $product_option.is_modifier eq 'Y'} selected="selected"{/if}>{$lng.lbl_modificator}</option>
{if $product.product_type ne 'C'}
  <option value=''{if $product_option.is_modifier eq '' and $product_option.classid gt 0} selected="selected"{/if}>{$lng.lbl_variant}</option>
{/if}
  <option value='T'{if $product_option.is_modifier eq 'T' and $product_option.classid gt 0} selected="selected"{/if}>{$lng.lbl_text_field}</option>
  <option value='A'{if $product_option.is_modifier eq 'A' and $product_option.classid gt 0} selected="selected"{/if}>{$lng.lbl_text_area}</option>

  </select></td> 
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[orderby]" /></td>{/if}
    <td class="DataField"><b>{$lng.lbl_orderby}:</b></td>
  <td>&nbsp;</td>
    <td class="DataField"><input type="text" size="5" maxlength="11" name="add[orderby]" value="{$product_option.orderby}" /></td> 
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[avail]" /></td>{/if}
    <td class="DataField"><b>{$lng.lbl_availability}:</b></td>
  <td>&nbsp;</td>
    <td class="DataField"><select name="add[avail]">
    <option value="Y"{if $product_option.avail eq 'Y' or $product_option.classid eq ''} selected="selected"{/if}>{$lng.lbl_enabled}</option>
    <option value=""{if $product_option.avail ne 'Y' and $product_option ne ''} selected="selected"{/if}>{$lng.lbl_disabled}</option>
  </select></td>
</tr>
<tr>
  {if $geid ne '' and $product_option ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[options]" /></td>{/if}
    <td valign="top"><b>{$lng.lbl_options_list}:</b>{if $product_option eq ''}<br />{$lng.txt_options_list_note}{/if}</td>
  <td>&nbsp;</td>
    <td valign="top">
  {if $product_option eq ''} 
  <textarea name="list" cols="60" rows="10" id="product_options_list"></textarea>
  {elseif $product_option.is_modifier ne 'T' and $product_option.is_modifier ne 'A'}
  <table>
  <tr class="TableHead">
    <td width="10">&nbsp;</td>
    <td>{$lng.lbl_option_value}</td>
    <td>{$lng.lbl_orderby}</td>
    <td>{$lng.lbl_availability}</td>
{if $product_option.is_modifier eq 'Y'}
    <td nowrap="nowrap" colspan="2">{$lng.lbl_option_surcharge}</td>
{/if}
  </tr>
  {if $product_option.options ne ''}
  {foreach from=$product_option.options item=o}
  <tr{cycle name="options" values=', class="TableSubHead"'}>
    <td><input type="checkbox" name="to_delete[{$o.optionid}]" value="Y" /></td>
    <td><input type="text" name="list[{$o.optionid}][option_name]" value="{$o.option_name|escape}" /></td>
    <td><input type="text" name="list[{$o.optionid}][orderby]" size="5" maxlength="11" value="{$o.orderby}" /></td>
    <td align="center"><input type="checkbox" name="list[{$o.optionid}][avail]" value="Y"{if $o.avail eq 'Y'} checked="checked"{/if} /></td>
{if $product_option.is_modifier eq 'Y'}
    <td><input type="text" name="list[{$o.optionid}][price_modifier]" size="5" value="{$o.price_modifier|formatprice}" /></td>
    <td><select name="list[{$o.optionid}][modifier_type]">
    <option value="$"{if $o.modifier_type eq '$'} selected="selected"{/if}>{$lng.lbl_absolute}</option>
    <option value="%"{if $o.modifier_type eq '%'} selected="selected"{/if}>{$lng.lbl_percent}</option>
    </select></td>
{/if}
  </tr>
  {/foreach}
  <tr>
    <td colspan="{if $product_option.is_modifier eq 'Y'}6{else}4{/if}"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("to_delete", "gi"))) {ldelim}document.optionform.mode.value="product_option_delete"; document.optionform.submit();{rdelim}' /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  {/if}
  <tr>
    <td class="TopLabel" colspan="{if $product_option.is_modifier eq 'Y'}6{else}4{/if}">{include file="main/subheader.tpl" title=$lng.lbl_add_option_value}</td>
  </tr>
  <tr>
    <td id="popt_box_0">&nbsp;</td>
    <td id="popt_box_1"><input type="text" name="new_list[option_name][0]" /></td>
    <td id="popt_box_2"><input type="text" name="new_list[orderby][0]" size="5" maxlength="11" /></td>
    <td id="popt_box_3"><input type="checkbox" name="new_list[avail][0]" value="Y" checked="checked" /></td>
{if $product_option.is_modifier eq 'Y'}
    <td id="popt_box_4"><input type="text" name="new_list[price_modifier][0]" size="5" value="{$zero}" /></td>
    <td id="popt_box_5"><select name="new_list[modifier_type][0]">
    <option value="$" selected="selected">{$lng.lbl_absolute}</option>
    <option value="%">{$lng.lbl_percent}</option>
    </select></td>
{/if}
    <td>{include file="buttons/multirow_add.tpl" mark="popt" is_lined=true}</td>
  </tr>
  </table>
  {elseif $product_option.is_modifier eq 'T' or $product_option.is_modifier eq 'A'}
  <font color="red">{$lng.txt_text_field_note}</font>
  {/if}
  </td>
</tr>
</table>
<br />
<br />
<input type="submit" value="{if $product_option eq ''}{$lng.lbl_add_option_group|strip_tags:false|escape}{else}{$lng.lbl_update_option_group|strip_tags:false|escape}{/if}" />
</form>
{if $product_option eq ''}
{assign var="dialog_title" value=$lng.lbl_add_option}
{else}
{assign var="dialog_title" value=$lng.lbl_update_option}
{/if}
{/capture}
{include file="dialog.tpl" title=$dialog_title content=$smarty.capture.dialog extra='width="100%"'}

{/if}
