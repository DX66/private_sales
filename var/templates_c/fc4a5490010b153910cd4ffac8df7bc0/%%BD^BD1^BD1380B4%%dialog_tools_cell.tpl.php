<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from dialog_tools_cell.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'dialog_tools_cell.tpl', 9, false),array('modifier', 'escape', 'dialog_tools_cell.tpl', 9, false),)), $this); ?>
<?php if ($this->_tpl_vars['cell']['separator']): ?>
<li class="dialog-cell-separator"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" /></li>
<?php else: ?>
<li>
  <a class="dialog-cell<?php if ($this->_tpl_vars['cell']['style'] == 'hl'): ?>-hl<?php endif; ?>" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['cell']['link'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['cell']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['cell']['target'] != ""): ?> target="<?php echo $this->_tpl_vars['cell']['target']; ?>
"<?php endif; ?>>
    <?php echo $this->_tpl_vars['cell']['title']; ?>

  </a>
</li>
<?php endif; ?>