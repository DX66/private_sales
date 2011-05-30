<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/main/login_link.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/login_link.tpl', 5, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/login_link.tpl","lbl_sign_in,lbl_sign_in"); ?><a href="<?php echo $this->_tpl_vars['authform_url']; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sign_in'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" <?php if (! ( ( $_COOKIE['robot'] == 'X-Cart Catalog Generator' && $_COOKIE['is_robot'] == 'Y' ) || ( $this->_tpl_vars['config']['Security']['use_https_login'] == 'Y' && ! $this->_tpl_vars['is_https_zone'] ) )): ?> onclick="javascript: return !popupOpen('login.php');"<?php endif; ?><?php if ($this->_tpl_vars['classname']): ?> class="<?php echo ((is_array($_tmp=$this->_tpl_vars['classname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_sign_in']; ?>
</a>