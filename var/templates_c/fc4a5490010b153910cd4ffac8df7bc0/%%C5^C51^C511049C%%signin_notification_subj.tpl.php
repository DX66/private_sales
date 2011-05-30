<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/signin_notification_subj.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'mail/signin_notification_subj.tpl', 5, false),array('modifier', 'substitute', 'mail/signin_notification_subj.tpl', 5, false),)), $this); ?>
<?php func_load_lang($this, "mail/signin_notification_subj.tpl","eml_signin_notification_subj"); ?><?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['skin_config'])), $this);?>
<?php echo $this->_tpl_vars['config']['Company']['company_name']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['eml_signin_notification_subj'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'user', $this->_tpl_vars['full_usertype']) : smarty_modifier_substitute($_tmp, 'user', $this->_tpl_vars['full_usertype'])); ?>
