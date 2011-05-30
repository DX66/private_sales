<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:57
         compiled from customer/main/popup_login.tpl */ ?>
<?php func_load_lang($this, "customer/main/popup_login.tpl","lbl_sign_in,lbl_authentication"); ?><h1><?php echo $this->_tpl_vars['lng']['lbl_sign_in']; ?>
</h1>

<p id="login-error" class="error-message" style="display:none;"></p>

<?php ob_start(); ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/login_form.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_authentication'],'content' => $this->_smarty_vars['capture']['dialog'],'noborder' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>