<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from modules/Customer_Reviews/ajax.rating.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'modules/Customer_Reviews/ajax.rating.tpl', 7, false),array('modifier', 'escape', 'modules/Customer_Reviews/ajax.rating.tpl', 7, false),array('function', 'load_defer', 'modules/Customer_Reviews/ajax.rating.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "modules/Customer_Reviews/ajax.rating.tpl","lbl_rated,lbl_error,lbl_cancel_vote"); ?><?php if (! ( $_COOKIE['robot'] == 'X-Cart Catalog Generator' && $_COOKIE['is_robot'] == 'Y' )): ?>
<?php ob_start(); ?>
var lbl_rated = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_rated'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_error = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_cancel_vote = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_cancel_vote'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
<?php $this->_smarty_vars['capture']['ajax_rating'] = ob_get_contents(); ob_end_clean(); ?>
<?php echo smarty_function_load_defer(array('file' => 'ajax_rating','direct_info' => $this->_smarty_vars['capture']['ajax_rating'],'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "modules/Customer_Reviews/ajax.rating.js",'type' => 'js'), $this);?>

<?php endif; ?>