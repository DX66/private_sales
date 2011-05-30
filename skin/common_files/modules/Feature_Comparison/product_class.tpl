{*
$Id: product_class.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Feature_Comparison ne ""}

{if $script_name eq ''}{assign var="script_name" value="product_modify.php"}{/if}

{capture name=dialog}
<form action="{$script_name}" method="post" name="productclassassignform">
<input type="hidden" name="section" value="feature_class" />
<input type="hidden" name="mode" value="product_class_assign" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellspacing="0" cellpadding="3">
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead" valign="top"><input type="checkbox" value="Y" name="fields[fclass]" /></td>{/if}
  <td nowrap="nowrap">{$lng.lbl_feature_class}:</td>
  <td>&nbsp;</td>
  <td width="100%"><select name="fclassid">
  <option value=""{if $product.fclassid eq ''} selected="selected"{/if}>{$lng.lbl_undefined}</option>
  {foreach from=$fc_classes item=v}
  <option value="{$v.fclassid}"{if $v.fclassid eq $product.fclassid} selected="selected"{/if}>{$v.class}</option>
  {/foreach}
  </select></td>
</tr>
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead" valign="top">&nbsp;</td>{/if}
  <td colspan="2">&nbsp;</td>
  <td><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_assign_feature_class content=$smarty.capture.dialog extra='width="100%"'}

{if $product.features.options ne ''}
<br />

{capture name=dialog}
<form action="{$script_name}" method="post" name="productclassform">
<input type="hidden" name="section" value="feature_class" />
<input type="hidden" name="mode" value="product_class_modify" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellspacing="0" cellpadding="3">
{foreach from=$product.features.options item=v}
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead" valign="top"><input type="checkbox" value="Y" name="fields[foptions][{$v.foptionid}]" /></td>{/if}
  <td nowrap="nowrap" valign="top">{$v.option_name}:</td>
  <td>&nbsp;</td>
  <td width="100%">
  {if ($v.option_type eq 'T' and $v.format ne 'T') or $v.option_type eq 'N'}
  <input type="text" name="options[{$v.foptionid}]" value="{$v.value|escape}" />
  {elseif $v.option_type eq 'T'}
  <textarea name="options[{$v.foptionid}]" rows="5" cols="40">{$v.value|escape}</textarea>
  {elseif $v.option_type eq 'S' or $v.option_type eq 'M'}
  {if $v.option_type eq 'S'}
  <select name="options[{$v.foptionid}]">
  {else}
  <select name="options[{$v.foptionid}][]" size="5" multiple="multiple">
  {/if}
  {foreach from=$v.variants item=o key=ko}
  <option value="{$ko}"{if $o.selected ne ''} selected="selected"{/if}>{$o.variant_name}</option>
  {/foreach}
  </select>
  {if $v.option_type eq 'M'}<br />{$lng.lbl_hold_ctrl_key}{/if}
  {elseif $v.option_type eq 'D'}
  {include file="main/datepicker.tpl" name="options[`$v.foptionid`]" id="options_`$v.foptionid`" date=$v.value time=$v.value end_year="c+5" start_year="c-5"}
  {elseif $v.option_type eq 'B'}
  <select name="options[{$v.foptionid}]">
  <option value='Y'{if $v.value eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=''{if $v.value ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
  {/if}
  </td>
</tr>
{/foreach}
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead" valign="top">&nbsp;</td>{/if}
  <td colspan="2">&nbsp;</td>
  <td><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_feature_class_options content=$smarty.capture.dialog extra='width="100%"'}
{/if}
{/if}
