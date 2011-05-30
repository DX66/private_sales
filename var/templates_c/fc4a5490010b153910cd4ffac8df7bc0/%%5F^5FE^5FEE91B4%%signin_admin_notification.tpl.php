<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/signin_admin_notification.tpl */ ?>
<?php func_load_lang($this, "mail/signin_admin_notification.tpl","eml_signin_admin_notification,lbl_profile_details"); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/mail_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<?php echo $this->_tpl_vars['lng']['eml_signin_admin_notification']; ?>


<?php echo $this->_tpl_vars['lng']['lbl_profile_details']; ?>
:
---------------------
<?php if ($this->_tpl_vars['config']['Email']['show_passwords_in_notifications'] == 'Y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/profile_data.tpl", 'smarty_include_vars' => array('show_pwd' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/profile_data.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/signature.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>