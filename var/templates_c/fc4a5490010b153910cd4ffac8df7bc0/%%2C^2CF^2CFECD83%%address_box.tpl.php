<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:24
         compiled from customer/main/address_box.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/main/address_box.tpl', 6, false),array('modifier', 'escape', 'customer/main/address_box.tpl', 9, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/address_box.tpl","lbl_add_new_address,lbl_billing_and_shipping_address,lbl_billing_address,lbl_shipping_address,lbl_change,lbl_delete,txt_are_you_sure"); ?><?php if ($this->_tpl_vars['mode'] == 'select'): ?>
  <form action="popup_address.php" method="post" name="address_<?php echo ((is_array($_tmp=@$this->_tpl_vars['address']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
" id="address_<?php echo ((is_array($_tmp=@$this->_tpl_vars['address']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
      <input type="hidden" name="mode" value="select" />
      <input type="hidden" name="id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['address']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
" />
      <input type="hidden" name="type" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['type'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('default', true, $_tmp, 'B') : smarty_modifier_default($_tmp, 'B')); ?>
" />
      <input type="hidden" name="for" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['for'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('default', true, $_tmp, 'cart') : smarty_modifier_default($_tmp, 'cart')); ?>
" />
  </form>
<?php endif; ?>

<li id="address_box_<?php echo ((is_array($_tmp=@$this->_tpl_vars['address']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
" class="address-box<?php if ($this->_tpl_vars['address']['id'] == $this->_tpl_vars['current']): ?> address-current<?php endif; ?><?php if ($this->_tpl_vars['mode'] == 'select' && ! $this->_tpl_vars['add_new'] && $this->_tpl_vars['address']['id'] != $this->_tpl_vars['current']): ?> address-select cursor-hover pointer<?php endif; ?>" <?php if ($this->_tpl_vars['mode'] == 'select' && $this->_tpl_vars['type'] != '' && $this->_tpl_vars['for'] != '' && $this->_tpl_vars['checkout_module'] != 'One_Page_Checkout' || $this->_tpl_vars['address']['id'] <= 0): ?>onclick="javascript: $('#address_<?php echo ((is_array($_tmp=@$this->_tpl_vars['address']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
').submit();"<?php endif; ?>>
  <div class="address-bg">
    <div class="address-main">

      <?php if ($this->_tpl_vars['add_new']): ?>
          <div class="new-address-label">
            <a class="new-address" href="popup_address.php" onclick="javascript: return !popupOpen('popup_address.php<?php if ($this->_tpl_vars['mode'] == 'select'): ?>?return=select&for=<?php echo $this->_tpl_vars['for']; ?>
&type=<?php echo $this->_tpl_vars['type']; ?>
<?php endif; ?>');"><?php echo $this->_tpl_vars['lng']['lbl_add_new_address']; ?>
</a>
          </div>

      <?php else: ?>

        <?php if ($this->_tpl_vars['address']['default_s'] == 'Y' || $this->_tpl_vars['address']['default_b'] == 'Y'): ?>
          <div class="address-default">
            <?php if ($this->_tpl_vars['address']['default_s'] == 'Y' && $this->_tpl_vars['address']['default_b'] == 'Y'): ?>
              <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_billing.png" width="19" height="15" alt="" />
              <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_shipping.png" width="16" height="9" alt="" />
              <?php if ($this->_tpl_vars['mode'] != 'select'): ?><?php echo $this->_tpl_vars['lng']['lbl_billing_and_shipping_address']; ?>
<?php endif; ?>
            <?php elseif ($this->_tpl_vars['address']['default_b'] == 'Y'): ?>
              <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_billing.png" width="19" height="15" alt="" />
              <?php if ($this->_tpl_vars['mode'] != 'select'): ?><?php echo $this->_tpl_vars['lng']['lbl_billing_address']; ?>
<?php endif; ?>
            <?php else: ?>
              <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_shipping.png" width="16" height="9" alt="" />
              <?php if ($this->_tpl_vars['mode'] != 'select'): ?><?php echo $this->_tpl_vars['lng']['lbl_shipping_address']; ?>
<?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/address_details_html.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        <br />
        <div class="buttons-row buttons-auto-separator">
          <?php if (! ( $this->_tpl_vars['checkout_module'] == 'One_Page_Checkout' && $this->_tpl_vars['for'] == 'cart' )): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_change'],'href' => "javascript: popupOpen('popup_address.php?id=".($this->_tpl_vars['address']['id'])."');",'link_href' => "popup_address.php?id=".($this->_tpl_vars['address']['id']),'target' => '_blank')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['address']['default_s'] != 'Y' && $this->_tpl_vars['address']['default_b'] != 'Y'): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_delete'],'href' => "javascript: if (confirm('".($this->_tpl_vars['lng']['txt_are_you_sure'])."')) self.location = 'address_book.php?mode=delete&amp;id=".($this->_tpl_vars['address']['id'])."'")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php endif; ?>
        </div>

      <?php endif; ?>

    </div>
  </div>
</li>