<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:22
         compiled from customer/phones.tpl */ ?>
<?php func_load_lang($this, "customer/phones.tpl","lbl_phone_1_title,lbl_phone_2_title"); ?><div class="phones">

  <?php if ($this->_tpl_vars['config']['Company']['company_phone']): ?>
    <span class="first"><?php echo $this->_tpl_vars['lng']['lbl_phone_1_title']; ?>
: <?php echo $this->_tpl_vars['config']['Company']['company_phone']; ?>
</span>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['config']['Company']['company_phone_2']): ?>
    <span class="last"><?php echo $this->_tpl_vars['lng']['lbl_phone_2_title']; ?>
: <?php echo $this->_tpl_vars['config']['Company']['company_phone_2']; ?>
</span>
  <?php endif; ?>

</div>
