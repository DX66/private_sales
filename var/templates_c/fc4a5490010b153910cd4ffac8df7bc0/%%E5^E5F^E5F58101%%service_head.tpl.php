<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/service_head.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'get_title', 'customer/service_head.tpl', 5, false),array('function', 'load_defer_code', 'customer/service_head.tpl', 15, false),)), $this); ?>
<?php echo smarty_function_get_title(array('page_type' => $this->_tpl_vars['meta_page_type'],'page_id' => $this->_tpl_vars['meta_page_id']), $this);?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/meta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/service_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/service_css.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['canonical_url']): ?>
  <link rel="canonical" href="<?php echo $this->_tpl_vars['current_location']; ?>
/<?php echo $this->_tpl_vars['canonical_url']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
  <base href="<?php echo $this->_tpl_vars['catalogs']['customer']; ?>
/" />
<?php endif; ?>
<?php echo smarty_function_load_defer_code(array('type' => 'css'), $this);?>

<?php echo smarty_function_load_defer_code(array('type' => 'js'), $this);?>
