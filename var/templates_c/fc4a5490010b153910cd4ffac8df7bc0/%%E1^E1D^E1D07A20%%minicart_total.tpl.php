<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/minicart_total.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'currency', 'customer/minicart_total.tpl', 9, false),array('function', 'load_defer_code', 'customer/minicart_total.tpl', 23, false),)), $this); ?>
<?php func_load_lang($this, "customer/minicart_total.tpl","lbl_sp_items,txt_minicart_total_note,lbl_cart_is_empty"); ?><span class="minicart">
<?php echo ''; ?><?php if ($this->_tpl_vars['minicart_total_items'] > 0): ?><?php echo '<span class="full">'; ?><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['minicart_total_cost'],'assign' => 'total'), $this);?><?php echo '<span class="minicart-items-value">'; ?><?php echo $this->_tpl_vars['minicart_total_items']; ?><?php echo '</span>&nbsp;<span class="minicart-items-label">'; ?><?php echo $this->_tpl_vars['lng']['lbl_sp_items']; ?><?php echo '</span>&nbsp;<span class="minicart-items-delim">/</span>&nbsp;'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/tooltip_js.tpl", 'smarty_include_vars' => array('class' => "minicart-items-total help-link",'title' => $this->_tpl_vars['total'],'text' => $this->_tpl_vars['lng']['txt_minicart_total_note'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</span>'; ?><?php else: ?><?php echo '<span class="empty"><strong>'; ?><?php echo $this->_tpl_vars['lng']['lbl_cart_is_empty']; ?><?php echo '</strong></span>'; ?><?php endif; ?><?php echo ''; ?>

</span>
<?php if ($this->_tpl_vars['minicart_total_standalone']): ?>
<?php echo smarty_function_load_defer_code(array('type' => 'css'), $this);?>

<?php echo smarty_function_load_defer_code(array('type' => 'js'), $this);?>

<?php endif; ?>