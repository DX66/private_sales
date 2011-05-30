<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/meta.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/meta.tpl', 5, false),array('function', 'meta', 'customer/meta.tpl', 13, false),)), $this); ?>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Language" content="<?php echo $this->_tpl_vars['shop_language']; ?>
" />
<?php if ($this->_tpl_vars['printable']): ?>
  <meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
<?php else: ?>
  <?php echo smarty_function_meta(array('type' => 'description','page_type' => $this->_tpl_vars['meta_page_type'],'page_id' => $this->_tpl_vars['meta_page_id']), $this);?>

  <?php echo smarty_function_meta(array('type' => 'keywords','page_type' => $this->_tpl_vars['meta_page_type'],'page_id' => $this->_tpl_vars['meta_page_id']), $this);?>

<?php endif; ?>