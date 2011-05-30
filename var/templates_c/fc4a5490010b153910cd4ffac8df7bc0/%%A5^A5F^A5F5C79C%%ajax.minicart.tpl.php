<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/ajax.minicart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'customer/ajax.minicart.tpl', 7, false),array('modifier', 'escape', 'customer/ajax.minicart.tpl', 7, false),array('function', 'load_defer', 'customer/ajax.minicart.tpl', 10, false),)), $this); ?>
<?php func_load_lang($this, "customer/ajax.minicart.tpl","lbl_error,txt_minicart_total_note"); ?><?php if (! ( $_COOKIE['robot'] == 'X-Cart Catalog Generator' && $_COOKIE['is_robot'] == 'Y' )): ?>
<?php ob_start(); ?>
var lbl_error = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var txt_minicart_total_note = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_minicart_total_note'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
<?php $this->_smarty_vars['capture']['ajax_minicart'] = ob_get_contents(); ob_end_clean(); ?>
<?php echo smarty_function_load_defer(array('file' => 'ajax_minicart','direct_info' => $this->_smarty_vars['capture']['ajax_minicart'],'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "js/ajax.minicart.js",'type' => 'js'), $this);?>

<?php endif; ?>