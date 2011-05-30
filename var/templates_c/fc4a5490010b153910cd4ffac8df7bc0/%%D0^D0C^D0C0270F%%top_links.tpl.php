<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:24
         compiled from customer/main/top_links.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'inc', 'customer/main/top_links.tpl', 8, false),array('modifier', 'amp', 'customer/main/top_links.tpl', 10, false),array('modifier', 'wm_remove', 'customer/main/top_links.tpl', 10, false),array('modifier', 'escape', 'customer/main/top_links.tpl', 10, false),)), $this); ?>
<div id="top-links" class="ui-tabs ui-widget ui-corner-all">
  <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-corner-all">
  <?php $_from = $this->_tpl_vars['tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ind'] => $this->_tpl_vars['tab']):
?>
    <?php echo smarty_function_inc(array('value' => $this->_tpl_vars['ind'],'assign' => 'ti'), $this);?>

    <li class="ui-corner-top ui-state-default<?php if ($this->_tpl_vars['tab']['selected']): ?> ui-tabs-selected ui-state-active<?php endif; ?>">
      <a href="<?php if ($this->_tpl_vars['tab']['url']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['tab']['url'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php else: ?>#<?php echo $this->_tpl_vars['prefix']; ?>
<?php echo $this->_tpl_vars['ti']; ?>
<?php endif; ?>"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['tab']['title'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
    </li>
  <?php endforeach; endif; unset($_from); ?>
  </ul>
  <div class="ui-tabs-panel ui-widget-content"></div>
</div>