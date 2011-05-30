<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:11
         compiled from main/prnotice.tpl */ ?>
<?php if ($this->_tpl_vars['main'] == 'catalog' && $this->_tpl_vars['current_category']['category'] == ""): ?>
  Powered by X-Cart <a href="http://www.x-cart.com"><?php echo $this->_tpl_vars['sm_prnotice_txt']; ?>
</a>
<?php else: ?>
  Powered by X-Cart <?php echo $this->_tpl_vars['sm_prnotice_txt']; ?>

<?php endif; ?>