<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/main/products.tpl */ ?>
<?php if ($this->_tpl_vars['products']): ?>
  <?php if ($this->_tpl_vars['config']['General']['ajax_add2cart'] == 'Y' && $this->_tpl_vars['config']['General']['redirect_to_cart'] != 'Y'): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/ajax.add2cart.tpl", 'smarty_include_vars' => array('_include_once' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['active_modules']['Customer_Reviews'] && $this->_tpl_vars['config']['Customer_Reviews']['ajax_rating'] == 'Y'): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Customer_Reviews/ajax.rating.tpl", 'smarty_include_vars' => array('_include_once' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['active_modules']['Feature_Comparison'] && ! $this->_tpl_vars['printable'] && $this->_tpl_vars['products_has_fclasses']): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Feature_Comparison/compare_selected_button.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['config']['Appearance']['products_per_row'] > 1 && ( $this->_tpl_vars['featured'] == 'Y' || $this->_tpl_vars['config']['Appearance']['featured_only_multicolumn'] == 'N' )): ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/products_t.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <?php else: ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/products_list.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <?php endif; ?>

  <?php if ($this->_tpl_vars['active_modules']['Feature_Comparison'] && ! $this->_tpl_vars['printable'] && $this->_tpl_vars['products_has_fclasses']): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Feature_Comparison/compare_selected_button.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endif; ?>

<?php endif; ?>