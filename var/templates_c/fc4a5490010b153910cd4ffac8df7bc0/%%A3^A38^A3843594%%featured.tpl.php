<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/main/featured.tpl */ ?>
<?php func_load_lang($this, "customer/main/featured.tpl","lbl_featured_products"); ?><?php if ($this->_tpl_vars['f_products'] != ""): ?>
  <?php ob_start(); ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/products.tpl", 'smarty_include_vars' => array('products' => $this->_tpl_vars['f_products'],'featured' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_featured_products'],'content' => $this->_smarty_vars['capture']['dialog'],'sort' => true,'additional_class' => "products-dialog dialog-featured-list")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>