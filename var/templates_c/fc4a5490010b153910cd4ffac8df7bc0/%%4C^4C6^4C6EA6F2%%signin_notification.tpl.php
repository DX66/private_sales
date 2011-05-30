<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/html/signin_notification.tpl */ ?>
<?php func_load_lang($this, "mail/html/signin_notification.tpl","eml_signin_notification,lbl_your_profile"); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/html/mail_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br /><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/salutation.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['userinfo']['title'],'firstname' => $this->_tpl_vars['userinfo']['firstname'],'lastname' => $this->_tpl_vars['userinfo']['lastname'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br /><?php echo $this->_tpl_vars['lng']['eml_signin_notification']; ?>


<br /><?php echo $this->_tpl_vars['lng']['lbl_your_profile']; ?>
:

<?php if ($this->_tpl_vars['config']['Email']['show_passwords_in_user_notificat'] == 'Y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/html/profile_data.tpl", 'smarty_include_vars' => array('show_pwd' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/html/profile_data.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mail/html/signature.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
