<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:22
         compiled from customer/noscript.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/noscript.tpl', 7, false),)), $this); ?>
<?php func_load_lang($this, "customer/noscript.tpl","txt_noscript_warning,txt_noscript_warning"); ?><noscript>
  <div class="noscript-warning">
    <div class="content"><?php echo ((is_array($_tmp=@$this->_tpl_vars['content'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['txt_noscript_warning']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['txt_noscript_warning'])); ?>
</div>
  </div>
</noscript>