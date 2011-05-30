{*
$Id: search_result.tpl,v 1.5 2010/06/11 08:15:52 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_products_management}

<script type="text/javascript">
//<![CDATA[
var txt_delete_products_warning = "{$lng.txt_delete_products_warning|strip_tags|wm_remove|escape:javascript}";
//]]>
</script>

<br />

{if $mode ne "search" or $products eq ""}

<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var searchform_def = [
  ['posted_data[categoryid]', ''],
  ['posted_data[category_main]', true],
  ['posted_data[category_extra]', true],
  ['posted_data[search_in_subcategories]', true],
  ['posted_data[by_title]', true],
  ['posted_data[by_shortdescr]', true],
  ['posted_data[by_fulldescr]', true],
  ['posted_data[by_keywords]', true],
  ['posted_data[price_min]', '{$zero}'],
  ['posted_data[price_max]', ''],
  ['posted_data[avail_min]', '0'],
  ['posted_data[avail_max]', ''],
  ['posted_data[weight_min]', '{$zero}'],
  ['posted_data[weight_max]', ''],
  ['posted_data[productcode]', ''],
  ['posted_data[including]', 'all'],
  ['posted_data[is_export]', false],
  ['posted_data[is_modify]', false],
  ['posted_data[productid]', ''],
  ['posted_data[provider]', ''],
  ['posted_data[forsale]', ''],
{if $usertype ne 'C' and $usertype ne 'B' and $active_modules.Feature_Comparison and $fclasses ne ''}
  ['posted_data[fclassid]', ''],
{/if}
  ['posted_data[flag_free_ship]', ''],
  ['posted_data[flag_ship_freight]', ''],
  ['posted_data[flag_global_disc]', ''],
  ['posted_data[flag_free_tax]', ''],
  ['posted_data[flag_min_amount]', ''],
  ['posted_data[flag_low_avail_limit]', ''],
  ['posted_data[flag_list_price]', ''],
{if $active_modules.Extra_Fields and $extra_fields ne ''}
{foreach from=$extra_fields item=v}
    ['posted_data[extra_fields][{$v.fieldid}]', false],
{/foreach}
{/if}
{if $active_modules.Manufacturers and $manufacturers ne '' and $config.Search_products.search_products_manufacturers eq 'Y'}
    ['posted_data[manufacturers][]', ''],
{/if}
  ['posted_data[substring]', '']
];
//]]>
</script>

{capture name=dialog}

<br />

<form name="searchform" action="search.php" method="post">
<input type="hidden" name="mode" value="search" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
  <td height="10" width="80%">
<input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring|escape}" />
&nbsp;
<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
  </td>
</tr>

<tr>
<td height="10"></td>
<td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="radio" id="including_all" name="posted_data[including]" value="all"{if $search_prefilled eq "" or $search_prefilled.including eq '' or $search_prefilled.including eq 'all'} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="including_all">{$lng.lbl_all_word}</label>&nbsp;&nbsp;</td>

  <td width="5"><input type="radio" id="including_any" name="posted_data[including]" value="any"{if $search_prefilled.including eq 'any'} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="including_any">{$lng.lbl_any_word}</label>&nbsp;&nbsp;</td>

  <td width="5"><input type="radio" id="including_phrase" name="posted_data[including]" value="phrase"{if $search_prefilled.including eq 'phrase'} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="including_phrase">{$lng.lbl_exact_phrase}</label></td>
</tr>
</table>
</td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>
  <td width="5"><input type="checkbox" id="posted_data_by_shortdescr" name="posted_data[by_shortdescr]"{if $search_prefilled eq "" or $search_prefilled.by_shortdescr} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_shortdescr">{$lng.lbl_short_description}</label>&nbsp;&nbsp;</td>
  <td width="5"><input type="checkbox" id="posted_data_by_fulldescr" name="posted_data[by_fulldescr]"{if $search_prefilled eq "" or $search_prefilled.by_fulldescr} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_fulldescr">{$lng.lbl_det_description}</label>&nbsp;&nbsp;</td>
  <td width="5"><input type="checkbox" id="posted_data_by_keywords" name="posted_data[by_keywords]"{if $search_prefilled eq "" or $search_prefilled.by_keywords} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_by_keywords">{$lng.lbl_keywords}</label>&nbsp;&nbsp;</td>
</tr>
</table>
  </td>
</tr>

{if $active_modules.Extra_Fields and $extra_fields ne ''}
<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_also_in}:</td>
  <td>
<table cellpadding="0" cellspacing="0">
{foreach from=$extra_fields item=v}
<tr>
  <td width="5"><input type="checkbox" id="posted_data_extra_fields_{$v.fieldid}" name="posted_data[extra_fields][{$v.fieldid}]"{if $v.selected eq "Y"} checked="checked"{/if} /></td>
  <td><label for="posted_data_extra_fields_{$v.fieldid}">{$v.field}</label></td>
</tr>
{/foreach}
</table>
  </td>
</tr>
{/if}

<tr>
  <td></td>
  <td>
  <hr />
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="checkbox" value='Y' id="posted_data_is_modify" name="posted_data[is_modify]" /></td>
  <td>&nbsp;</td>
  <td height="10" class="FormButton" nowrap="nowrap"><label for="posted_data_is_modify">{$lng.lbl_search_and_modify}</label></td>
</tr>
</table>
  </td>
</tr>

<tr> 
  <td></td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="checkbox" id="posted_data_is_export" name="posted_data[is_export]" value="Y" /></td>
  <td>&nbsp;</td>
  <td class="FormButton" nowrap="nowrap"><label for="posted_data_is_export">{$lng.lbl_search_and_export}</label></td>
</tr>
</table>
  </td>
</tr>

</table>

<br />
{include file="main/visiblebox_link.tpl" no_use_class="Y" mark="1" title=$lng.lbl_advanced_search_options extra=' width="100%"'}
<br />

<table cellpadding="0" cellspacing="0" width="100%" style="display: none;" id="box1">
<tr>
  <td>

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in_category}:</td>
  <td height="10">
  {include file="main/category_selector.tpl" extra=' style="width: 70%;"' field="posted_data[categoryid]" display_empty="E" categoryid=$search_prefilled.categoryid allcategories=$search_categories}
  </td>
</tr>

<tr>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5" nowrap="nowrap">{$lng.lbl_as}&nbsp;&nbsp;</td>
  <td width="5"><input type="checkbox" id="posted_data_category_main" name="posted_data[category_main]"{if $search_prefilled eq "" or $search_prefilled.category_main} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_category_main">{$lng.lbl_main_category}</label>&nbsp;&nbsp;</td>
  <td width="5"><input type="checkbox" id="posted_data_category_extra" name="posted_data[category_extra]"{if $search_prefilled eq "" or $search_prefilled.category_extra} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_category_extra">{$lng.lbl_additional_category}</label></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="checkbox" id="posted_data_search_in_subcategories" name="posted_data[search_in_subcategories]"{if $search_prefilled eq "" or $search_prefilled.search_in_subcategories} checked="checked"{/if} /></td>
  <td nowrap="nowrap"><label for="posted_data_search_in_subcategories">{$lng.lbl_search_in_subcategories}</label></td>
</tr>
</table>
  </td>
</tr>

{if $active_modules.Manufacturers and $manufacturers ne ''}
{capture name=manufacturers_items}
{section name=mnf loop=$manufacturers}
    <option value="{$manufacturers[mnf].manufacturerid}"{if $manufacturers[mnf].selected eq 'Y'} selected="selected"{/if}>{$manufacturers[mnf].manufacturer}</option>
{/section}
{/capture}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_manufacturers}:</td>
  <td height="10">
  <select name="posted_data[manufacturers][]" style="width: 70%;" multiple="multiple" size="{if $smarty.section.mnf.total gt 5}5{else}{$smarty.section.mnf.total}{/if}">
{$smarty.capture.manufacturers_items}
  </select>
  </td>
</tr>
{/if}

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
  <td height="10" width="80%"><input type="text" maxlength="64" name="posted_data[productcode]" value="{$search_prefilled.productcode|escape}" style="width:70%" /></td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_productid}#:</td>
  <td height="10" width="80%"><input type="text" maxlength="64" name="posted_data[productid]" value="{$search_prefilled.productid|escape}" style="width:70%" /></td>
</tr>

{if $usertype eq "A"}
<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_provider}:</td>
  <td height="10" width="80%"><input type="text" maxlength="64" name="posted_data[provider]" value="{$search_prefilled.provider|escape}" style="width:70%" /></td>
</tr>
{/if}

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
  <td height="10" width="80%">
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}" /></td>
  <td>&nbsp;-&nbsp;</td>
  <td><input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_quantity}:</td>
  <td height="10" width="80%">
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="text" size="10" maxlength="10" name="posted_data[avail_min]" value="{if $search_prefilled eq ""}0{else}{$search_prefilled.avail_min|escape}{/if}" /></td>
  <td>&nbsp;-&nbsp;</td>
  <td><input type="text" size="10" maxlength="10" name="posted_data[avail_max]" value="{$search_prefilled.avail_max|escape}" /></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_weight} ({$config.General.weight_symbol}):</td>
  <td height="10" width="80%">
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="text" size="10" maxlength="10" name="posted_data[weight_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.weight_min|formatprice}{/if}" /></td>
  <td>&nbsp;-&nbsp;</td>
  <td><input type="text" size="10" maxlength="10" name="posted_data[weight_max]" value="{$search_prefilled.weight_max|formatprice}" /></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_availability}:</td>
  <td height="10">
  <select name="posted_data[forsale]" style="width:70%">
    <option value="">&nbsp;</option>
    <option value="Y"{if $search_prefilled.forsale eq "Y"} selected="selected"{/if}>{$lng.lbl_avail_for_sale}</option>
    <option value="H"{if $product.forsale eq "H"} selected="selected"{/if}>{$lng.lbl_hidden}</option>
    <option value="N"{if $search_prefilled.forsale eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
{if $active_modules.Product_Configurator}
    <option value="B"{if $search_prefilled.forsale eq "B"} selected="selected"{/if}>{$lng.lbl_bundled}</option>
{/if}
  </select>
  </td>
</tr>

{if $usertype ne 'C' and $usertype ne 'B' and $active_modules.Feature_Comparison and $fclasses ne ''}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_product_feature_classes}:</td>
  <td height="10">
  <select name="posted_data[fclassid]" style="width:70%">
    <option value="">&nbsp;</option>
{foreach from=$fclasses item=v}
    <option value="{$v.fclassid}"{if $search_prefilled.fclassid eq $v.fclassid} selected="selected"{/if}>{$v.class}</option>
{/foreach}
  </select>
  </td>
</tr>
{/if}

<tr>
  <td class="FormButton">{$lng.lbl_free_shipping}:&nbsp;</td>
  <td>
    <select name="posted_data[flag_free_ship]">
      <option value="">&nbsp;</option>
      <option value="Y"{if $search_prefilled.flag_free_ship eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
      <option value="N"{if $search_prefilled.flag_free_ship eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
    </select>
  </td>
  </tr>
  <tr>
    <td class="FormButton">{$lng.lbl_global_discounts}:&nbsp;</td>
    <td>
      <select name="posted_data[flag_global_disc]">
        <option value="">&nbsp;</option>
        <option value="Y"{if $search_prefilled.flag_global_disc eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_global_disc eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="FormButton">{$lng.lbl_min_order_amount}:&nbsp;</td>
    <td>
      <select name="posted_data[flag_min_amount]">
        <option value="">&nbsp;</option>
        <option value="Y"{if $search_prefilled.flag_min_amount eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_min_amount eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="FormButton">{$lng.lbl_list_price}:&nbsp;</td>
    <td>
      <select name="posted_data[flag_list_price]">
        <option value="">&nbsp;</option>
        <option value="Y"{if $search_prefilled.flag_list_price eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_list_price eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="FormButton">{$lng.lbl_shipping_freight}:&nbsp;</td>
    <td>
      <select name="posted_data[flag_ship_freight]">
        <option value="">&nbsp;</option>
        <option value="Y"{if $search_prefilled.flag_ship_freight eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_ship_freight eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="FormButton">{$lng.lbl_tax_exempt}:&nbsp;</td>
    <td>
      <select name="posted_data[flag_free_tax]">
        <option value="">&nbsp;</option>
        <option value="Y"{if $search_prefilled.flag_free_tax eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_free_tax eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="FormButton">{$lng.lbl_lowlimit_in_stock}:&nbsp;</td>
    <td>
      <select name="posted_data[flag_low_avail_limit]">
        <option value="">&nbsp;</option>
        <option value="Y"{if $search_prefilled.flag_low_avail_limit eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_low_avail_limit eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </td>
  </tr>

</table>

  </td>
</tr>

</table>

<table cellpadding="1" cellspacing="5" width="100%">
  <tr>
    <td class="FormButton normal" width="20%">
      <a href="javascript:void(0);" onclick="javascript: reset_form('searchform', searchform_def);" class="underline">{$lng.lbl_reset_filter}</a>
    </td>
    <td class="main-button">
      <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
    </td>
  </tr>
</table>

</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
visibleBox('1');
//]]>
</script>
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_products content=$smarty.capture.dialog extra='width="100%"'}

<br />

<!-- SEARCH FORM DIALOG END -->

{/if}

<!-- SEARCH RESULTS SUMMARY -->

<a name="results"></a>

{if $mode eq "search"}
{if $total_items gt "1"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{elseif $total_items eq "0"}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

{if $mode eq "search" and $products ne ""}

<!-- SEARCH RESULTS START -->

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="search.php"}</div>

{if $total_pages gt 2}
{assign var="navpage" value=$navigation_page}
{/if}

<form action="process_product.php" method="post" name="processproductform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="navpage" value="{$navpage}" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
  <td>

{include file="main/navigation.tpl"}

{include file="main/products.tpl" products=$products}

<br />

{include file="main/navigation.tpl"}

<br />

<div class="main-button">
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</div>
<br /><br />
<input type="button" value="{$lng.lbl_modify_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) {ldelim} document.processproductform.action='product_modify.php'; submitForm(document.processproductform, 'list'); {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi')) &amp;&amp; confirm(txt_delete_products_warning)) submitForm(document.processproductform, 'delete');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_export_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(document.processproductform, 'export');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_export_all_found|strip_tags:false|escape}" onclick="javascript: self.location='search.php?mode=search&amp;export=export_found';" />

<br /><br /><br />

{$lng.txt_operation_for_first_selected_only}

<br /><br />

<input type="button" value="{$lng.lbl_preview_product|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(document.processproductform, 'details');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_clone_product|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(document.processproductform, 'clone');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_generate_html_links|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(document.processproductform, 'links');" />

  </td>
</tr>

</table>
</form>

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}

{/if}

<br /><br />
