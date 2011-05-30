<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from customer/main/cart_subtotal.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/cart_subtotal.tpl', 9, false),array('modifier', 'default', 'customer/main/cart_subtotal.tpl', 40, false),array('function', 'currency', 'customer/main/cart_subtotal.tpl', 13, false),array('function', 'alter_currency', 'customer/main/cart_subtotal.tpl', 14, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/cart_subtotal.tpl","lbl_total,lbl_subtotal,lbl_discount,lbl_discount_coupon,lbl_unset_coupon,lbl_unset_coupon,lbl_shipping_cost,lbl_discounted,lbl_unset_coupon,lbl_unset_coupon,lbl_discounted_subtotal,lbl_including,lbl_giftcert_discount,lbl_applied_giftcerts,lbl_unset_gc,txt_order_total_msg"); ?><div class="right-box">
<?php $this->assign('subtotal', $this->_tpl_vars['cart']['subtotal']); ?>
<?php $this->assign('discounted_subtotal', $this->_tpl_vars['cart']['discounted_subtotal']); ?>

  <table cellspacing="0" class="totals" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_total'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">

    <tr>
      <td class="total"><?php echo $this->_tpl_vars['lng']['lbl_subtotal']; ?>
:</td>
      <td class="total-value"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['cart']['display_subtotal']), $this);?>
</td>
      <td class="total-alt-value"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['cart']['display_subtotal']), $this);?>
</td>
    </tr>

<?php if ($this->_tpl_vars['cart']['discount'] > 0): ?>
    <tr>
      <td class="total-name"><?php echo $this->_tpl_vars['lng']['lbl_discount']; ?>
:</td>
      <td class="total-value"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['cart']['discount']), $this);?>
</td>
      <td class="total-alt-value"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['cart']['discount']), $this);?>
</td>
    </tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['cart']['coupon_discount'] != 0 && $this->_tpl_vars['cart']['coupon_type'] != 'free_ship'): ?>
    <tr>
      <td class="total-name dcoupons-clear">
        <?php echo $this->_tpl_vars['lng']['lbl_discount_coupon']; ?>

        <a href="cart.php?mode=unset_coupons" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_unset_coupon'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_unset_coupon'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a>:
      <br /><span class="small">#<?php echo $this->_tpl_vars['cart']['coupon']; ?>
</span>
      </td>
      <td class="total-value" valign="top"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['cart']['coupon_discount']), $this);?>
</td>
      <td class="total-alt-value" valign="top"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['cart']['coupon_discount']), $this);?>
</td>
    </tr>
<?php elseif ($this->_tpl_vars['config']['Shipping']['enable_shipping'] == 'Y' && $this->_tpl_vars['cart']['coupon_type'] == 'free_ship'): ?>
    <tr>
      <td class="total-name dcoupons-clear">
        <?php echo $this->_tpl_vars['lng']['lbl_shipping_cost']; ?>
<?php if ($this->_tpl_vars['cart']['coupon_discount'] != 0 && $this->_tpl_vars['cart']['coupon_type'] == 'free_ship'): ?> (<?php echo $this->_tpl_vars['lng']['lbl_discounted']; ?>
 <a href="cart.php?mode=unset_coupons" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_unset_coupon'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_unset_coupon'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a>)<?php endif; ?>:
      </td>
      <td class="total-value"><?php echo smarty_function_currency(array('value' => ((is_array($_tmp=@$this->_tpl_vars['cart']['display_shipping_cost'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero']))), $this);?>
</td>
      <td class="total-alt-value"><?php echo smarty_function_alter_currency(array('value' => ((is_array($_tmp=@$this->_tpl_vars['cart']['display_shipping_cost'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero']))), $this);?>
</td>
    </tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['cart']['discounted_subtotal'] != $this->_tpl_vars['cart']['subtotal']): ?>
    <tr>
      <td class="total-line" colspan="3">
        <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" />
      </td>
    </tr>

    <tr>
      <td class="total"><?php echo $this->_tpl_vars['lng']['lbl_discounted_subtotal']; ?>
:</td>
      <td class="total-value"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['cart']['display_discounted_subtotal']), $this);?>
</td>
      <td class="total-alt-value"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['cart']['display_discounted_subtotal']), $this);?>
</td>
    </tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['cart']['taxes'] && $this->_tpl_vars['config']['Taxes']['display_taxed_order_totals'] == 'Y'): ?>

    <tr>
      <td colspan="3" class="total-taxes"><?php echo $this->_tpl_vars['lng']['lbl_including']; ?>
:</td>
    </tr>

<?php $_from = $this->_tpl_vars['cart']['taxes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tax_name'] => $this->_tpl_vars['tax']):
?>
    <tr class="total-tax-line">
      <td class="total-tax-name"><?php echo $this->_tpl_vars['tax']['tax_display_name']; ?>
:</td>
      <td><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['tax']['tax_cost_no_shipping']), $this);?>
</td>
      <td><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['tax']['tax_cost_no_shipping']), $this);?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['cart']['applied_giftcerts']): ?>
    <tr>
      <td class="total-name"><?php echo $this->_tpl_vars['lng']['lbl_giftcert_discount']; ?>
:</td>
      <td class="total-value"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['cart']['giftcert_discount']), $this);?>
</td>
      <td class="total-alt-value"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['cart']['giftcert_discount']), $this);?>
</td>
    </tr>
<?php endif; ?>

  </table>

<?php if ($this->_tpl_vars['cart']['applied_giftcerts']): ?>
  <br />
  <br />
  <div class="form-text"><?php echo $this->_tpl_vars['lng']['lbl_applied_giftcerts']; ?>
:</div>
<?php $_from = $this->_tpl_vars['cart']['applied_giftcerts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['gc']):
?>
    <div class="dcoupons-clear">
      <?php echo $this->_tpl_vars['gc']['giftcert_id']; ?>

      <a href="cart.php?mode=unset_gc&amp;gcid=<?php echo $this->_tpl_vars['gc']['giftcert_id']; ?>
"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_unset_gc'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a>
       : <span class="total-name"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['gc']['giftcert_cost']), $this);?>
</span>
    </div>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['not_logged_message'] == '1'): ?>
<?php echo $this->_tpl_vars['lng']['txt_order_total_msg']; ?>

<?php endif; ?>

</div>

<input type="hidden" name="action" value="update" />

<hr />

<?php if ($this->_tpl_vars['active_modules']['Special_Offers'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/cart_bonuses.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>