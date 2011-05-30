<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:19
         compiled from admin/main/conf_fields_validation_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'admin/main/conf_fields_validation_js.tpl', 14, false),array('modifier', 'wm_remove', 'admin/main/conf_fields_validation_js.tpl', 14, false),array('modifier', 'escape', 'admin/main/conf_fields_validation_js.tpl', 14, false),)), $this); ?>
<?php func_load_lang($this, "admin/main/conf_fields_validation_js.tpl","err_invalid_field_data"); ?><script type="text/javascript">
//<![CDATA[
var email_validation_regexp = /<?php echo $this->_tpl_vars['email_validation_regexp']; ?>
/gi

var validationFields = [
<?php if ($this->_tpl_vars['configuration']): ?>
<?php $_from = $this->_tpl_vars['configuration']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['conf_var']):
?>
<?php if ($this->_tpl_vars['conf_var']['validation']): ?>
<?php $this->assign('opt_comment', "opt_".($this->_tpl_vars['conf_var']['name'])); ?>
  {name: '<?php echo $this->_tpl_vars['conf_var']['name']; ?>
', validation: "<?php echo $this->_tpl_vars['conf_var']['validation']; ?>
", comment: "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng'][$this->_tpl_vars['opt_comment']])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['conf_var']['comment']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['conf_var']['comment'])))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
"},
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
  {}
];

var invalid_parameter_text = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['err_invalid_field_data'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
//]]>
</script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/conf_fields_validation.js"></script>