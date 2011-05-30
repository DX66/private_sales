{*
$Id: product_details.tpl,v 1.7 2010/07/21 12:14:12 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{include file="check_clean_url.tpl"}
{include file="main/product_details_js.tpl"}
{include file="check_required_fields_js.tpl"}

<form action="product_modify.php" method="post" name="modifyform" onsubmit="javascript: return checkRequired(requiredFields){if $config.SEO.clean_urls_enabled eq "Y"} &amp;&amp;checkCleanUrl(document.modifyform.clean_url){/if}">
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="section" value="main" />
<input type="hidden" name="mode" value="{if $is_pconf}pconf{else}product_modify{/if}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellpadding="4" cellspacing="0" width="100%" class="product-details-table">

{include file="main/image_area.tpl"}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_product_owner}</td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td class="FormButton" width="10%" nowrap="nowrap">{$lng.lbl_provider}:</td>
  <td class="ProductDetails" width="90%">
{if $usertype eq "A" and $new_product eq 1}
  <select name="provider" class="InputWidth">
{section name=prov loop=$providers}
    <option value="{$providers[prov].id}"{if $product.provider eq $providers[prov].id} selected="selected"{/if}>{$providers[prov].login} ({$providers[prov].title} {$providers[prov].lastname} {$providers[prov].firstname})</option>
{/section}
  </select>
  {if $top_message.fillerror ne "" and $product.provider eq ""}<font class="Star">&lt;&lt;</font>{/if}
{else}
{$provider_info.title} {$provider_info.lastname} {$provider_info.firstname} ({$provider_info.login})
{/if}
  </td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_classification}</td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryid]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_main_category}:</td>
  <td class="ProductDetails">{include file="main/category_selector.tpl" field="categoryid" extra=' class="InputWidth"' categoryid=$product.categoryid|default:$default_categoryid}
  {if $top_message.fillerror ne "" and $product.categoryid eq ""}<font class="Star">&lt;&lt;</font>{/if}
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryids]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_additional_categories}:</td>
  <td class="ProductDetails">
  <select name="categoryids[]" class="InputWidth" multiple="multiple" size="8">
{foreach from=$allcategories item=c key=catid}
    <option value="{$catid}"{if $product.add_categoryids[$catid]} selected="selected"{/if}>{$c}</option>
{/foreach}
  </select>
  </td>
</tr>

{if $active_modules.Manufacturers ne "" and not $is_pconf}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[manufacturer]" /></td>{/if}
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_manufacturer}:</td>
    <td class="ProductDetails">
  <select name="manufacturerid">
      <option value=''{if $product.manufacturerid eq ''} selected="selected"{/if}>{$lng.lbl_no_manufacturer}</option>
    {foreach from=$manufacturers item=v}
      <option value='{$v.manufacturerid}'{if $v.manufacturerid eq $product.manufacturerid} selected="selected"{/if}>{$v.manufacturer}</option>
    {/foreach}
    </select>
  </td>
</tr>
{/if}

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[forsale]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_availability}:</td>
  <td class="ProductDetails">
  <select name="forsale">
    <option value="Y"{if $product.forsale eq "Y" or $product.forsale eq ""} selected="selected"{/if}>{$lng.lbl_avail_for_sale}</option>
    <option value="H"{if $product.forsale eq "H"} selected="selected"{/if}>{$lng.lbl_hidden}</option>
    <option value="N"{if $product.forsale ne "Y" and $product.forsale ne "" and $product.forsale ne "H" and ($product.forsale ne "B" or not $active_modules.Product_Configurator)} selected="selected"{/if}>{$lng.lbl_disabled}</option>
{if $active_modules.Product_Configurator and not $is_pconf}
    <option value="B"{if $product.forsale eq "B"} selected="selected"{/if}>{$lng.lbl_bundled}</option>
{/if}
  </select>
  </td>
</tr>

{if $product.internal_url}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_product_url}:</td>
  <td class="ProductDetails"><a href="{$product.internal_url}">{$product.internal_url}</a></td>
</tr>
{/if}

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_details}</td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[productcode]" disabled="disabled"/></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
  <td class="ProductDetails"><input type="text" name="productcode" id="productcode" size="20" maxlength="32" value="{$product.productcode|escape}" class="InputWidth" /></td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[product]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_product_name}* :</td>
  <td class="ProductDetails"> 
  <input type="text" name="product" id="product" size="45" class="InputWidth" value="{$product.product|escape}" {if $config.SEO.clean_urls_enabled eq "Y"}onchange="javascript: if (this.form.clean_url.value == '') copy_clean_url(this, this.form.clean_url)"{/if} />
  {if $top_message.fillerror ne "" and $product.product eq ""}<font class="Star">&lt;&lt;</font>{/if}
  </td>
</tr>

{include file="main/clean_url_field.tpl" clean_url=$product.clean_url clean_urls_history=$product.clean_urls_history clean_url_fill_error=$top_message.clean_url_fill_error tooltip_id='clean_url_tooltip_link'}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[keywords]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_keywords}:</td>
  <td class="ProductDetails"><input type="text" name="keywords" class="InputWidth" value="{$product.keywords|escape:"html"}" /></td>
</tr>

{if $active_modules.Egoods ne ""}
{include file="modules/Egoods/egoods.tpl"}
{/if}

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[descr]" /></td>{/if}
  <td colspan="2" class="FormButton">
<div{if $active_modules.HTML_Editor and not $html_editor_disabled} class="description"{/if}>{$lng.lbl_short_description}* :</div>
<div class="description-data">
{include file="main/textarea.tpl" name="descr" cols=45 rows=8 data=$product.descr width="100%" btn_rows=4}
{if $top_message.fillerror ne "" and ($product.descr eq "" or $product.xss_descr eq "Y")}<font class="Star">&lt;&lt;</font>{/if}
</div>
  </td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[fulldescr]" /></td>{/if}
  <td colspan="2" class="FormButton">
<div{if $active_modules.HTML_Editor and not $html_editor_disabled} class="description"{/if}>{$lng.lbl_det_description}:</div>
<div class="description-data">
  {include file="main/textarea.tpl" name="fulldescr" cols=45 rows=12 class="InputWidth" data=$product.fulldescr width="100%" btn_rows=4}
  {if $product.xss_fulldescr eq "Y"}<font class="Star">&lt;&lt;</font>{/if}
</div>
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2">{$lng.txt_html_tags_in_description}</td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[title_tag]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_title_tag}:</td>
  <td class="ProductDetails"><textarea name="title_tag" cols="45" rows="6" class="InputWidth">{$product.title_tag}</textarea></td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[meta_keywords]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_meta_keywords}:</td>
  <td class="ProductDetails"><textarea name="meta_keywords" cols="45" rows="6" class="InputWidth">{$product.meta_keywords}</textarea></td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[meta_description]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_meta_description}:</td>
  <td class="ProductDetails"><textarea name="meta_description" cols="45" rows="6" class="InputWidth">{$product.meta_description}</textarea></td>
</tr>

<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><hr /></td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[price]" />{/if}</td>{/if}
  <td class="FormButton" nowrap="nowrap">{if not $is_pconf}{$lng.lbl_price}{else}{$lng.lbl_pconf_base_price}{/if} ({$config.General.currency_symbol}):</td>
  <td class="ProductDetails">
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
  <input type="text" name="price" size="18" value="{ $product.price|formatprice|default:$zero}" />
  {if $top_message.fillerror ne "" and $product.price eq ""}<font class="Star">&lt;&lt;</font>{/if}
{/if}
  </td>
</tr>

{if not $is_pconf}
<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[list_price]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_list_price} <span class="Text">({$config.General.currency_symbol}):</span></td>
  <td class="ProductDetails"><input type="text" name="list_price" size="18" value="{$product.list_price|formatprice|default:$zero}" /></td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[avail]" />{/if}</td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_quantity_in_stock}:</td>
  <td class="ProductDetails">
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
  <input type="text" name="avail" size="18" value="{if $product.productid eq ""}{$product.avail|default:1000}{else}{$product.avail}{/if}" />
  {if $top_message.fillerror ne "" and $product.avail eq ""}<font class="Star">&lt;&lt;</font>{/if}
{/if}
  </td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[low_avail_limit]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_lowlimit_in_stock}:</td>
  <td class="ProductDetails"> 
  <input type="text" name="low_avail_limit" size="18" value="{if $product.productid eq ""}10{else}{ $product.low_avail_limit }{/if}" />
  {if $top_message.fillerror ne "" and $product.low_avail_limit le 0}<font class="Star">&lt;&lt;</font>{/if}
  </td>
</tr>
{/if}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[min_amount]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_min_order_amount}:</td>
  <td class="ProductDetails"><input type="text" name="min_amount" size="18" value="{if $product.productid eq ""}1{else}{$product.min_amount}{/if}" /></td>
</tr>

{if $active_modules.RMA ne '' and not $is_pconf}
<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[return_time]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_return_time}:</td>
  <td class="ProductDetails"><input type="text" name="return_time" size="18" value="{$product.return_time}" /></td>
</tr>
{/if}

{if not $is_pconf}
<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><hr /></td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[weight]" />{/if}</td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_weight} ({$config.General.weight_symbol}):</td>
  <td class="ProductDetails"> 
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
  <input type="text" name="weight" size="18" value="{ $product.weight|formatprice|default:$zero }" />
{/if}
  </td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_shipping]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_free_shipping}:</td>
  <td class="ProductDetails">
  <select name="free_shipping">
    <option value='N'{if $product.free_shipping eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
    <option value='Y'{if $product.free_shipping eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
  </select> 
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[shipping_freight]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_shipping_freight} ({$config.General.currency_symbol}):</td>
  <td class="ProductDetails">
  <input type="text" name="shipping_freight" size="18" value="{$product.shipping_freight|formatprice|default:$zero}" />
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[small_item]" /></td>{/if}
  <td class="FormButton">{$lng.lbl_small_item}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="small_item" value="Y"{if $product.small_item ne "Y"} checked="checked"{/if} onclick="javascript: switchPDims(this);" />
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[dimensions]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_shipping_box_dimensions} ({$config.General.dimensions_symbol}):</td>
  <td class="ProductDetails">
  <table cellpadding="0" cellspacing="1" border="0" width="100%">
  <tr>
    <td colspan="2">{$lng.lbl_length}</td>
    <td colspan="2">{$lng.lbl_width}</td>
    <td colspan="3">{$lng.lbl_height}</td>
  </tr>
  <tr>
    <td><input type="text" name="length" size="6" value="{$product.length|default:$zero}"{if $product.small_item eq "Y"} disabled="disabled"{/if} /></td>
    <td>&nbsp;x&nbsp;</td>
    <td><input type="text" name="width" size="6" value="{$product.width|default:$zero}"{if $product.small_item eq "Y"} disabled="disabled"{/if} /></td>
    <td>&nbsp;x&nbsp;</td>
    <td><input type="text" name="height" size="6" value="{$product.height|default:$zero}"{if $product.small_item eq "Y"} disabled="disabled"{/if} /></td>
    <td align="center" width="100%">{if $new_product eq 1}&nbsp;{else}<a href="javascript:void(0);" onclick="javascript: popupOpen('unavailable_shipping.php?id={$product.productid}', '', {ldelim}width:350,height:500{rdelim});">{$lng.lbl_check_for_unavailable_shipping_methods}</a>{/if}</td>
  </tr>
  </table>
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[separate_box]" /></td>{/if}
  <td class="FormButton">{$lng.lbl_ship_in_separate_box}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="separate_box" value="Y"{if $product.separate_box eq "Y"} checked="checked"{/if}{if $product.small_item eq "Y"} disabled="disabled"{/if} onclick="javascript: switchSSBox(this);" />
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[items_per_box]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_items_per_box}:</td>
  <td class="ProductDetails">
  <input type="text" name="items_per_box" size="18" value="{$product.items_per_box|default:1}"{if $product.small_item eq "Y" or $product.separate_box ne "Y"} disabled="disabled"{/if} />
  </td>
</tr>

<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><hr /></td>
</tr>
{/if} {*** / not $is_pconf / ***}

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[membershipids]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_membership}:</td>
  <td class="ProductDetails">{include file="main/membership_selector.tpl" data=$product}</td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_tax]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_tax_exempt}:</td>
  <td class="ProductDetails">
  <select name="free_tax"{if $taxes} onchange="javascript: ChangeTaxesBoxStatus(this);"{/if}>
    <option value='N'{if $product.free_tax eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
    <option value='Y'{if $product.free_tax eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
  </select> 
  </td>
</tr>

{if $taxes}
<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[taxes]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_apply_taxes}:</td>
  <td class="ProductDetails"> 
  <select name="taxes[]" multiple="multiple"{if $product.free_tax eq "Y"} disabled="disabled"{/if}>
  {section name=tax loop=$taxes}
  <option value="{$taxes[tax].taxid}"{if $taxes[tax].selected gt 0} selected="selected"{/if}>{$taxes[tax].tax_name}</option>
  {/section}
  </select>
  <br />{$lng.lbl_hold_ctrl_key}
  {if $is_admin_user}<br /><a href="{$catalogs.provider}/taxes.php" class="SmallNote" target="_blank">{$lng.lbl_click_here_to_manage_taxes}</a>{/if}
  </td>
</tr>
{/if}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[discount_avail]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_apply_global_discounts}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="discount_avail" value="Y"{if $product.productid eq "" or $product.discount_avail eq "Y"} checked="checked"{/if} />
  </td>
</tr>

{if $gcheckout_enabled}

<input type="hidden" name="valid_for_gcheckout" value="N" />
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[valid_for_gcheckout]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_gcheckout_product_valid}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="valid_for_gcheckout" value="Y"{if $product.productid eq "" or $product.valid_for_gcheckout eq "Y"} checked="checked"{/if} />
  </td>
</tr>

{/if}

{if $active_modules.Extra_Fields ne ""}
{include file="modules/Extra_Fields/product_modify.tpl"}
{/if}

{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/product_modify.tpl"}
{/if}

<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2" align="center">
    <br /><br />
    <div id="sticky_content">
    <table width="100%">
      <tr>
        <td width="120" align="left" class="main-button">
          <input type="submit" class="big-main-button" value=" {$lng.lbl_apply_changes|strip_tags:false|escape} " />
        </td>
        <td width="100%" align="right">
          {if $product.productid gt 0}
            <input type="button" value="{$lng.lbl_preview|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'details');" /> &nbsp;&nbsp;&nbsp;
            <input type="button" value="{$lng.lbl_clone|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'clone');" />&nbsp;&nbsp;&nbsp;
            <input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'delete');" />&nbsp;&nbsp;&nbsp;
            <input type="button" value="{$lng.lbl_generate_html_links|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'links');" />
          {/if}
        </td>
      </tr>
    </table>
    </div>
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"'}

{if $new_product ne "1" and $geid eq ''}
  <br />
  {include file="main/clean_urls.tpl" resource_name="productid" resource_id=$productid clean_url_action="product_modify.php" clean_urls_history_mode="clean_urls_history" clean_urls_history=$product.clean_urls_history}
{/if}
