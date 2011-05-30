{*
$Id: product_magnifier_modify.tpl,v 1.1 2010/05/21 08:32:43 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Magnifier ne ""}

<script type="text/javascript" src="{$SkinDir}/modules/Magnifier/popup.js"></script>

{$lng.txt_zoom_images_top_text}

<br /><br />

{capture name=dialog}
<form action="product_modify.php" method="post" name="uploadform">

<input type="hidden" name="section" value="{$section}" />
<input type="hidden" name="mode" value="product_zoomer" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="uploadform" prefix="iids"}

<table cellspacing="2" cellpadding="3" width="100%">

<tr class="TableHead">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="15">&nbsp;</td>
  <td width="80">{$lng.lbl_image}</td>
  <td width="15">{$lng.lbl_pos}</td>
  <td width="30%">{$lng.lbl_availability}</td>
  <td width="35%" nowrap="nowrap">{$lng.lbl_image_size}</td> 
  <td width="35%">&nbsp;</td>
</tr>

{foreach from=$zoomer_images item=image}

<tr{cycle values=", class='TableSubHead'"}>
  {if $geid ne ''}<td width="15" class="TableSubHead">{if $image.common_image eq 'Y'}<input type="checkbox" value="Y" name="fields[z_image][{$image.imageid}]" />{/if}</td>{/if}
  <td width="15"><input type="checkbox" value="Y" name="iids[{$image.imageid}]" /></td>
  <td align="center">
<a href="javascript:void(0);" onclick="javascript: popup_magnifier('{$productid}', '{$config.Magnifier.magnifier_width}','{$config.Magnifier.magnifier_height}', '{$image.imageid}');" id="thmb_{$image.imageid}"><img src="{$xcart_web_dir}/images/Z/{$productid}/{$image.imageid}/thumbnail.jpg" alt="" width="80" /></a>
  </td>
  <td><input type="text" size="5" maxlength="5" name="zoomer_image[{$image.imageid}][orderby]" value="{$image.orderby}" /></td>
  <td>
<select name="zoomer_image[{$image.imageid}][avail]" style="width: 100%;">
  <option value="Y"{if $image.avail eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
  <option value="N"{if $image.avail ne "Y"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
</select>
  </td>
  <td align="center">{$image.image_x}x{$image.image_y}</td>
  <td align="center" nowrap="nowrap">{include file="buttons/button.tpl" href="javascript: popup_create_thumbnail(`$productid`, `$image.imageid`, `$magnifier_sets.x_crt_thmb`, `$magnifier_sets.y_crt_thmb`)" button_title=$lng.lbl_re_create_thumbnail}</td>
</tr>
{foreachelse}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="6" align="center">{$lng.txt_no_images}</td>
</tr>

{/foreach}

</table>

<table cellspacing="0" cellpadding="3" width="100%">

{if $zoomer_images}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if( checkMarks(this.form, new RegExp('iids', 'ig')) &amp;&amp;confirm('{$lng.lbl_delete_image|wm_remove|escape:javascript}?')) submitForm(this, 'product_zoomer_delete');" />&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'zoomer_update_availability');" />&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_reslice_image|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'reslice');" />
  </td>
</tr>

{/if}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td>
<br /><br />

{if not $gd_not_loaded and $gd_config.correct_version}

{include file="main/subheader.tpl" title=$lng.lbl_add_new_image}

<table>
<tr>
  <td><b>{$lng.lbl_preview}:</b></td>
  <td>&nbsp;</td>
  <td><a href="{$xcart_web_dir}/image.php?id={$product.productid}&amp;type=Z&amp;tmp" target="_blank"><img id="edit_image_Z" src="{$xcart_web_dir}/image.php?id=0&amp;type=Z&amp;tmp" alt="" /></a></td>
</tr>
<tr>
  <td colspan="3" style="display: none;" id="edit_image_Z_text"><br /><br />{$lng.txt_save_det_image_note}</td>
</tr>
</table>

  </td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[new_z_image]" /></td>{/if}
  <td>

<table cellpadding="4" cellspacing="0">
<tr>
  <td nowrap="nowrap">{$lng.lbl_select_file}:</td>
  <td><input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_image_selection('Z', '{$product.productid}', 'edit_image_Z');" /></td>
</tr>
<tr>
  <td colspan="2"><br /><input type="submit" value="{$lng.lbl_upload|strip_tags:false|escape}" /></td>
</tr>
</table>

{else}

<font class="ErrorMessage">{$lng.lbl_error_gd_zoomer_upload_images}</font>

{/if}
  
  </td>
</tr>
</table>

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_zoom_images content=$smarty.capture.dialog extra='width="100%"'}
{/if}
