<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:22
         compiled from customer/bottom.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'customer/bottom.tpl', 10, false),)), $this); ?>
<?php func_load_lang($this, "customer/bottom.tpl","lbl_contact_us"); ?><div class="box">
  <ul class="helpbox">
    <li><a href="help.php?section=contactus&amp;mode=update"><?php echo $this->_tpl_vars['lng']['lbl_contact_us']; ?>
</a></li>
    <?php $_from = $this->_tpl_vars['pages_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p']):
?>
      <?php if ($this->_tpl_vars['p']['show_in_menu'] == 'Y'): ?>
        <li><a href="pages.php?pageid=<?php echo $this->_tpl_vars['p']['pageid']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['p']['title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</a></li>
      <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
  </ul>

  <div class="subbox">
    <div class="left"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/prnotice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
    <div class="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "copyright.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
  </div>
</div>