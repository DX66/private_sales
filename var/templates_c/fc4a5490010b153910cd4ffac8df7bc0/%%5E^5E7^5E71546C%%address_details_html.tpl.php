<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:43
         compiled from customer/main/address_details_html.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/address_details_html.tpl', 6, false),array('modifier', 'default', 'customer/main/address_details_html.tpl', 15, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/address_details_html.tpl","lbl_phone,lbl_fax"); ?><div class="address-line">
  <?php if ($this->_tpl_vars['default_fields']['title'] && $this->_tpl_vars['address']['title'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['address']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['firstname'] && $this->_tpl_vars['address']['firstname'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['address']['firstname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['lastname'] && $this->_tpl_vars['address']['lastname'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['address']['lastname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>
</div>

<div class="address-line">
  <?php if ($this->_tpl_vars['default_fields']['address'] && $this->_tpl_vars['address']['address'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['address']['address'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
,<br /><?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['address_2'] && $this->_tpl_vars['address']['address_2'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['address']['address_2'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
,<br /><?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['city'] && $this->_tpl_vars['address']['city'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['address']['city'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
, <?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['state'] && $this->_tpl_vars['address']['state'] != ''): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['address']['statename'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['address']['state']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['address']['state'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
, <?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['county'] && $this->_tpl_vars['address']['county'] != ''): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['address']['countyname'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['address']['county']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['address']['county'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
, <br /><?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['zipcode'] && $this->_tpl_vars['address']['zipcode'] != ''): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/zipcode.tpl", 'smarty_include_vars' => array('val' => $this->_tpl_vars['address']['zipcode'],'zip4' => $this->_tpl_vars['address']['zip4'],'static' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><br /><?php endif; ?>
  <?php if ($this->_tpl_vars['default_fields']['country'] && $this->_tpl_vars['address']['country'] != ''): ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['address']['countryname'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['address']['country']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['address']['country'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>
</div>

<div class="address-line">
  <?php if ($this->_tpl_vars['default_fields']['phone'] && $this->_tpl_vars['address']['phone'] != ''): ?><?php echo $this->_tpl_vars['lng']['lbl_phone']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['address']['phone'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?><br />
  <?php if ($this->_tpl_vars['default_fields']['fax'] && $this->_tpl_vars['address']['fax'] != ''): ?><?php echo $this->_tpl_vars['lng']['lbl_fax']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['address']['fax'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>
</div>