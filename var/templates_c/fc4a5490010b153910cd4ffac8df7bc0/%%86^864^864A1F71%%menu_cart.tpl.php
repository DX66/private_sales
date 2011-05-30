<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/menu_cart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/menu_cart.tpl', 15, false),array('modifier', 'cat', 'customer/menu_cart.tpl', 39, false),)), $this); ?>
<?php func_load_lang($this, "customer/menu_cart.tpl","lbl_friends_wish_list,lbl_wish_list,lbl_gift_registry,lbl_subscriptions_info"); ?><?php if ($this->_tpl_vars['config']['General']['ajax_add2cart'] == 'Y' && $this->_tpl_vars['main'] != 'cart' && $this->_tpl_vars['main'] != 'checkout'): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/ajax.minicart.tpl", 'smarty_include_vars' => array('_include_once' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php ob_start(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/cart_checkout_links.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<ul>
  <?php if ($this->_tpl_vars['active_modules']['Wishlist'] && $this->_tpl_vars['wlid'] != ""): ?>
    <li><a href="cart.php?mode=friend_wl&amp;wlid=<?php echo ((is_array($_tmp=$this->_tpl_vars['wlid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo $this->_tpl_vars['lng']['lbl_friends_wish_list']; ?>
</a></li>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['active_modules']['Wishlist']): ?>
    <li><a href="cart.php?mode=wishlist"><?php echo $this->_tpl_vars['lng']['lbl_wish_list']; ?>
</a></li>

    <?php if ($this->_tpl_vars['active_modules']['Gift_Registry']): ?>
      <li><a href="giftreg_manage.php"><?php echo $this->_tpl_vars['lng']['lbl_gift_registry']; ?>
</a></li>
    <?php endif; ?>
  
  <?php endif; ?>

  <?php if ($this->_tpl_vars['user_subscription'] != ""): ?>
    <li><a href="orders.php?mode=subscriptions"><?php echo $this->_tpl_vars['lng']['lbl_subscriptions_info']; ?>
</a></li>
  <?php endif; ?>

</ul>
<?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['config']['General']['ajax_add2cart'] == 'Y' && $this->_tpl_vars['main'] != 'cart' && $this->_tpl_vars['main'] != 'checkout' && $this->_tpl_vars['minicart_total_items'] > 0): ?>
  <?php $this->assign('additional_class', "menu-minicart ajax-minicart"); ?>
<?php else: ?>
  <?php $this->assign('additional_class', "menu-minicart"); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['minicart_total_items'] > 0): ?>
  <?php $this->assign('additional_class', ((is_array($_tmp=$this->_tpl_vars['additional_class'])) ? $this->_run_mod_handler('cat', true, $_tmp, ' full') : smarty_modifier_cat($_tmp, ' full'))); ?>
<?php else: ?>
  <?php $this->assign('additional_class', ((is_array($_tmp=$this->_tpl_vars['additional_class'])) ? $this->_run_mod_handler('cat', true, $_tmp, ' empty') : smarty_modifier_cat($_tmp, ' empty'))); ?>
<?php endif; ?>
<?php ob_start();
$_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/minicart_total.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->assign('title', ob_get_contents()); ob_end_clean();
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/menu_dialog.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['menu'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>