<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from modules/Special_Offers/product_modify.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'modules/Special_Offers/product_modify.tpl', 20, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/product_modify.tpl","lbl_sp_apply_offers_discounts,lbl_sp_give_bp_for_each_item"); ?><tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><hr /></td>
</tr>
<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[sp_data][sp_discount_avail]" /></td><?php endif; ?>
  <td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_sp_apply_offers_discounts']; ?>
:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="sp_data[sp_discount_avail]" value="Y"<?php if ($this->_tpl_vars['product']['productid'] == "" || $this->_tpl_vars['product']['sp_discount_avail'] == 'Y'): ?> checked="checked"<?php endif; ?> />
  </td>
</tr>
<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[sp_data][bonus_points]" /></td><?php endif; ?>
  <td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_sp_give_bp_for_each_item']; ?>
:</td>
  <td class="ProductDetails">
  <input type="text" name="sp_data[bonus_points]" size="18" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['bonus_points'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
" />
  </td>
</tr>