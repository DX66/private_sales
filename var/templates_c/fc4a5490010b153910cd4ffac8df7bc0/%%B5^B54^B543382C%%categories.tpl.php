<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/categories.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'interline', 'customer/categories.tpl', 17, false),array('modifier', 'escape', 'customer/categories.tpl', 17, false),array('modifier', 'amp', 'customer/categories.tpl', 17, false),)), $this); ?>
<?php func_load_lang($this, "customer/categories.tpl","lbl_categories"); ?><?php if ($this->_tpl_vars['categories_menu_list'] != '' || $this->_tpl_vars['fancy_use_cache']): ?>
<?php ob_start(); ?>

<?php if ($this->_tpl_vars['active_modules']['Flyout_Menus']): ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Flyout_Menus/categories.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $this->assign('additional_class', "menu-fancy-categories-list"); ?>

<?php else: ?>

  <ul>
    <?php $_from = $this->_tpl_vars['categories_menu_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['categories'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['categories']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['c']):
        $this->_foreach['categories']['iteration']++;
?>
      <li<?php echo smarty_function_interline(array('name' => 'categories'), $this);?>
><a href="home.php?cat=<?php echo $this->_tpl_vars['c']['categoryid']; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['c']['category'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['c']['category'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</a></li>
    <?php endforeach; endif; unset($_from); ?>
  </ul>

  <?php $this->assign('additional_class', "menu-categories-list"); ?>

<?php endif; ?>

<?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/menu_dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_categories'],'content' => $this->_smarty_vars['capture']['menu'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>