<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from check_required_fields_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'check_required_fields_js.tpl', 16, false),)), $this); ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/check_required_fields_js.js"></script>
<?php if ($this->_tpl_vars['fillerror'] != ''): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mark_required_fields_js.tpl", 'smarty_include_vars' => array('form' => ((is_array($_tmp=@$this->_tpl_vars['formname'])) ? $this->_run_mod_handler('default', true, $_tmp, 'registerform') : smarty_modifier_default($_tmp, 'registerform')),'errfields' => $this->_tpl_vars['fillerror']['fields'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>