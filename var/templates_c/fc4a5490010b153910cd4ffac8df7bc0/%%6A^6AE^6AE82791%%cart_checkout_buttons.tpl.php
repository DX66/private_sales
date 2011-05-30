<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/cart_checkout_buttons.tpl */ ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/cart_checkout_buttons.tpl","lbl_sp_add_free_products,lbl_sp_unused_offers"); ?><?php if ($this->_tpl_vars['cart']['not_used_free_products']): ?>

  <div class="offers-cart-button">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_sp_add_free_products'],'href' => "offers.php?mode=add_free",'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </div>

<?php elseif ($this->_tpl_vars['customer_unused_offers']): ?>

  <div class="offers-cart-button">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_sp_unused_offers'],'href' => "offers.php?mode=unused",'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </div>

<?php endif; ?>