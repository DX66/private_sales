<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:26
         compiled from main/zipcode.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'regex_replace', 'main/zipcode.tpl', 7, false),array('modifier', 'escape', 'main/zipcode.tpl', 7, false),)), $this); ?>
<?php if (! $this->_tpl_vars['static']): ?>

  <?php $this->assign('cntid', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, '/zipcode/', 'country') : smarty_modifier_regex_replace($_tmp, '/zipcode/', 'country')))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))); ?>
  <input type="text" id="<?php echo ((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="zipcode<?php if ($this->_tpl_vars['zip_section']): ?> <?php echo $this->_tpl_vars['zip_section']; ?>
<?php endif; ?>" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['val'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
  <?php if ($this->_tpl_vars['config']['General']['zip4_support'] == 'Y' && ! $this->_tpl_vars['nozip4']): ?>
  <?php echo ''; ?><?php $this->assign('zip4id', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, '/zipcode/', 'zip4') : smarty_modifier_regex_replace($_tmp, '/zipcode/', 'zip4')))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))); ?><?php echo ''; ?><?php $this->assign('zip4name', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['name'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, '/zipcode/', 'zip4') : smarty_modifier_regex_replace($_tmp, '/zipcode/', 'zip4')))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))); ?><?php echo '<span id="'; ?><?php echo $this->_tpl_vars['zip4id']; ?><?php echo '_container">&nbsp;-&nbsp;<input type="text" id="'; ?><?php echo $this->_tpl_vars['zip4id']; ?><?php echo '" class="zip4" name="'; ?><?php echo $this->_tpl_vars['zip4name']; ?><?php echo '" size="10" maxlength="4" value="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['zip4'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '" /></span>'; ?>

  <?php endif; ?>

<?php else: ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['val'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php if ($this->_tpl_vars['zip4'] != ''): ?>-<?php echo ((is_array($_tmp=$this->_tpl_vars['zip4'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>
<?php endif; ?>