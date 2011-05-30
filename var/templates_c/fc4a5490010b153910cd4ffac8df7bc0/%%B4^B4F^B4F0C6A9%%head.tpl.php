<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/head.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/head.tpl', 23, false),array('modifier', 'escape', 'customer/head.tpl', 23, false),)), $this); ?>
<?php func_load_lang($this, "customer/head.tpl","lbl_register,lbl_logoff,lbl_my_account,lbl_need_help"); ?><div class="line0">

  <div class="logo">
    <a href="<?php echo $this->_tpl_vars['catalogs']['customer']; ?>
/home.php"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/xlogo.gif" alt="" /></a>
  </div>

  <div class="line1">

  <?php if (( $this->_tpl_vars['main'] != 'cart' || $this->_tpl_vars['cart_empty'] ) && $this->_tpl_vars['main'] != 'checkout'): ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/language_selector.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <div class="auth-row">
    <?php if ($this->_tpl_vars['login'] == ''): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/login_link.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      |
      <a href="register.php"><?php echo $this->_tpl_vars['lng']['lbl_register']; ?>
</a>
    <?php else: ?>
      <span><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['fullname'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['login']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['login'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span>
      <a href="<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
/login.php?mode=logout"><?php echo $this->_tpl_vars['lng']['lbl_logoff']; ?>
</a>
      |
      <a href="register.php?mode=update"><?php echo $this->_tpl_vars['lng']['lbl_my_account']; ?>
</a>
    <?php endif; ?>
      |
      <a href="help.php" class="last"><?php echo $this->_tpl_vars['lng']['lbl_need_help']; ?>
</a>
    </div>

  <?php endif; ?>

  </div>

  <div class="line2">

    <?php if (( $this->_tpl_vars['main'] != 'cart' || $this->_tpl_vars['cart_empty'] ) && $this->_tpl_vars['main'] != 'checkout'): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/phones.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  </div>

  <div class="line3">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/tabs.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </div>

</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/noscript.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>