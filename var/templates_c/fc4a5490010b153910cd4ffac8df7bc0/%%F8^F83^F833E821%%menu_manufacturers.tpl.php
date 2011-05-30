<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from modules/Manufacturers/menu_manufacturers.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'modules/Manufacturers/menu_manufacturers.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "modules/Manufacturers/menu_manufacturers.tpl","lbl_other_manufacturers,lbl_manufacturers"); ?><?php if ($this->_tpl_vars['manufacturers_menu'] != ''): ?>

  <?php ob_start(); ?>
    <ul>

      <?php $_from = $this->_tpl_vars['manufacturers_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
         <li><a href="manufacturers.php?manufacturerid=<?php echo $this->_tpl_vars['m']['manufacturerid']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['m']['manufacturer'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</a></li>
      <?php endforeach; endif; unset($_from); ?>

      <?php if ($this->_tpl_vars['show_other_manufacturers']): ?>
        <li><a href="manufacturers.php"><?php echo $this->_tpl_vars['lng']['lbl_other_manufacturers']; ?>
</a></li>
      <?php endif; ?>

    </ul>
  <?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/menu_dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_manufacturers'],'content' => $this->_smarty_vars['capture']['menu'],'additional_class' => "menu-manufacturers")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>