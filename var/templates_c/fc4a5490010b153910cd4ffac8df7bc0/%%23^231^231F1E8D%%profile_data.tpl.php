<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/profile_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'mail_truncate', 'mail/profile_data.tpl', 7, false),array('modifier', 'default', 'mail/profile_data.tpl', 15, false),)), $this); ?>
<?php func_load_lang($this, "mail/profile_data.tpl","lbl_account_information,lbl_username,lbl_email,lbl_password,lbl_personal_information,lbl_title,lbl_first_name,lbl_last_name,lbl_company,lbl_url,lbl_ssn,lbl_tax_number,lbl_tax_exempt,txt_tax_exemption_assigned,lbl_membership,lbl_signup_for_membership"); ?><?php echo $this->_tpl_vars['lng']['lbl_account_information']; ?>
:
---------------------
<?php if ($this->_tpl_vars['config']['email_as_login'] != 'Y'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_username'])) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['login']; ?>

<?php endif; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_email'])) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['email']; ?>

<?php if ($this->_tpl_vars['show_pwd']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_password'])) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['password']; ?>

<?php endif; ?>

<?php echo $this->_tpl_vars['lng']['lbl_personal_information']; ?>
:
---------------------
<?php if ($this->_tpl_vars['userinfo']['default_fields']['title']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_title'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['title']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['default_fields']['firstname']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_first_name'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['firstname']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['default_fields']['lastname']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_last_name'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['lastname']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['default_fields']['company']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_company'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['company']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['default_fields']['url']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_url'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['url']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['default_fields']['ssn']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_ssn'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['ssn']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['default_fields']['tax_number']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_tax_number'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['tax_number']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['tax_exempt'] == 'Y'): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_tax_exempt'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['lng']['txt_tax_exemption_assigned']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['membership']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_membership'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['membership']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['userinfo']['pending_membershipid'] != $this->_tpl_vars['userinfo']['membershipid']): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['lng']['lbl_signup_for_membership'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['userinfo']['pending_membership']; ?>

<?php endif; ?>
<?php $_from = $this->_tpl_vars['userinfo']['additional_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?><?php if ($this->_tpl_vars['v']['section'] == 'P'): ?>
<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['v']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['v']['value']; ?>

<?php endif; ?><?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['userinfo']['additional_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?><?php if ($this->_tpl_vars['v']['section'] == 'C' || $this->_tpl_vars['v']['section'] == 'A'): ?>
<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['v']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')))) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['v']['value']; ?>

<?php endif; ?><?php endforeach; endif; unset($_from); ?>
