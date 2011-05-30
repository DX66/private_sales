{*
$Id: product_variants.tpl,v 1.4.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Product_Options ne ""}

<script type="text/javascript" src="{$SkinDir}/modules/Product_Options/product_variants.js"></script>

<script type="text/javascript">
//<![CDATA[
var vwprices = [];
{foreach from=$variants item=v key=k}
{if $v.wholesale ne ''}
vwprices[{$k}] = [{foreach from=$v.wholesale item=vw name=whlv}
{if $vw.quantity gt 0}

  [{$vw.quantity}, {$vw.membershipid}, '{$vw.price|formatprice}']{if not $smarty.foreach.whlv.last},{/if}
{/if}
{/foreach}];
{/if}
{/foreach}
var memberships = [];
{foreach from=$memberships item=m}
memberships[{$m.membershipid}] = "{$m.membership|replace:'"':'\"'}";
{/foreach}
var lbl_delete = "{$lng.lbl_delete|wm_remove|escape:javascript}";
var lbl_all = "{$lng.lbl_all|wm_remove|escape:javascript}";
var msg_adm_warn_variants_sel = "{$lng.msg_adm_warn_variants_sel|wm_remove|escape:javascript}";
var current_location = "{$current_location|wm_remove|escape:javascript}";
var pwindow;
var dateObj = new Date();
var imgTStmap = null;
var oldTStmap;
var productid = {$productid};
//]]>
</script>

{$lng.txt_product_variants_note_1}<br />
<br />
{capture name=dialog}
{$lng.txt_product_variants_note_2}<br />
<br />
<strong>{$lng.lbl_note}:</strong> {$lng.txt_product_variants_warning}<br />
<br />

<div align="right">{include file="main/visiblebox_link.tpl" mark="fpv" title=$lng.lbl_filter_product_variants}</div>
<form action="product_modify.php" method="post" name="productvariantssearchform">
<input type="hidden" id="imageW_onunload" name="imageW_onunload" value="" />
<input type="hidden" name="section" value="variants" />
<input type="hidden" name="mode" value="product_variants_search" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellpadding="0" cellspacing="0" width="100%" style="display: none;" id="boxfpv">
<tr><td>{include file="main/subheader.tpl" title=$lng.lbl_filter_product_variants}</td></tr>
<tr><td>{$lng.txt_filter_product_variants_note}</td></tr>
<tr><td>{$lng.lbl_select_options}:</td></tr>
<tr><td><hr /></td></tr>
<tr><td>
<table width="100%" cellspacing="1" cellpadding="2">
{foreach from=$product_options item=v}
{if $v.is_modifier eq ''}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
  <td><b>{$v.class}</b>:</td>
{assign var="classid" value=$v.classid}
  <td>{foreach from=$v.options item=o}
{assign var="optionid" value=$o.optionid}
{assign var="tmp_class" value=$search_variants[$classid]}
  <span style="white-space: nowrap;"><input type="checkbox" name="search[{$classid}][{$optionid}]" value="{$optionid}"{if $tmp_class[$optionid] ne '' or $is_search_all eq 'Y'} checked="checked"{/if} />&nbsp;{$o.option_name}</span>&nbsp;&nbsp;
  {/foreach}</td>
</tr>
{/if}
{/foreach}
</table>
</td></tr>
<tr><td><hr /></td></tr>
<tr><td><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td></tr>
</table>
</form>
<br />

{if $def_variant_failure}
<div class="ErrorMessage">{$lng.lbl_warning}: {$lng.txt_default_variant_failure_note}</div>
<br />
{/if}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var checkboxes_form = 'productvariantsform';
var checkboxes = [{foreach from=$variants item=v key=k}'v{$k}',{/foreach}''];

var vids = [];
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>
<div style="line-height: 170%;"><a href="javascript:void(0);" onclick="javascript: change_all(true); rebuildWP();">{$lng.lbl_check_all}</a> / <a href="javascript:void(0);" onclick="javascript: change_all(false); rebuildWP();">{$lng.lbl_uncheck_all}</a></div>

<form action="product_modify.php" method="post" name="productvariantsform">
<input type="hidden" name="section" value="variants" />
<input type="hidden" name="mode" value="product_variants_modify" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />
<input type="hidden" name="submode" value="data" />

<table cellspacing="0" cellpadding="3" width="100%">
<tr class="TableHead"> 
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="15" class="DataTable">&nbsp;</td>
  <td width="80%" class="DataTable">{$lng.lbl_variants}</td>
  <td class="DataTable">{$lng.lbl_image}</td>
  <td class="DataTable">{$lng.lbl_sku}</td>
  <td class="DataTable">{$lng.lbl_weight}</td>
  <td class="DataTable">{$lng.lbl_in_stock}</td>
  <td class="DataTable">{$lng.lbl_price}</td>
  <td>{$lng.lbl_def}</td>
</tr>
{foreach from=$variants item=v key=k}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[variants][{$k}]" /></td>{/if}
  <td class="DataTable" width="15"><input type="checkbox" id="v{$k}" name="vids[{$k}]" value="{$k}" onclick="javascript: rebuildWP(); displayImage(this, {$k|default:0});" /></td>
  <td class="DataTable"><table cellspacing="1" cellpadding="0">
  {foreach from=$v.options item=o}
  <tr>
    <td>{$o.class}:</td>
    <td>{$o.option_name}</td>
  </tr>
  {/foreach}
  </table></td>
  <td align="center" class="DataTable"><img id="image_W_{$k}" src="{$current_location}/image.php?type=W&amp;id={$k}&amp;timestamp={$smarty.now}" width="25" height="25" alt="" onclick="javascript: window.open(this.src,'_wpreview','location=no,direction=no,menubar=no,toolbar=no,status=no');" style="cursor: pointer;" /></td>
  <td class="DataTable"><input type="text" size="10" name="vs[{$k}][productcode]" value="{$v.productcode|escape}" maxlength="32"{if $v.sku_err} class="error-field"{/if} /></td>
  <td class="DataTable"><input type="text" size="5" name="vs[{$k}][weight]" value="{$v.weight|formatprice}" /></td>
  <td class="DataTable"><input type="text" size="5" name="vs[{$k}][avail]" value="{$v.avail|formatnumeric}" /></td>
  <td nowrap="nowrap" class="DataTable"><input type="text" size="7" name="vs[{$k}][price]" value="{$v.price|formatprice}" />
{if $v.wholesale ne ''}
<img id="close{$k}wp" src="{$ImagesDir}/plus.gif" alt="{$lng.lbl_click_to_open|escape}" onclick="javascript: visibleBox('{$k}wp');" />
<img id="open{$k}wp" style="display: none" src="{$ImagesDir}/minus.gif" alt="{$lng.lbl_click_to_close|escape}" onclick="javascript: visibleBox('{$k}wp');" />
{/if}
</td>
  <td><input type="radio" name="def_variant" value="{$k}"{if $v.def eq 'Y'} checked="checked"{/if} />
<script type="text/javascript">
//<![CDATA[
vids[{$k}] = [document.getElementById('v{$k}'), document.getElementById('image_W_{$k}')];
//]]>
</script>
  </td>
</tr>
{if $v.wholesale ne ''}
<tr id="box{$k}wp" style="display: none;">
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
  <td colspan="6" align="right" width="100%">

<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
  <td>{$lng.lbl_quantity}</td>
  <td>{$lng.lbl_membership}</td>
  <td>{$lng.lbl_price}</td>
</tr>
  {foreach from=$v.wholesale item=wp}
  {if $wp.quantity gt 1 or $wp.membershipid gt 0}
<tr>
  <td align="center">{$wp.quantity|formatnumeric}</td>
  <td align="center">{$memberships_keys[$wp.membershipid].membership|default:$lng.lbl_all}</td>
  <td align="center">{currency value=$wp.price}</td>
</tr>
  {/if}
  {/foreach}
</table>

  </td>
</tr>
{/if}
{foreachelse}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td align="center" colspan="8">{$lng.lbl_variants_list_empty}</td>
</tr>
{/foreach}
{if $variants ne ''}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="8">

<table cellspacing="1" cellpadding="3">
<tr>
  <td class="ButtonsRow"><b>{$lng.lbl_edit_selected}:</b></td>
  <td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_change_images href="javascript: updateWImage();" substyle="image-change"}</td>
  <td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_back_to_default_image href="javascript: deleteWImage();" substyle="image-delete"}</td>
  <td class="ButtonsRow" style="display: none;" id="imageW_reset">{include file="buttons/button.tpl" button_title=$lng.lbl_undo_changes href="javascript: resetWImage();" substyle="image-reset"}</span></td>
</tr>
<tr>
  <td colspan="4"><span class="Note" style="display: none;" id="imageW_text">{$lng.lbl_variant_image_save_msg}</span></td>
</tr>
</table>
<br /><br />

<div class="main-button">
  <input type="button" class="big-main-button" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" onclick="javascript: this.form.submit();" />
<div>

  </td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>&nbsp;</td>
</tr>

{if $active_modules.Wholesale_Trading}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="8">{include file="main/visiblebox_link.tpl" mark="mwpv" title=$lng.lbl_modify_wholesale_prices_for_selected_variants}</td>
</tr>
<tr id="boxmwpv" style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[wp_variant]" /></td>{/if}
  <td colspan="8">
<br />
{$lng.txt_modify_wholesale_prices_for_selected_variants_note}<br />
<br />
<b>{$lng.lbl_note}:</b> <span id="wholesale_admin_note_small">{$lng.lbl_wholesale_admin_note_small} <a href="javascript:void(0);" onclick="javascript: document.getElementById('wholesale_admin_note_small').style.display = 'none'; document.getElementById('wholesale_admin_note').style.display = '';">{$lng.lbl_more}</a></span><span id="wholesale_admin_note" style="display: none;">{$lng.lbl_wholesale_admin_note}</span><br />
<br />
<table cellspacing="1" cellpadding="2" id="wp_table">
<tr class="TableHead">
  <td>{$lng.lbl_quantity}</td>
  <td>{$lng.lbl_price}</td>
  <td>{$lng.lbl_membership}</td>
</tr>
<tr id="wp_tr">
  <td id="wp_box_1"><input type="text" size="5" name="new_wprice[quantity][0]" value="1" /></td>
  <td id="wp_box_2"><input type="text" size="7" name="new_wprice[price][0]" /></td>
  <td id="wp_box_3"><select name="new_wprice[membershipid][0]">
  <option value="">{$lng.lbl_all}</option>
  {foreach from=$memberships item=m}
  <option value="{$m.membershipid}">{$m.membership}</option>
  {/foreach}
  </select></td>
  <td>{include file="buttons/multirow_add.tpl" mark="wp"}</td>
</tr>
</table>
<br />
<input type="button" value="{$lng.lbl_update_wholesale_prices|strip_tags:false|escape}" onclick="javascript: this.form.submode.value = 'prices'; this.form.submit();" />
  </td>
</tr>
{/if}
{/if}
</table>

<input type="hidden" name="delete_wprice_quantity" />
<input type="hidden" name="delete_wprice_membershipid" />
<input type="hidden" name="tstamp" value="" />
<input type="hidden" id="skip_image_W" name="skip_image[W]" value="" />
<input type="hidden" id="wimg_update_action" name="wimg_update_action" value="" />
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_variants content=$smarty.capture.dialog extra='width="100%"'}
{/if}
