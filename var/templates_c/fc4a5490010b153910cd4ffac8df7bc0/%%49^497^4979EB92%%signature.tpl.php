<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/signature.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'mail_truncate', 'mail/signature.tpl', 10, false),)), $this); ?>
<?php func_load_lang($this, "mail/signature.tpl","eml_signature,lbl_phone,lbl_fax,lbl_url"); ?>--
<?php echo $this->_tpl_vars['lng']['eml_signature']; ?>


<?php if ($this->_tpl_vars['config']['Company']['company_name']): ?><?php echo $this->_tpl_vars['config']['Company']['company_name']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['config']['Company']['company_phone']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_phone'])) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['config']['Company']['company_phone']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['config']['Company']['company_fax']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_fax'])) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php echo $this->_tpl_vars['config']['Company']['company_fax']; ?>

<?php endif; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_url'])) ? $this->_run_mod_handler('mail_truncate', true, $_tmp) : smarty_modifier_mail_truncate($_tmp)); ?>
<?php if ($this->_tpl_vars['config']['Company']['company_website'] != ""): ?> <?php echo $this->_tpl_vars['config']['Company']['company_website']; ?>
 (<?php echo $this->_tpl_vars['http_location']; ?>
)<?php else: ?><?php echo $this->_tpl_vars['http_location']; ?>
<?php endif; ?>