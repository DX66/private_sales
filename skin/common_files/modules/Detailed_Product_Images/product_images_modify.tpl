{*
$Id: product_images_modify.tpl,v 1.2 2010/07/16 06:30:56 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Detailed_Product_Images ne ""}

{$txt_get_images_top_text}

<br /><br />

{capture name=dialog}
{if $config.Detailed_Product_Images.det_image_popup eq 'Y'}
  <br />
    <strong>{$lng.lbl_note}:</strong>
    {$lng.txt_det_image_warning_pmodify}
{/if}

<form action="product_modify.php" method="post" name="uploadform">

<input type="hidden" name="mode" value="product_images" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

{if $images}
{include file="main/check_all_row.tpl" style="line-height: 170%;" form="uploadform" prefix="iids"}
{/if}

<table cellspacing="0" cellpadding="3" width="100%">

<tr class="TableHead">
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
<td width="15" class="DataTable">&nbsp;</td>
<td width="65" class="DataTable">{$lng.lbl_image}</td>
<td width="5%" class="DataTable">{$lng.lbl_pos}</td>
<td width="15%" class="DataTable">{$lng.lbl_availability}</td>
<td width="40%" nowrap="nowrap" class="DataTable">{$lng.lbl_alternative_text}</td>
<td width="40%" nowrap="nowrap">{$lng.lbl_image_properties}</td>
</tr>

{if $images}

{section name=image loop=$images}

<tr{cycle values=", class='TableSubHead'"}>
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[d_image][{$images[image].imageid}]" /></td>{/if}
  <td width="15" class="DataTable"><input type="checkbox" value="Y" name="iids[{$images[image].imageid}]" /></td>
  <td align="center" class="DataTable">
    <a href="{$xcart_web_dir}/image.php?id={$images[image].imageid}&amp;type=D" target="_blank"><img src="{$xcart_web_dir}/image.php?id={$images[image].imageid}&amp;type=D" width="{$images[image].thbn_image_x}" height="{$images[image].thbn_image_y}" alt="" /></a>
  </td>
  <td class="DataTable">
<input type="text" size="5" maxlength="5" name="image[{$images[image].imageid}][orderby]" value="{$images[image].orderby|escape}" style="width: 100%;" />
  </td>
  <td class="DataTable">
<select name="image[{$images[image].imageid}][avail]" style="width:100%">
  <option value="Y"{if $images[image].avail eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
  <option value="N"{if $images[image].avail eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
</select>
  </td>
  <td class="DataTable"><input type="text" size="32" name="image[{$images[image].imageid}][alt]" value="{$images[image].alt|escape}" style="width:100%" /></td>
<td width="30%" class="DataTable">
{$images[image].type},
{$images[image].image_x}x{$images[image].image_y},
{$images[image].image_size}b
</td>
</tr>
{/section}

<tr>
  <td>&nbsp;</td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3" class="main-button">
    <input type="button" class="big-main-button" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" onclick="document.uploadform.mode.value='update_availability';document.uploadform.submit();" />&nbsp;&nbsp;&nbsp;
  </td>
  <td colspan="3" align="right">
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('iids', 'ig'))) {ldelim} document.uploadform.mode.value='product_images_delete'; document.uploadform.submit();{rdelim}" />
  </td>
</tr>

{else}

<tr>
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
<td colspan="6" align="center">{$lng.txt_no_images}</td>
</tr>

{/if}
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
<td colspan="6">
<br /><br />

{include file="main/subheader.tpl" title=$lng.txt_add_new_detail_image}

<table>

<tr>
<td><b>{$lng.lbl_preview}:</b></td>
<td>&nbsp;</td>
<td><a href="{$xcart_web_dir}/image.php?id={$product.productid}&amp;type=D&amp;tmp" target="_blank"><img id="edit_image_D" src="{$xcart_web_dir}/image.php?id=0&amp;type=D&amp;tmp" alt="" /></a></td>
</tr>

<tr>
<td colspan="3" style="display: none;" id="edit_image_D_text"><br /><br />{$lng.txt_save_det_image_note}</td>
</tr>

</table>

</td>
</tr>
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[new_d_image]" /></td>{/if}
<td colspan="6">
<table cellpadding="4" cellspacing="0">

<tr>
<td nowrap="nowrap">{$lng.lbl_select_file}:</td>
<td>
<input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="popup_image_selection('D', '{$product.productid}', 'edit_image_D');" />
</td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_alternative_text}</td>
<td><input type="text" size="45" name="alt" value="" /></td>
</tr>

<tr>
  <td colspan="2" class="main-button">
    <br />
    <input type="submit" value="{$lng.lbl_upload|strip_tags:false|escape}" />
  </td>
</tr>

</table>

</td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_detailed_images content=$smarty.capture.dialog extra='width="100%"'}
{/if}
