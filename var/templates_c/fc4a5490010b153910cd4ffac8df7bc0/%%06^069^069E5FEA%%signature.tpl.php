<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/html/signature.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'mail/html/signature.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "mail/html/signature.tpl","eml_signature,lbl_phone,lbl_fax,lbl_url"); ?><hr size="1" noshade="noshade" />
<?php echo $this->_tpl_vars['lng']['eml_signature']; ?>

<p><font size="2">
<?php if ($this->_tpl_vars['config']['Company']['company_name']): ?><?php echo $this->_tpl_vars['config']['Company']['company_name']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['config']['Company']['company_phone']): ?><?php echo $this->_tpl_vars['lng']['lbl_phone']; ?>
: <?php echo $this->_tpl_vars['config']['Company']['company_phone']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['config']['Company']['company_fax']): ?><?php echo $this->_tpl_vars['lng']['lbl_fax']; ?>
:   <?php echo $this->_tpl_vars['config']['Company']['company_fax']; ?>
<br /><?php endif; ?>
<?php echo $this->_tpl_vars['lng']['lbl_url']; ?>
: <a href="<?php echo $this->_tpl_vars['http_location']; ?>
/" target="_blank"><?php echo ((is_array($_tmp=@$this->_tpl_vars['config']['Company']['company_website'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['http_location']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['http_location'])); ?>
</a>
</font></p>