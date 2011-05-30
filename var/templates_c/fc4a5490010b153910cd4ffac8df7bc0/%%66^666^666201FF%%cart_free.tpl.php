<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/cart_free.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'modules/Special_Offers/customer/cart_free.tpl', 17, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/cart_free.tpl","lbl_sp_cart_free_item,lbl_sp_cart_free_shipping_item,lbl_sp_cart_free_shipping_item"); ?><?php if ($this->_tpl_vars['product']['free_amount'] > 0 && $this->_tpl_vars['product']['subtotal'] == 0): ?>
  <span class="offers-free-note"><?php echo $this->_tpl_vars['lng']['lbl_sp_cart_free_item']; ?>
</span>
<?php endif; ?>
<?php if ($this->_tpl_vars['config']['Shipping']['enable_shipping'] == 'Y' && $this->_tpl_vars['product']['free_shipping_used'] != ""): ?>
  <?php if ($this->_tpl_vars['product']['free_shipping_ids']): ?>
    <?php $this->assign('free_shippings', ""); ?>
    <?php $this->assign('is_first', true); ?>
    <?php $_from = $this->_tpl_vars['shipping']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['delivery_method']):
?>
      <?php if ($this->_tpl_vars['product']['free_shipping_ids'][$this->_tpl_vars['delivery_method']['shippingid']]): ?>
        <?php if ($this->_tpl_vars['is_first']): ?>
          <?php $this->assign('is_first', false); ?>
        <?php else: ?>
          <?php $this->assign('free_shippings', ((is_array($_tmp=$this->_tpl_vars['free_shippings'])) ? $this->_run_mod_handler('cat', true, $_tmp, ",&nbsp;") : smarty_modifier_cat($_tmp, ",&nbsp;"))); ?>
        <?php endif; ?>
        <?php $this->assign('free_shippings', ((is_array($_tmp=$this->_tpl_vars['free_shippings'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['delivery_method']['shipping']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['delivery_method']['shipping']))); ?>
      <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <?php if ($this->_tpl_vars['free_shippings'] != ""): ?>
    <span class="offers-free-shipping-note"><?php echo $this->_tpl_vars['lng']['lbl_sp_cart_free_shipping_item']; ?>
</span>
    <span class="small-note">(<?php echo $this->_tpl_vars['free_shippings']; ?>
)</span>
    <?php endif; ?>
  <?php else: ?>
    <span class="offers-free-shipping-note"><?php echo $this->_tpl_vars['lng']['lbl_sp_cart_free_shipping_item']; ?>
</span>
  <?php endif; ?>
<?php endif; ?>