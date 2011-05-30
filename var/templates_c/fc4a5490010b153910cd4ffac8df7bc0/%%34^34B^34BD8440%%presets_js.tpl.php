<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from presets_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'presets_js.tpl', 10, false),array('modifier', 'wm_remove', 'presets_js.tpl', 13, false),array('modifier', 'strip_tags', 'presets_js.tpl', 16, false),)), $this); ?>
<?php func_load_lang($this, "presets_js.tpl","lbl_no_items_have_been_selected,lbl_required_field_is_empty,lbl_field_required,lbl_field_format_is_invalid,txt_required_fields_not_completed,txt_email_invalid,lbl_error,lbl_warning,lbl_ok,lbl_yes,lbl_no,txt_ajax_error_note,lbl_blockui_default_message"); ?><script type="text/javascript">
//<![CDATA[
var number_format_dec = '<?php echo $this->_tpl_vars['number_format_dec']; ?>
';
var number_format_th = '<?php echo $this->_tpl_vars['number_format_th']; ?>
';
var number_format_point = '<?php echo $this->_tpl_vars['number_format_point']; ?>
';
var store_language = '<?php echo ((is_array($_tmp=$this->_tpl_vars['store_language'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var xcart_web_dir = "<?php echo ((is_array($_tmp=$this->_tpl_vars['xcart_web_dir'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var images_dir = "<?php echo ((is_array($_tmp=$this->_tpl_vars['ImagesDir'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_no_items_have_been_selected = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_no_items_have_been_selected'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var current_area = '<?php echo $this->_tpl_vars['usertype']; ?>
';
var skin_dir = '<?php echo ((is_array($_tmp=$this->_tpl_vars['SkinDir'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_required_field_is_empty = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_required_field_is_empty'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_field_required = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_field_required'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var lbl_field_format_is_invalid = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_field_format_is_invalid'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var txt_required_fields_not_completed = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_required_fields_not_completed'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
<?php if ($this->_tpl_vars['use_email_validation'] != 'N'): ?>
var txt_email_invalid = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_email_invalid'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var email_validation_regexp = new RegExp("<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['email_validation_regexp'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", "gi");
<?php endif; ?>
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
var txt_ajax_error_note = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_ajax_error_note'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var is_admin_editor = <?php if ($this->_tpl_vars['is_admin_editor']): ?>true<?php else: ?>false<?php endif; ?>;
var lbl_blockui_default_message = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_blockui_default_message'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var current_location = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['current_area'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
//]]>
</script>