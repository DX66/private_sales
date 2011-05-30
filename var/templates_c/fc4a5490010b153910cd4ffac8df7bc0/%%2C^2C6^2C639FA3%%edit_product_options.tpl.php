<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from customer/buttons/edit_product_options.tpl */ ?>
<?php func_load_lang($this, "customer/buttons/edit_product_options.tpl","lbl_edit_options"); ?><?php if (! $this->_tpl_vars['target']): ?>
  <?php $this->assign('target', 'cart'); ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_edit_options'],'href' => "javascript: popupOpen('popup_poptions.php?target=".($this->_tpl_vars['target'])."&amp;id=".($this->_tpl_vars['id'])."');",'style' => 'link','link_href' => "popup_poptions.php?target=".($this->_tpl_vars['target'])."&amp;id=".($this->_tpl_vars['id']),'target' => '_blank')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>