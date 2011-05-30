<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/cart_price_special.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'currency', 'modules/Special_Offers/customer/cart_price_special.tpl', 6, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/cart_price_special.tpl","lbl_sp_common_price,lbl_sp_special_price"); ?><?php if ($this->_tpl_vars['product']['saved_original_price'] && $this->_tpl_vars['product']['special_price_used'] && $this->_tpl_vars['product']['saved_original_price'] != $this->_tpl_vars['price']): ?>
  <?php echo $this->_tpl_vars['lng']['lbl_sp_common_price']; ?>
: <span class="offers-common-price"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['product']['saved_original_price']), $this);?>
</span><br />
  <?php echo $this->_tpl_vars['lng']['lbl_sp_special_price']; ?>
:
<?php endif; ?>