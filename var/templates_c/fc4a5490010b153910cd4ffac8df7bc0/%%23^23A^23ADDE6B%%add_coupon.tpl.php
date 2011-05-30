<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Discount_Coupons/add_coupon.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'modules/Discount_Coupons/add_coupon.tpl', 17, false),)), $this); ?>
<?php func_load_lang($this, "modules/Discount_Coupons/add_coupon.tpl","txt_add_coupon_header,txt_gcheckout_add_coupon_note,lbl_redeem_discount_coupon,lbl_coupon_code,lbl_redeem_discount_coupon"); ?>
<p class="text-block"><?php echo $this->_tpl_vars['lng']['txt_add_coupon_header']; ?>
</p>

<?php ob_start(); ?>

  <?php if ($this->_tpl_vars['gcheckout_enabled']): ?>
    <p class="text-block"><?php echo $this->_tpl_vars['lng']['txt_gcheckout_add_coupon_note']; ?>
</p>
  <?php endif; ?>

  <form action="cart.php" name="couponform">
    <input type="hidden" name="mode" value="add_coupon" />

    <table cellspacing="0" class="data-table" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_redeem_discount_coupon'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
      <tr>
        <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_coupon_code']; ?>
</td>
        <td><input type="text" size="32" name="coupon" /></td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td class="button-row"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/submit.tpl", 'smarty_include_vars' => array('type' => 'input')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
      </tr>
    </table>

  </form>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_redeem_discount_coupon'],'content' => $this->_smarty_vars['capture']['dialog'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>