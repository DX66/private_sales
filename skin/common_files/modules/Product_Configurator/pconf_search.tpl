{*
$Id: pconf_search.tpl,v 1.4.2.1 2010/10/22 07:52:53 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{* $id: pconf_search.tpl,v 1.15 2005/08/26 13:25:39 max Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var txt_pconf_search_delete_alert = "{$lng.txt_pconf_search_delete_alert|wm_remove|escape:javascript}";
var product_type, no_product_type;
{literal}
function toggle_status(obj) {
  if (!product_type)
    product_type = document.getElementById('product_type');
  if (!no_product_type)
    no_product_type = document.getElementById('no_product_type');
  if (!product_type || !no_product_type)
    return false;

  if (obj.options[obj.selectedIndex].value == 'C') {
    product_type.disabled = true;
    no_product_type.disabled = true;
  } else {
    no_product_type.disabled = false;
    if (no_product_type.onclick)
      no_product_type.onclick();
  }
}

function toggle_no_product_type(obj) {
  if (!product_type)
    product_type = document.getElementById('product_type');
  if (!product_type)
    return false;
  product_type.disabled = (obj.checked);
}

{/literal}
//]]>
</script>

{$lng.txt_pconf_search_title}
<br /><br />
{capture name=dialog}
<form action="pconf.php" method="post" name="pconfsearchform">
<input type="hidden" name="mode" value="search" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td height="10" width="30%" class="FormButton" nowrap="nowrap">{$lng.lbl_product} #:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10" width="70%"><input type="text" name="post_data[productid]" size="6" maxlength="11" value="{$search_data.productid}" /></td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_product_title}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10"><input type="text" name="post_data[substring]" size="30" value="{$search_data.substring|escape}" /></td>
</tr>

{if $usertype eq "A"}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_provider}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10"><input type="text" name="post_data[provider]" size="30" value="{$search_data.provider|escape}" /></td>
</tr>
{/if}

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_pconf_search_status}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
  <select name="post_data[product_status]" onchange="javascript: toggle_status(this);">
    <option value=""{if $search_data.product_status eq ""} selected="selected"{/if}>{$lng.lbl_pconf_search_allproducts}</option>
    <option value="C"{if $search_data.product_status eq "C" or $search_data eq ""} selected="selected"{/if}>{$lng.lbl_pconf_search_configurable_only}</option>
    <option value="B"{if $search_data.product_status eq "B"} selected="selected"{/if}>{$lng.lbl_pconf_search_bundled_only}</option>
  </select>
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_in_category}:</td>
  <td width="10" height="10"><font class="CustomerMessage">*</font></td>
  <td height="10">{include file="main/category_selector.tpl" field="post_data[categoryid]" display_empty="E" categoryid=$search_data.categoryid}</td>
</tr>

<tr>
  <td height="10">&nbsp;</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="checkbox" id="post_data_search_in_subcategories" name="post_data[search_in_subcategories]"{if $search_data.search_in_subcategories ne "" or $search_data eq ""} checked="checked"{/if} /></td>
  <td><label for="post_data_search_in_subcategories">{$lng.lbl_pconf_search_subcats}</label></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td height="10" class="FormButton">{$lng.lbl_pconf_search_no_types}:<br /></td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10"><input type="checkbox" id="no_product_type" name="post_data[no_product_type]"{if $search_data.no_product_type} checked="checked"{/if}{if $search_data.product_status eq "C"} disabled="disabled"{/if} onclick="javascript: toggle_no_product_type(this);" /></td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_pconf_search_types}:<br /></td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
  <select id="product_type" name="post_data[product_type][]" multiple="multiple" size="5"{if $search_data.no_product_type or $search_data.product_status eq "C"} disabled="disabled"{/if}>
{foreach from=$product_types item=pt}
    <option value="{$pt.ptypeid}"{if $pt.selected} selected="selected"{/if}>{$pt.ptype_name|escape}</option>
{/foreach}
    <option value="">&nbsp;</option>
  </select>
<script type="text/javascript">
//<![CDATA[
var tmp = document.getElementById('product_type');
if (tmp)
  tmp.options[tmp.options.length-1] = null;
//]]>
</script>
<br />
{$lng.lbl_pconf_search_note_ctrl_types}
  </td>
</tr>

<tr>
  <td width="78" class="FormButton">&nbsp;</td>
  <td width="10">&nbsp;</td>
  <td width="282" class="SubmitBox"><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_products content=$smarty.capture.dialog extra='width="100%"'}

<br />
{if $smarty.get.action eq "go"}
{$lng.lbl_products_found|substitute:"items":$total_items}
<br />
{/if}

{if $total_items gt 0}
<br />
{capture name=dialog}
{include file="main/navigation.tpl"}
<form action="process_product.php" method="post" name="processproductform">
<input type="hidden" name="source" value="pconf" />
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <th width="10">&nbsp;</th>
  <th width="10" align="left" height="16">ID</th>
  <th align="left">Product</th>
  <th align="left">{$lng.lbl_pconf_search_status}</th>
  <th nowrap="nowrap">{$lng.lbl_pconf_search_assigned_types}</th>
</tr>

{foreach from=$products item=product}
<tr{cycle values=" , class='TableSubHead'"}>
  <td><input type="checkbox" name="productids[{$product.productid}]" value="{$product.productid}" /></td>
  <td>#<a href="product_modify.php?productid={$product.productid}">{$product.productid}</a></td>
  <td width="99%"><a href="product_modify.php?productid={$product.productid}"><font class="ItemsList">{$product.product|truncate:35:"...":false|amp}</font></a>
</td>
  <td>
{if $product.product_type eq "C"}<a href="product_modify.php?productid={$product.productid}&amp;mode=pconf&amp;edit=wizard" title="{$lng.lbl_configure|escape}"><font color="#006600"><b>{$lng.lbl_pconf_search_configurable}</b></font></a>
{elseif $product.forsale eq "B"}<a href="product_modify.php?productid={$product.productid}" title="{$lng.lbl_modify|escape}"><b>{$lng.lbl_pconf_search_bundled}</b></a>
{elseif $product.forsale eq "N"}<a href="product_modify.php?productid={$product.productid}" title="{$lng.lbl_modify|escape}"><font class="ErrorMessage">{$lng.lbl_pconf_search_disabled}</b></font></a>
{elseif $product.forsale eq "H"}<a href="product_modify.php?productid={$product.productid}" title="{$lng.lbl_modify|escape}"><b>{$lng.lbl_hidden}</b></a>
{else}&nbsp;{/if}
  </td>

{if $product.product_type eq "C"}
  <td>&nbsp;</td>
{else}
  <td align="center">{if $product.types_count gt 0}<b>{$product.types_count}</b>{else}{$lng.lbl_pconf_search_notavail}{/if}</td>
{/if}

</tr>
{/foreach}

</table>
<br />
{include file="main/navigation.tpl"}
<br />
<input type="hidden" name="mode" value="" />
<input type="button" value="{$lng.lbl_details|strip_tags:false|escape}" onclick="javascript: submitForm(document.processproductform, 'details');" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_clone|strip_tags:false|escape}" onclick="javascript: submitForm(document.processproductform, 'clone');" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (confirm(txt_pconf_search_delete_alert) && checkMarks(this.form, new RegExp('productids', 'gi'))) submitForm(document.processproductform, 'delete');" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_modify_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids', 'gi'))) {ldelim} document.processproductform.action='product_modify.php'; submitForm(document.processproductform, 'list');{rdelim}" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_export_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids', 'gi'))) submitForm(document.processproductform, 'export');" />

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}
{/if}

