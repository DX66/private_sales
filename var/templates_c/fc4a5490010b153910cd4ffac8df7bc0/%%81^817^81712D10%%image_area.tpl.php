<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/image_area.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'main/image_area.tpl', 27, false),array('modifier', 'escape', 'main/image_area.tpl', 27, false),array('modifier', 'strip_tags', 'main/image_area.tpl', 69, false),)), $this); ?>
<?php func_load_lang($this, "main/image_area.tpl","lbl_product_images,lbl_modified,lbl_no_image_uploaded,lbl_upload_thumbnail,lbl_upload_image,lbl_image,lbl_save,lbl_no_image_uploaded,lbl_change_image,lbl_upload_image,lbl_reset_image,lbl_delete_image,lbl_thumbnail,lbl_save,lbl_no_thumbnail_uploaded,txt_need_thumbnail_help,lbl_thumbnail_msg,lbl_change_thumbnail,lbl_upload_thumbnail,lbl_generate_thumbnail,lbl_reset_thumbnail,lbl_delete_thumbnail,lbl_close,lbl_product_image_save_msg,lbl_save,lbl_auto_resize_no_gd_library"); ?><tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_product_images'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>

<td colspan="2">
<table width="100%" cellpadding="1" cellspacing="1">
<tr>
<td width="50%" valign="top" align="left">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/top_message_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<script type="text/javascript">
//<![CDATA[
var  old_widthT = "<?php echo $this->_tpl_vars['product']['images']['T']['new_x']; ?>
";
var  old_heightT = "<?php echo $this->_tpl_vars['product']['images']['T']['new_y']; ?>
";
var  old_widthP = "<?php echo $this->_tpl_vars['product']['images']['P']['new_x']; ?>
";
var  old_heightP = "<?php echo $this->_tpl_vars['product']['images']['P']['new_y']; ?>
";
var  geid = "<?php echo $this->_tpl_vars['geid']; ?>
";
var lbl_modified = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_modified'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var  lbl_no_image_uploaded= "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_no_image_uploaded'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";

var  change_buttons = new Array();
change_buttons['T'] = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_upload_thumbnail'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
change_buttons['P'] = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_upload_image'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
//]]>
</script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/image_manipulations.js"></script>


<table cellspacing="4" cellpadding="4">
<tr> 
  <td class="ProductDetails" valign="top"><table class="geid-checkbox"><tr><?php if ($this->_tpl_vars['geid'] != ''): ?><td class="geid-checkbox"><input type="checkbox" value="Y" name="fields[image]" id="fields_image" /></td><?php endif; ?><td><font class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_image']; ?>
:</font></td></tr></table></td>
  <?php if ($this->_tpl_vars['product']['is_pimage']): ?><?php $this->assign('no_delete', ""); ?><?php else: ?><?php $this->assign('no_delete', 'Y'); ?><?php endif; ?>
</tr>
<tr>
  <td class="ProductDetails" valign="top" align="left">
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/edit_product_image.tpl", 'smarty_include_vars' => array('type' => 'P','id' => $this->_tpl_vars['product']['productid'],'button_name' => $this->_tpl_vars['lng']['lbl_save'],'idtag' => 'edit_product_image','image' => $this->_tpl_vars['product']['image']['P'],'already_loaded' => $this->_tpl_vars['product']['is_image_P'],'image_x' => $this->_tpl_vars['product']['images']['P']['new_x'],'image_y' => $this->_tpl_vars['product']['images']['P']['new_y'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <br />
  <span id="original_image_descr_P"><?php if ($this->_tpl_vars['product']['is_pimage']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/image_property2.tpl", 'smarty_include_vars' => array('image' => $this->_tpl_vars['product']['image']['P'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_no_image_uploaded']; ?>
<?php endif; ?></span>
  <span id="modified_image_descr_P" style="display: none;"></span>
  <br /><br />
  </td>
</tr>

<tr>
<td class="ProductDetails" valign="top" align="left" style="padding: 4px 4px 10px 4px;">
  <input id="change_image" type="button" value="<?php if ($this->_tpl_vars['product']['is_pimage']): ?><?php echo $this->_tpl_vars['lng']['lbl_change_image']; ?>
<?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_upload_image']; ?>
<?php endif; ?>" onclick='javascript: popup_image_selection("P", "<?php echo $this->_tpl_vars['product']['productid']; ?>
", "edit_product_image");' />
<br />

</td>
</tr>

<tr <?php if (! $this->_tpl_vars['product']['is_pimage']): ?>style="display: none;" id="edit_product_image_reset2"<?php endif; ?>>
  <td class="SubHeaderLine" style="BACKGROUND-COLOR: #d3d3d3;"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /><br /></td>
</tr> 

<tr id="edit_product_image_reset" style="display: none;">
<td class="ProductDetails" valign="top" align="left" style="padding: 10px 4px 4px 4px;">
  <input id="Pimage_reset" type="button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_reset_image'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="javascript: $('#edit_product_image').attr('src', '<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif');popup_image_selection_reset('P', '<?php echo $this->_tpl_vars['product']['productid']; ?>
', 'edit_product_image'); reset_descr('P', 'edit_product_image', old_widthP, old_heightP);" />
</td>
</tr>

<tr id="Pimage_button3" <?php if (! $this->_tpl_vars['product']['is_pimage']): ?>style="display: none;"<?php endif; ?>>
<td class="ProductDetails" valign="top" align="left">
  <input type="button" value="<?php echo $this->_tpl_vars['lng']['lbl_delete_image']; ?>
" onclick="javascript: try { delete_image('edit_product_image', 'P', '<?php echo $this->_tpl_vars['product']['productid']; ?>
', 'change_image'); } catch (err) { submitForm(this, 'delete_product_image'); }" />
</td>
</tr>
</table>


</td>

<td width="50%" valign="top" align="left">


<table cellspacing="4" cellpadding="4">
<tr>
  <td class="ProductDetails" valign="top"><table class="geid-checkbox"><tr><?php if ($this->_tpl_vars['geid'] != ''): ?><td class="geid-checkbox"><input type="checkbox" value="Y" name="fields[thumbnail]" id="fields_thumbnail" /></td><?php endif; ?><td><font class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_thumbnail']; ?>
:</font></td></tr></table></td>
  <?php if ($this->_tpl_vars['product']['is_thumbnail']): ?><?php $this->assign('no_delete', ""); ?><?php else: ?><?php $this->assign('no_delete', 'Y'); ?><?php endif; ?>
</tr>
<tr>
<td class="ProductDetails" valign="top" align="left">

  <table cellpadding="0" cellspacing="0">
  <tr><td valign="top" align="left">
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/edit_product_image.tpl", 'smarty_include_vars' => array('type' => 'T','id' => $this->_tpl_vars['product']['productid'],'delete_js' => "submitForm(this, 'delete_thumbnail');",'button_name' => $this->_tpl_vars['lng']['lbl_save'],'image' => $this->_tpl_vars['product']['image']['T'],'already_loaded' => $this->_tpl_vars['product']['is_image_T'],'image_x' => "product.images.T.new_x",'image_y' => $this->_tpl_vars['product']['images']['T']['new_y'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <br />
  <span id="original_image_descr_T"><?php if ($this->_tpl_vars['product']['is_thumbnail']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/image_property2.tpl", 'smarty_include_vars' => array('image' => $this->_tpl_vars['product']['image']['T'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_no_thumbnail_uploaded']; ?>
<?php endif; ?></span>
  <span id="modified_image_descr_T" style="display: none;"></span>
  </td>
  <td class="Product_Details" valign="top" align="left">
    &nbsp;&nbsp;
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/tooltip_js.tpl", 'smarty_include_vars' => array('text' => $this->_tpl_vars['lng']['txt_need_thumbnail_help'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </td>
  </tr>
  </table>

  <span class="ImageNotes"><?php echo $this->_tpl_vars['lng']['lbl_thumbnail_msg']; ?>
</span>

</td>
</tr>

<tr>
<td class="ProductDetails" valign="top" align="left" <?php if (! $this->_tpl_vars['product']['is_pimage'] || ! $this->_tpl_vars['gdlib_enabled']): ?>style="padding: 4px 4px 10px 4px;"<?php endif; ?>>
  <input id="change_thumbnail" type="button" value="<?php if ($this->_tpl_vars['product']['is_thumbnail']): ?><?php echo $this->_tpl_vars['lng']['lbl_change_thumbnail']; ?>
<?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_upload_thumbnail']; ?>
<?php endif; ?>" onclick='javascript: popup_image_selection("T", "<?php echo $this->_tpl_vars['product']['productid']; ?>
", "edit_image");' />
</td>
</tr>

<?php if ($this->_tpl_vars['gdlib_enabled']): ?>

<tr id="tr_generate_thumbnail" <?php if (! $this->_tpl_vars['product']['is_pimage']): ?>style="display: none;"<?php endif; ?>>
<td class="ProductDetails" nowrap="nowrap" valign="top" align="left" style="padding: 4px 4px 10px 4px;">
  <input type="button" id="generate_thumbnail" value="<?php echo $this->_tpl_vars['lng']['lbl_generate_thumbnail']; ?>
" onclick="javascript: try { gen_thumbnail('<?php echo $this->_tpl_vars['product']['productid']; ?>
'); } catch(err) { submitForm(this, 'generate_thumbnail'); };" />
</td>
</tr>

<?php endif; ?>

<tr <?php if (! $this->_tpl_vars['product']['is_thumbnail']): ?>style="display: none;" id="edit_image_reset2"<?php endif; ?>>
  <td class="SubHeaderLine" style="BACKGROUND-COLOR: #d3d3d3"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>

<tr id="edit_image_reset" style="display: none;">
<td class="ProductDetails" valign="top" align="left" style="padding: 10px 4px 4px 4px;">
  <input id="Timage_reset" type="button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_reset_thumbnail'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="javascript: $('#edit_image').attr('src', '<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif'); popup_image_selection_reset('T', '<?php echo $this->_tpl_vars['product']['productid']; ?>
', 'edit_image'); reset_descr('T', 'edit_image', old_widthT, old_heightT);" />
</td>
</tr>

<tr id="Timage_button3" <?php if (! $this->_tpl_vars['product']['is_thumbnail']): ?>style="display: none;"<?php endif; ?>>
<td class="ProductDetails" valign="top" align="left">
  <input type="button" value="<?php echo $this->_tpl_vars['lng']['lbl_delete_thumbnail']; ?>
" onclick="javascript: try { delete_image('edit_image', 'T', '<?php echo $this->_tpl_vars['product']['productid']; ?>
', 'change_thumbnail'); } catch (err) { submitForm(this, 'delete_thumbnail'); }" />
</td>
</tr>

</table>


</td>
</tr>
</table>

</td>
</tr>

<tr id="ajax_message" style="display: none">
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2" class="ImageNotes" nowrap="nowrap" align="center">

  <div id="ajax-dialog-message">
    <div id="ajax-dialog-main" class="box message-i">
<a href="javascript:void(0);" class="close-link" onclick="javascript: $('#ajax_message').hide();"><img id="ajax-dialog-img" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_close'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="close-img-i" /></a><span id="ajax-dialog-content"></span>
    </div>
  </div>

  </td>
</tr>

<tr id='image_save_msg' style="display: none;">
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2" class="ImageNotes" nowrap="nowrap" align="center"><br /><br /><?php echo $this->_tpl_vars['lng']['lbl_product_image_save_msg']; ?>
<br /><br /><input type="submit" value=" <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_save'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 " /></td>
</tr>

<?php if (! $this->_tpl_vars['gdlib_enabled']): ?>
<tr>
<?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
<td colspan="2" class="ImageNotes"><?php echo $this->_tpl_vars['lng']['lbl_auto_resize_no_gd_library']; ?>
</td>
</tr>
<?php endif; ?>