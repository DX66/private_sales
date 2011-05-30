<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/ajax.add2cart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'customer/ajax.add2cart.tpl', 7, false),array('modifier', 'escape', 'customer/ajax.add2cart.tpl', 7, false),array('function', 'load_defer', 'customer/ajax.add2cart.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "customer/ajax.add2cart.tpl","lbl_added,lbl_error"); ?><?php if (! ( $_COOKIE['robot'] == 'X-Cart Catalog Generator' && $_COOKIE['is_robot'] == 'Y' )): ?>
<?php ob_start(); ?>
var lbl_added = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_added'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_error = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var redirect_to_cart = <?php if ($this->_tpl_vars['config']['General']['redirect_to_cart'] == 'Y'): ?>true<?php else: ?>false<?php endif; ?>;
<?php $this->_smarty_vars['capture']['add2cart'] = ob_get_contents(); ob_end_clean(); ?>
<?php echo smarty_function_load_defer(array('file' => 'add2cart','direct_info' => $this->_smarty_vars['capture']['add2cart'],'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "js/ajax.add2cart.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "js/ajax.product.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "js/ajax.products.js",'type' => 'js'), $this);?>

<?php endif; ?>