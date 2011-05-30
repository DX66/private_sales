<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/buttons/buy_now.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'customer/buttons/buy_now.tpl', 5, false),)), $this); ?>
<?php func_load_lang($this, "customer/buttons/buy_now.tpl","lbl_buy_now"); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_buy_now'],'additional_button_class' => ((is_array($_tmp=$this->_tpl_vars['additional_button_class'])) ? $this->_run_mod_handler('cat', true, $_tmp, ' add-to-cart-button') : smarty_modifier_cat($_tmp, ' add-to-cart-button')))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>