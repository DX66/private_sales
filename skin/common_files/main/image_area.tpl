{*
$Id: image_area.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_product_images}</td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}

<td colspan="2">
<table width="100%" cellpadding="1" cellspacing="1">
<tr>
<td width="50%" valign="top" align="left">

{include file="main/top_message_js.tpl"}

<script type="text/javascript">
//<![CDATA[
var  old_widthT = "{$product.images.T.new_x}";
var  old_heightT = "{$product.images.T.new_y}";
var  old_widthP = "{$product.images.P.new_x}";
var  old_heightP = "{$product.images.P.new_y}";
var  geid = "{$geid}";
var lbl_modified = "{$lng.lbl_modified|wm_remove|escape:javascript}";
var  lbl_no_image_uploaded= "{$lng.lbl_no_image_uploaded|wm_remove|escape:javascript}";

var  change_buttons = new Array();
change_buttons['T'] = "{$lng.lbl_upload_thumbnail|wm_remove|escape:javascript}";
change_buttons['P'] = "{$lng.lbl_upload_image|wm_remove|escape:javascript}";
//]]>
</script>

<script type="text/javascript" src="{$SkinDir}/js/image_manipulations.js"></script>

{* Start product image table*}

<table cellspacing="4" cellpadding="4">
<tr> 
  <td class="ProductDetails" valign="top"><table class="geid-checkbox"><tr>{if $geid ne ''}<td class="geid-checkbox"><input type="checkbox" value="Y" name="fields[image]" id="fields_image" /></td>{/if}<td><font class="FormButton">{$lng.lbl_image}:</font></td></tr></table></td>
  {if $product.is_pimage}{assign var="no_delete" value=""}{else}{assign var="no_delete" value="Y"}{/if}
</tr>
<tr>
  <td class="ProductDetails" valign="top" align="left">
  {include file="main/edit_product_image.tpl" type="P" id=$product.productid button_name=$lng.lbl_save idtag="edit_product_image" image=$product.image.P already_loaded=$product.is_image_P image_x=$product.images.P.new_x image_y=$product.images.P.new_y}
  <br />
  <span id="original_image_descr_P">{if $product.is_pimage}{include file="main/image_property2.tpl" image=$product.image.P}{else}{$lng.lbl_no_image_uploaded}{/if}</span>
  <span id="modified_image_descr_P" style="display: none;"></span>
  <br /><br />
  </td>
</tr>

<tr>
<td class="ProductDetails" valign="top" align="left" style="padding: 4px 4px 10px 4px;">
  <input id="change_image" type="button" value="{if $product.is_pimage}{$lng.lbl_change_image}{else}{$lng.lbl_upload_image}{/if}" onclick='javascript: popup_image_selection("P", "{$product.productid}", "edit_product_image");' />
<br />

</td>
</tr>

<tr {if not $product.is_pimage}style="display: none;" id="edit_product_image_reset2"{/if}>
  <td class="SubHeaderLine" style="BACKGROUND-COLOR: #d3d3d3;"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr> 

<tr id="edit_product_image_reset" style="display: none;">
<td class="ProductDetails" valign="top" align="left" style="padding: 10px 4px 4px 4px;">
  <input id="Pimage_reset" type="button" value="{$lng.lbl_reset_image|strip_tags:false|escape}" onclick="javascript: $('#edit_product_image').attr('src', '{$ImagesDir}/spacer.gif');popup_image_selection_reset('P', '{$product.productid}', 'edit_product_image'); reset_descr('P', 'edit_product_image', old_widthP, old_heightP);" />
</td>
</tr>

<tr id="Pimage_button3" {if not $product.is_pimage}style="display: none;"{/if}>
<td class="ProductDetails" valign="top" align="left">
  <input type="button" value="{$lng.lbl_delete_image}" onclick="javascript: try {ldelim} delete_image('edit_product_image', 'P', '{$product.productid}', 'change_image'); {rdelim} catch (err) {ldelim} submitForm(this, 'delete_product_image'); {rdelim}" />
</td>
</tr>
</table>

{* Finish product image table *}

</td>

<td width="50%" valign="top" align="left">

{* Start product thumbnail table *}

<table cellspacing="4" cellpadding="4">
<tr>
  <td class="ProductDetails" valign="top"><table class="geid-checkbox"><tr>{if $geid ne ''}<td class="geid-checkbox"><input type="checkbox" value="Y" name="fields[thumbnail]" id="fields_thumbnail" /></td>{/if}<td><font class="FormButton">{$lng.lbl_thumbnail}:</font></td></tr></table></td>
  {if $product.is_thumbnail}{assign var="no_delete" value=""}{else}{assign var="no_delete" value="Y"}{/if}
</tr>
<tr>
<td class="ProductDetails" valign="top" align="left">

  <table cellpadding="0" cellspacing="0">
  <tr><td valign="top" align="left">
  {include file="main/edit_product_image.tpl" type="T" id=$product.productid delete_js="submitForm(this, 'delete_thumbnail');" button_name=$lng.lbl_save image=$product.image.T already_loaded=$product.is_image_T image_x=product.images.T.new_x image_y=$product.images.T.new_y}
  <br />
  <span id="original_image_descr_T">{if $product.is_thumbnail}{include file="main/image_property2.tpl" image=$product.image.T}{else}{$lng.lbl_no_thumbnail_uploaded}{/if}</span>
  <span id="modified_image_descr_T" style="display: none;"></span>
  </td>
  <td class="Product_Details" valign="top" align="left">
    &nbsp;&nbsp;
    {include file="main/tooltip_js.tpl" text=$lng.txt_need_thumbnail_help}
  </td>
  </tr>
  </table>

  <span class="ImageNotes">{$lng.lbl_thumbnail_msg}</span>

</td>
</tr>

<tr>
<td class="ProductDetails" valign="top" align="left" {if not $product.is_pimage or not $gdlib_enabled}style="padding: 4px 4px 10px 4px;"{/if}>
  <input id="change_thumbnail" type="button" value="{if $product.is_thumbnail}{$lng.lbl_change_thumbnail}{else}{$lng.lbl_upload_thumbnail}{/if}" onclick='javascript: popup_image_selection("T", "{$product.productid}", "edit_image");' />
</td>
</tr>

{if $gdlib_enabled}

<tr id="tr_generate_thumbnail" {if not $product.is_pimage}style="display: none;"{/if}>
<td class="ProductDetails" nowrap="nowrap" valign="top" align="left" style="padding: 4px 4px 10px 4px;">
  <input type="button" id="generate_thumbnail" value="{$lng.lbl_generate_thumbnail}" onclick="javascript: try {ldelim} gen_thumbnail('{$product.productid}'); {rdelim} catch(err) {ldelim} submitForm(this, 'generate_thumbnail'); {rdelim};" />
</td>
</tr>

{/if}

<tr {if not $product.is_thumbnail}style="display: none;" id="edit_image_reset2"{/if}>
  <td class="SubHeaderLine" style="BACKGROUND-COLOR: #d3d3d3"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>

<tr id="edit_image_reset" style="display: none;">
<td class="ProductDetails" valign="top" align="left" style="padding: 10px 4px 4px 4px;">
  <input id="Timage_reset" type="button" value="{$lng.lbl_reset_thumbnail|strip_tags:false|escape}" onclick="javascript: $('#edit_image').attr('src', '{$ImagesDir}/spacer.gif'); popup_image_selection_reset('T', '{$product.productid}', 'edit_image'); reset_descr('T', 'edit_image', old_widthT, old_heightT);" />
</td>
</tr>

<tr id="Timage_button3" {if not $product.is_thumbnail}style="display: none;"{/if}>
<td class="ProductDetails" valign="top" align="left">
  <input type="button" value="{$lng.lbl_delete_thumbnail}" onclick="javascript: try {ldelim} delete_image('edit_image', 'T', '{$product.productid}', 'change_thumbnail'); {rdelim} catch (err) {ldelim} submitForm(this, 'delete_thumbnail'); {rdelim}" />
</td>
</tr>

</table>

{* Finish product thumbnail table *}

</td>
</tr>
</table>

</td>
</tr>

<tr id="ajax_message" style="display: none">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2" class="ImageNotes" nowrap="nowrap" align="center">

  <div id="ajax-dialog-message">
    <div id="ajax-dialog-main" class="box message-i">
<a href="javascript:void(0);" class="close-link" onclick="javascript: $('#ajax_message').hide();"><img id="ajax-dialog-img" src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_close|escape}" class="close-img-i" /></a><span id="ajax-dialog-content"></span>
    </div>
  </div>

  </td>
</tr>

<tr id='image_save_msg' style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2" class="ImageNotes" nowrap="nowrap" align="center"><br /><br />{$lng.lbl_product_image_save_msg}<br /><br /><input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " /></td>
</tr>

{if not $gdlib_enabled}
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
<td colspan="2" class="ImageNotes">{$lng.lbl_auto_resize_no_gd_library}</td>
</tr>
{/if}
