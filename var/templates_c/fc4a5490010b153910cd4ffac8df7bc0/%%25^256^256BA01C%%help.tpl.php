<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from admin/help.tpl */ ?>
<?php func_load_lang($this, "admin/help.tpl","lbl_help,lbl_xcart_faqs,lbl_xcart_manuals,lbl_community_forums,lbl_support_helpdesk,lbl_license_agreement,lbl_purchase_paid_license,lbl_services"); ?><li>
  <?php echo $this->_tpl_vars['lng']['lbl_help']; ?>

  <div>
    <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_xcart_faqs']; ?>
</a>
    <a href="http://help.qtmsoft.com/index.php?title=X-Cart:User_manual_contents" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_xcart_manuals']; ?>
</a>
    <a href="http://forum.x-cart.com/" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_community_forums']; ?>
</a>
    <a href="http://secure.qtmsoft.com/" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_support_helpdesk']; ?>
</a>
    <a href="http://www.x-cart.com/software_license_agreement.html" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_license_agreement']; ?>
</a>
    <?php if ($this->_tpl_vars['shop_evaluation']): ?>
      <a href="http://www.x-cart.com/purchasing_shopping_cart_software.html" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_purchase_paid_license']; ?>
</a>
    <?php endif; ?>
    <a href="http://www.x-cart.com/services.html" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_services']; ?>
</a>
  </div>
</li>
