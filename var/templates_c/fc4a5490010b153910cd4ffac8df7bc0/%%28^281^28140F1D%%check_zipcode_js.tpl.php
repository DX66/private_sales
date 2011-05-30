<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from check_zipcode_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'check_zipcode_js.tpl', 7, false),array('modifier', 'escape', 'check_zipcode_js.tpl', 7, false),array('modifier', 'strip_tags', 'check_zipcode_js.tpl', 27, false),array('modifier', 'substitute', 'check_zipcode_js.tpl', 33, false),array('modifier', 'lower', 'check_zipcode_js.tpl', 35, false),)), $this); ?>
<?php func_load_lang($this, "check_zipcode_js.tpl","lbl_or,txt_error_common_zip_code,txt_error_at_zip_code,txt_error_ca_zip_code,txt_error_ch_zip_code,txt_error_de_zip_code,txt_error_lu_zip_code,txt_error_us_zip_code,lbl_billing_address,lbl_shipping_address"); ?><script type="text/javascript">
//<![CDATA[
var config_default_country = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['config']['General']['default_country'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";

<?php if ($this->_tpl_vars['config']['General']['zip4_support'] == 'Y'): ?>
  <?php $this->assign('zip4_format', "(".($this->_tpl_vars['lng']['lbl_or'])." 5+4) "); ?>
<?php endif; ?>

// used in check_zip_code_field() from check_zipcode.js
// note: you should update language variables after change this table
// Please, update func_check_zip php function after any changes
<?php echo '
var check_zip_code_rules = {
  AT: { rules: [/^.{4}$/gi] },
  CA: { rules: [/^.{6,7}$/gi] },
  CH: { rules: [/^.{4}$/gi] },
  DE: { rules: [/^\\d{5}$/gi] },
  LU: { rules: [/^\\d{4}$/gi] },
  US: { rules: [/^\\d{5}$/gi] }
};
'; ?>


var txt_error_common_zip_code = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_common_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.AT.error = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_at_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.CA.error = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_ca_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.CH.error = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_ch_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.DE.error = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_de_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.LU.error = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_lu_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.US.error = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_us_zip_code'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'zip4_format', $this->_tpl_vars['zip4_format']) : smarty_modifier_substitute($_tmp, 'zip4_format', $this->_tpl_vars['zip4_format'])))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';

var lbl_billing_address = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_billing_address'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
 ';
var lbl_shipping_address = '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_shipping_address'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
 ';
var check_zip_code_posted_alert = '';
//]]>
</script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/check_zipcode.js"></script>
