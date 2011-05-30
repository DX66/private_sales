{*
$Id: product_options.tpl,v 1.4 2010/06/08 07:32:34 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Product_Options ne ""}
{if $script_name eq ''}{assign var="script_name" value="product_modify.php"}{/if}

<a name="top"></a>
{capture name=dialog}
{$lng.txt_product_options_list_note}<br />
<br />
<strong>{$lng.lbl_note}:</strong> {$lng.txt_product_variants_warning}<br />
<br />

{if $product_options ne ''}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'productoptionsform';
checkboxes = new Array({foreach from=$product_options item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.classid}]'{/foreach});

var v_alert = "{$lng.txt_variant_alert|wm_remove|escape:javascript|replace:'"':'\"'|replace:"\n":""}";
var v_del_alert = "{$lng.txt_delete_variant_alert|wm_remove|escape:javascript|replace:'"':'\"'|replace:"\n":""}";
var del_variants = [];
var disabled_variants = [];
{foreach from=$product_options item=v key=k}
{if $v.is_modifier eq ''}
del_variants[{$v.classid}] = true;
{if $v.avail ne 'Y'}
disabled_variants[{$v.classid}] = true;
{/if}
{/if}
{/foreach}

{literal}
function variant_alert(obj, id) {
  if(!obj)
    return false
  if(!obj.checked && !disabled_variants[id])
    return confirm(v_alert);
  return true;
}

function variant_del_alert() {
  if (del_variants.length == 0)
    return true;

  for (var x in del_variants) {
    if (isNaN(x))
      continue;
    var n = document.productoptionsform.elements['to_delete['+x+']'];
    if (n && n.checked)
      return confirm(v_del_alert);
  }
  return true;
}

{/literal}
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>
{/if}

<form action="{$script_name}" method="post" name="productoptionsform">
<input type="hidden" name="section" value="options" />
<input type="hidden" name="mode" value="product_options_modify" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table {if $geid ne ''}cellspacing="0" cellpadding="4"{else}cellspacing="1" cellpadding="2"{/if} width="100%">

<tr class="TableHead"> 
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="10" class="DataTable">&nbsp;</td>
  <td class="DataTable">#</td>
  <td nowrap="nowrap" class="DataTable">{$lng.lbl_option_class}</td>
  <td nowrap="nowrap" class="DataTable">{$lng.lbl_option_type}</td>
  <td class="DataTable">{$lng.lbl_orderby}</td>
  <td class="DataTable">{$lng.lbl_availability}</td>
  <td width="70%">{$lng.lbl_options_list}</td>
</tr>
{foreach from=$product_options item=v}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
{if $geid ne ''}<td width="15" class="TableSubHead" valign="top"><input type="checkbox" value="Y" name="fields[classes][{$v.classid}]" /></td>{/if}
  <td valign="top" class="DataTable"><input type="checkbox" name="to_delete[{$v.classid}]" value="Y" /></td>
  <td valign="top" class="DataTable">{$v.classid}</td>
  <td valign="top" class="DataTable"><a href="{$script_name}?productid={$product.productid}&amp;classid={$v.classid}&amp;section=options{$redirect_geid}#modify_class">{$v.class}</a></td>
  <td valign="top" class="DataTable">{if $v.is_modifier eq 'Y'}{$lng.lbl_modificator}{elseif $v.is_modifier eq 'T'}{$lng.lbl_text_field}{elseif $v.is_modifier eq 'A'}{$lng.lbl_text_area}}{else}{$lng.lbl_variant}{/if}</td>
  <td valign="top" class="DataTable"><input type="text" name="po_classes[{$v.classid}][orderby]" size="5" maxlength="11" value="{$v.orderby}" /></td>
  <td align="center" valign="top"><input type="checkbox" name="po_classes[{$v.classid}][avail]" value="Y"{if $v.avail eq 'Y'} checked="checked"{/if}{if $v.is_modifier eq ''} onclick="javascript: return variant_alert(this, {$v.classid});"{/if} /></td>
  <td valign="top">

<table cellspacing="0" cellpadding="2">
  {if $v.options}
  {foreach from=$v.options item=o}
<tr>
    <td>{if $o.avail ne 'Y'}<font color="red">{/if}{$o.option_name}{if $o.avail ne 'Y'}</font>{/if}</td>
  {if $v.is_modifier eq 'Y' and $o.price_modifier ne 0}
    <td>{$o.price_modifier|formatprice}</td>
    <td>{if $o.modifier_type|default:"$" eq '$'}{$config.General.currency_symbol}{else}%{/if}</td>
  {/if}
</tr>
  {/foreach}
  {else}
<tr>
  <td colspan="{if $v.is_modifier eq 'Y'}3{else}1{/if}">{$lng.lbl_options_list_empty}</td>
</tr>
  {/if}
</table>

  </td>
</tr>
{foreachelse}
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td align="center" colspan="7">{$lng.lbl_product_options_list_empty}</td>
</tr>
{/foreach}
</table>
{if $product_options ne ''}
<br />
<table width="100%">
<tr>
  <td align="left"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if(checkMarks(this.form, new RegExp('to_delete', 'gi')) &amp;&amp; variant_del_alert()) {ldelim} document.productoptionsform.mode.value='product_options_delete'; document.productoptionsform.submit(); {rdelim}" />&nbsp;&nbsp;&nbsp;<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
  <td align="right"><input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='{$script_name}?submode=product_options_add&amp;productid={$product.productid}&amp;section=options{$redirect_geid}';" /></td>
</tr>
</table>
<br />
{/if}
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_option_groups content=$smarty.capture.dialog extra='width="100%"'}

<br />

{if $product_options ne ''}
<br />

<a name="exceptions"></a>
{capture name=dialog}
{$lng.txt_product_option_exceptions_note}<br />
<br />

{if $def_options_failure eq 'def_is_ex'}
<div class="ErrorMessage">{$lng.lbl_warning}: {$lng.txt_default_options_failure_note}</div>
<br />
{elseif $def_options_failure eq 'all_is_ex'}
<div class="ErrorMessage">{$lng.lbl_warning}: {$lng.txt_all_options_failure_note}</div>
<br />
{/if}

<form action="{$script_name}" method="post" name="exceptionform">
<input type="hidden" name="section" value="options" />
<input type="hidden" name="mode" value="product_options_ex_add" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table width="100%" cellspacing="0" cellpadding="3">
<tr class="TableHead">
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="10" class="DataTable">&nbsp;</td>
  <td>{$lng.lbl_options_combination}</td>
</tr>
{foreach from=$product_options_ex key=k item=o}
<tr{cycle name="exceptions" values=', class="TableSubHead"'}>
    {if $geid ne ''}<td width="15" class="TableSubHead" rowspan=""><input type="checkbox" value="Y" name="fields[exceptions][{$k}]" /></td>{/if}
  <td width="10" class="DataTable"><input type="checkbox" name="to_delete[{$k}]" /></td>
  <td>{foreach from=$o item=v}
    <span style="white-space: nowrap;">{$v.class}:&nbsp;
    {foreach from=$product_options item=c}
    {if $c.classid eq $v.classid}
      {foreach from=$c.options item=o}
      {if $o.optionid eq $v.optionid}{$o.option_name}{/if}
      {/foreach}
    {/if}
    {/foreach}
    </span>&nbsp;&nbsp;
  {/foreach}</td>
</tr>
{foreachelse}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2" align="center">{$lng.lbl_exceptions_list_empty}</td>
</tr>
{/foreach}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
</tr>
{if $product_options_ex ne ''}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
    <td><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'gi'))){ldelim}document.exceptionform.mode.value='product_options_ex_delete'; document.exceptionform.submit();{rdelim}" /></td>
</tr>
{/if}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
    <td class="TopLabel" colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_add_exception}</td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[new_exception]" /></td>{/if}
  <td colspan="2"><table>
  {foreach from=$product_options item=v}
  {if $v.options ne ''}
  <tr>
    <td>{$v.class}:</td>
    <td><select name="new_exception[{$v.classid}]">
    <option value="">{$lng.lbl_select_one_bracket}</option>
    {foreach from=$v.options item=o}
    <option value='{$o.optionid}'>{$o.option_name}</option>
    {/foreach}
    </select></td>
  </tr>
  {/if}
  {/foreach}
  <tr style="display: none;"><td colspan="2">&nbsp;</td></tr>
  </table>
  </td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2" class="TopLabel"><input type="submit" value="{$lng.lbl_add_exception|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_option_exceptions content=$smarty.capture.dialog extra='width="100%"'}
<br />

{if $user_account.allow_active_content}
<a name="js_code"></a>
{capture name=dialog}
{$lng.txt_product_options_js_note}<br />
<br />

<form action="{$script_name}" method="post" name="validateform">
<input type="hidden" name="section" value="options" />
<input type="hidden" name="mode" value="product_options_js_update" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[js]" /></td>{/if}
  <td><textarea name="js_code" cols="60" rows="15">{$product_options_js}</textarea></td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_validation_script_javascript content=$smarty.capture.dialog extra='width="100%"'}
{/if}

{/if}
{/if}
