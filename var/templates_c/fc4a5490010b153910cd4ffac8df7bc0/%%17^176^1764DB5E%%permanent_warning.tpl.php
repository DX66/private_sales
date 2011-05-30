<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:19
         compiled from permanent_warning.tpl */ ?>
<?php if ($this->_tpl_vars['permanent_warning']): ?>
<?php echo '<ol class="pw">'; ?><?php $_from = $this->_tpl_vars['permanent_warning']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['pw']):
?><?php echo '<li'; ?><?php if ($this->_tpl_vars['k'] == 0): ?><?php echo ' class="first-child"'; ?><?php endif; ?><?php echo '>'; ?><?php echo $this->_tpl_vars['pw']; ?><?php echo '</li>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ol>'; ?>

<?php endif; ?>