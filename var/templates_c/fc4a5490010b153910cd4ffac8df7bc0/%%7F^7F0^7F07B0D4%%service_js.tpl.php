<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/service_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/service_js.tpl', 13, false),array('modifier', 'wm_remove', 'customer/service_js.tpl', 14, false),array('modifier', 'replace', 'customer/service_js.tpl', 19, false),array('modifier', 'strip_tags', 'customer/service_js.tpl', 24, false),array('modifier', 'default', 'customer/service_js.tpl', 63, false),array('function', 'load_defer', 'customer/service_js.tpl', 42, false),array('function', 'getvar', 'customer/service_js.tpl', 99, false),)), $this); ?>
<?php func_load_lang($this, "customer/service_js.tpl","lbl_no_items_have_been_selected,lbl_product_minquantity_error,lbl_product_maxquantity_error,lbl_product_quantity_type_error,lbl_required_field_is_empty,lbl_field_required,lbl_field_format_is_invalid,txt_required_fields_not_completed,lbl_blockui_default_message,lbl_error,lbl_warning,lbl_ok,lbl_yes,lbl_no,txt_minicart_total_note,txt_ajax_error_note,txt_email_invalid,txt_this_form_is_for_demo_purposes,txt_this_link_is_for_demo_purposes"); ?><?php ob_start(); ?>
<?php if ($this->_tpl_vars['__frame_not_allowed'] && ! $_GET['open_in_layer']): ?>
if (top != self)
    top.location = self.location;
<?php endif; ?>
var number_format_dec = '<?php echo $this->_tpl_vars['number_format_dec']; ?>
';
var number_format_th = '<?php echo $this->_tpl_vars['number_format_th']; ?>
';
var number_format_point = '<?php echo $this->_tpl_vars['number_format_point']; ?>
';
var store_language = '<?php echo ((is_array($_tmp=$this->_tpl_vars['store_language'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var xcart_web_dir = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['xcart_web_dir'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var images_dir = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['ImagesDir'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
<?php if ($this->_tpl_vars['AltImagesDir']): ?>var alt_images_dir = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['AltImagesDir'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";<?php endif; ?>
var lbl_no_items_have_been_selected = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_no_items_have_been_selected'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var current_area = '<?php echo $this->_tpl_vars['usertype']; ?>
';
var currency_format = "<?php echo ((is_array($_tmp=$this->_tpl_vars['config']['General']['currency_format'])) ? $this->_run_mod_handler('replace', true, $_tmp, '$', $this->_tpl_vars['config']['General']['currency_symbol']) : smarty_modifier_replace($_tmp, '$', $this->_tpl_vars['config']['General']['currency_symbol'])); ?>
";
var lbl_product_minquantity_error = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_product_minquantity_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_product_maxquantity_error = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_product_maxquantity_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_product_quantity_type_error = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_product_quantity_type_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var is_limit = <?php if ($this->_tpl_vars['config']['General']['unlimited_products'] == 'Y'): ?>false<?php else: ?>true<?php endif; ?>;
var lbl_required_field_is_empty = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_required_field_is_empty'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_field_required = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_field_required'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_field_format_is_invalid = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_field_format_is_invalid'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var txt_required_fields_not_completed = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_required_fields_not_completed'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_blockui_default_message = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_blockui_default_message'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_error = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_warning = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_warning'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_ok = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_ok'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_yes = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_yes'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_no = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_no'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var txt_minicart_total_note = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_minicart_total_note'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var txt_ajax_error_note = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_ajax_error_note'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
<?php if ($this->_tpl_vars['use_email_validation'] != 'N'): ?>
var txt_email_invalid = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_email_invalid'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var email_validation_regexp = new RegExp("<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['email_validation_regexp'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", "gi");
<?php endif; ?>
var is_admin_editor = <?php if ($this->_tpl_vars['is_admin_editor']): ?>true<?php else: ?>false<?php endif; ?>;
<?php $this->_smarty_vars['capture']['javascript_code'] = ob_get_contents(); ob_end_clean(); ?>
<?php echo smarty_function_load_defer(array('file' => 'javascript_code','direct_info' => $this->_smarty_vars['capture']['javascript_code'],'type' => 'js'), $this);?>


<?php echo smarty_function_load_defer(array('file' => "js/common.js",'type' => 'js'), $this);?>

<?php if ($this->_tpl_vars['config']['Adaptives']['is_first_start'] == 'Y'): ?>
  <?php echo smarty_function_load_defer(array('file' => "js/browser_identificator.js",'type' => 'js'), $this);?>

<?php endif; ?>

<?php if ($this->_tpl_vars['webmaster_mode'] == 'editor'): ?>
  <?php ob_start(); ?>
var catalogs = {
  admin: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['catalogs']['admin'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  provider: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['catalogs']['provider'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  customer: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['catalogs']['customer'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  partner: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['catalogs']['partner'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  images: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['ImagesDir'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  skin: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['SkinDir'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
"
};
var lng_labels = [];
<?php $_from = $this->_tpl_vars['webmaster_lng']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lbl_name'] => $this->_tpl_vars['lbl_val']):
?>
lng_labels['<?php echo $this->_tpl_vars['lbl_name']; ?>
'] = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lbl_val'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
<?php endforeach; endif; unset($_from); ?>
var page_charset = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
";
  <?php $this->_smarty_vars['capture']['webmaster_code'] = ob_get_contents(); ob_end_clean(); ?>
  <?php echo smarty_function_load_defer(array('file' => 'webmaster_code','direct_info' => $this->_smarty_vars['capture']['webmaster_code'],'type' => 'js'), $this);?>

  <?php echo smarty_function_load_defer(array('file' => "js/editor_common.js",'type' => 'js'), $this);?>

  <?php if ($this->_tpl_vars['user_agent'] == 'ns'): ?>
    <?php echo smarty_function_load_defer(array('file' => "js/editorns.js",'type' => 'js'), $this);?>

  <?php else: ?>
    <?php echo smarty_function_load_defer(array('file' => "js/editor.js",'type' => 'js'), $this);?>

  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Magnifier'] != ''): ?>
  <?php echo smarty_function_load_defer(array('file' => "lib/swfobject-min.js",'type' => 'js'), $this);?>

<?php endif; ?>

<?php echo smarty_function_load_defer(array('file' => "lib/jquery-min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jquery.bgiframe.min.js",'type' => 'js'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "jquery_ui.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_load_defer(array('file' => "js/ajax.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/cluetip/jquery.cluetip.js",'type' => 'js'), $this);?>

<?php if ($this->_tpl_vars['is_admin_preview']): ?>
  <?php ob_start(); ?>
var txt_this_form_is_for_demo_purposes = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_this_form_is_for_demo_purposes'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var txt_this_link_is_for_demo_purposes = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_this_link_is_for_demo_purposes'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
  <?php $this->_smarty_vars['capture']['admin_preview_js'] = ob_get_contents(); ob_end_clean(); ?>
  <?php echo smarty_function_load_defer(array('file' => 'admin_preview_js','direct_info' => $this->_smarty_vars['capture']['admin_preview_js'],'type' => 'js'), $this);?>

  <?php echo smarty_function_load_defer(array('file' => "js/admin_preview.js",'type' => 'js'), $this);?>

<?php endif; ?>
<?php echo smarty_function_load_defer(array('file' => "js/popup_open.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jquery.blockUI.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jquery.blockUI.defaults.js",'type' => 'js'), $this);?>


<?php echo smarty_function_load_defer(array('file' => "lib/jquery.cookie.js",'type' => 'js'), $this);?>


<?php if ($this->_tpl_vars['main'] == 'product'): ?>
  <?php echo smarty_function_getvar(array('var' => 'det_images_widget'), $this);?>

  <?php if ($this->_tpl_vars['det_images_widget'] == 'cloudzoom'): ?>
    <?php echo smarty_function_load_defer(array('file' => "lib/cloud_zoom/cloud-zoom.min.js",'type' => 'js'), $this);?>

  <?php endif; ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "onload_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>