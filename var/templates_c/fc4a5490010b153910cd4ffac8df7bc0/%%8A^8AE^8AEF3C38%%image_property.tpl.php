<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/image_property.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'main/image_property.tpl', 6, false),array('modifier', 'replace', 'main/image_property.tpl', 7, false),array('function', 'byte_format', 'main/image_property.tpl', 6, false),)), $this); ?>
<?php func_load_lang($this, "main/image_property.tpl","lbl_image_size,lbl_image_type"); ?><?php if ($this->_tpl_vars['image'] && $this->_tpl_vars['image']['image_type'] != '' && $this->_tpl_vars['image']['image_size'] > 0): ?>
  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_image_size'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
: <?php echo $this->_tpl_vars['image']['image_x']; ?>
x<?php echo $this->_tpl_vars['image']['image_y']; ?>
, <?php echo smarty_function_byte_format(array('value' => $this->_tpl_vars['image']['image_size'],'format' => 'k'), $this);?>
Kb
  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_image_type'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['image']['image_type'])) ? $this->_run_mod_handler('replace', true, $_tmp, "image/", "") : smarty_modifier_replace($_tmp, "image/", "")); ?>

<?php endif; ?>