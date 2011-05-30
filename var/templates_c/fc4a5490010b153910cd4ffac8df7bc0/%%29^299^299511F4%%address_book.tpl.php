<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:24
         compiled from customer/main/address_book.tpl */ ?>
<?php func_load_lang($this, "customer/main/address_book.tpl","lbl_select_address,lbl_edit_address_book,lbl_address_book"); ?><?php if ($this->_tpl_vars['mode'] == 'select'): ?>
  <h1><?php echo $this->_tpl_vars['lng']['lbl_select_address']; ?>
</h1>
  <div><a href="address_book.php"<?php if ($this->_tpl_vars['is_modal_popup']): ?> onclick="javascript: self.location='address_book.php';"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_edit_address_book']; ?>
</a></div>
<?php else: ?>
  <h1><?php echo $this->_tpl_vars['lng']['lbl_address_book']; ?>
</h1>
<?php endif; ?>

<br />

<ul class="address-container<?php if ($this->_tpl_vars['mode'] == 'select'): ?> popup-address<?php endif; ?>">
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/address_box.tpl", 'smarty_include_vars' => array('add_new' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php if ($this->_tpl_vars['addresses']): ?>
    <?php $_from = $this->_tpl_vars['addresses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a']):
?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/address_box.tpl", 'smarty_include_vars' => array('address' => $this->_tpl_vars['a'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endforeach; endif; unset($_from); ?>
  <?php endif; ?>
</ul>