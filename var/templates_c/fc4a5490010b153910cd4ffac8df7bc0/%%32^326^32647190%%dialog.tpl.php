<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:36
         compiled from customer/dialog.tpl */ ?>
<div class="dialog<?php if ($this->_tpl_vars['additional_class']): ?> <?php echo $this->_tpl_vars['additional_class']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['noborder']): ?> noborder<?php endif; ?><?php if ($this->_tpl_vars['sort'] && $this->_tpl_vars['printable'] != 'Y'): ?> list-dialog<?php endif; ?>">
  <?php if (! $this->_tpl_vars['noborder']): ?>
    <div class="title">
      <h2><?php echo $this->_tpl_vars['title']; ?>
</h2>
      <?php if ($this->_tpl_vars['sort'] && $this->_tpl_vars['printable'] != 'Y'): ?>
        <div class="sort-box">
          <?php if ($this->_tpl_vars['selected'] == '' && $this->_tpl_vars['direction'] == ''): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/search_sort_by.tpl", 'smarty_include_vars' => array('selected' => $this->_tpl_vars['search_prefilled']['sort_field'],'direction' => $this->_tpl_vars['search_prefilled']['sort_direction'],'url' => $this->_tpl_vars['products_sort_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php else: ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/search_sort_by.tpl", 'smarty_include_vars' => array('url' => $this->_tpl_vars['products_sort_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="content"><?php echo $this->_tpl_vars['content']; ?>
</div>
</div>