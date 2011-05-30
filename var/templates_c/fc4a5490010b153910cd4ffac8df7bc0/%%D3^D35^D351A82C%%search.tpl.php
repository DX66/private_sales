<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/search.tpl', 19, false),array('modifier', 'escape', 'customer/search.tpl', 19, false),)), $this); ?>
<?php func_load_lang($this, "customer/search.tpl","lbl_search,lbl_search,lbl_advanced_search"); ?><div class="search">
  <div class="valign-middle">
    <form method="post" action="search.php" name="productsearchform">

      <input type="hidden" name="simple_search" value="Y" />
      <input type="hidden" name="mode" value="search" />
      <input type="hidden" name="posted_data[by_title]" value="Y" />
      <input type="hidden" name="posted_data[by_descr]" value="Y" />
      <input type="hidden" name="posted_data[by_sku]" value="Y" />
      <input type="hidden" name="posted_data[search_in_subcategories]" value="Y" />
      <input type="hidden" name="posted_data[including]" value="all" />

      <?php echo '<input type="text" name="posted_data[substring]" class="text'; ?><?php if (! $this->_tpl_vars['search_prefilled']['substring']): ?><?php echo ' default-value'; ?><?php endif; ?><?php echo '" value="'; ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['search_prefilled']['substring'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_search']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_search'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '" />'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('type' => 'input','style' => 'image')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '<a href="search.php" class="search">'; ?><?php echo $this->_tpl_vars['lng']['lbl_advanced_search']; ?><?php echo '</a>'; ?>


    </form>

  </div>
</div>