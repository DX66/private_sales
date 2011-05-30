<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/cart_checkout_links.tpl */ ?>
<?php func_load_lang($this, "customer/cart_checkout_links.tpl","lbl_view_cart,lbl_checkout"); ?><div class="cart-checkout-links">
<?php if ($this->_tpl_vars['active_modules']['Wishlist'] != "" || $this->_tpl_vars['user_subscription'] != "" || $this->_tpl_vars['minicart_total_items'] > 0): ?>
<hr class="minicart" />
<?php endif; ?>
<?php if ($this->_tpl_vars['minicart_total_items'] > 0): ?>
  <ul>
    <li><a href="cart.php"><?php echo $this->_tpl_vars['lng']['lbl_view_cart']; ?>
</a></li>

    <?php if ($this->_tpl_vars['active_modules']['Google_Checkout'] == ""): ?>
      <li><a href="cart.php?mode=checkout"><?php echo $this->_tpl_vars['lng']['lbl_checkout']; ?>
</a></li>
    <?php endif; ?>
  </ul>
<?php endif; ?>
</div>