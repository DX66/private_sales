<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from customer/printable_link.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/printable_link.tpl', 7, false),array('modifier', 'amp', 'customer/printable_link.tpl', 7, false),)), $this); ?>
<?php func_load_lang($this, "customer/printable_link.tpl","lbl_printable_version"); ?><?php if ($this->_tpl_vars['printable_link_visible']): ?>
  <div class="printable-bar">
    <a href="<?php echo $this->_tpl_vars['php_url']['url']; ?>
?printable=Y<?php if ($this->_tpl_vars['php_url']['query_string'] != ''): ?>&amp;<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['php_url']['query_string'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php endif; ?>"><?php echo $this->_tpl_vars['lng']['lbl_printable_version']; ?>
</a>
  </div>
<?php endif; ?>