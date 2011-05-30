<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from main/evaluation.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'main/evaluation.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "main/evaluation.tpl","txt_reg_wrong_domain,txt_reg_not_registered"); ?><?php if ($this->_tpl_vars['shop_evaluation'] && $this->_tpl_vars['main'] == 'top_info'): ?>
<div class="evaluation-notice">
<?php if ($this->_tpl_vars['shop_evaluation'] == 'WRONG_DOMAIN'): ?>
  <?php if ($this->_tpl_vars['txt_reg_wrong_domain']): ?>
  <?php echo $this->_tpl_vars['txt_reg_wrong_domain']; ?>

  <?php else: ?>
  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_reg_wrong_domain'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'license_url', $this->_tpl_vars['license_url'], 'wrong_domain', $this->_tpl_vars['wrong_domain'], 'http_location', $this->_tpl_vars['http_location']) : smarty_modifier_substitute($_tmp, 'license_url', $this->_tpl_vars['license_url'], 'wrong_domain', $this->_tpl_vars['wrong_domain'], 'http_location', $this->_tpl_vars['http_location'])); ?>

  <?php endif; ?>
<?php else: ?>
  <?php if ($this->_tpl_vars['txt_reg_not_registered']): ?>
  <?php echo $this->_tpl_vars['txt_reg_not_registered']; ?>

  <?php else: ?>
  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_reg_not_registered'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'http_location', $this->_tpl_vars['http_location']) : smarty_modifier_substitute($_tmp, 'http_location', $this->_tpl_vars['http_location'])); ?>

  <?php endif; ?>
<?php endif; ?>
</div>
<?php endif; ?>