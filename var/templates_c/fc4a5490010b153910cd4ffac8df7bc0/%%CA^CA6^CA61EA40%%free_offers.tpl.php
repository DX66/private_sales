<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/free_offers.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'modules/Special_Offers/customer/free_offers.tpl', 23, false),array('modifier', 'substitute', 'modules/Special_Offers/customer/free_offers.tpl', 31, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/free_offers.tpl","lbl_sp_available_offers,lbl_sp_click_to_add_small,lbl_sp_offers,lbl_sp_ttl_bonus_points_N,lbl_details,txt_sp_reduce_points_balance_note,lbl_sp_apply_offers,lbl_sp_ttl_bonus_points,lbl_sp_current_bp_balance,lbl_sp_remaining_bp_balance"); ?><?php if ($this->_tpl_vars['cart']['free_offers']): ?>

  <script type="text/javascript">
  var bp_balance = <?php echo $this->_tpl_vars['cart']['remained_points']; ?>
;
  var cart_free_offers = [];
  <?php $_from = $this->_tpl_vars['cart']['free_offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['offer']):
?>
  cart_free_offers['<?php echo $this->_tpl_vars['offer']['offerid']; ?>
'] = <?php echo $this->_tpl_vars['offer']['amount_min']; ?>
;
  <?php endforeach; endif; unset($_from); ?>
  </script>

  <script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/modules/Special_Offers/customer/free_offers.js"></script>

  <div>

    <strong><?php echo $this->_tpl_vars['lng']['lbl_sp_available_offers']; ?>
*</strong> (<?php echo $this->_tpl_vars['lng']['lbl_sp_click_to_add_small']; ?>
):

    <br /><br />

    <table cellpadding="3" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_offers'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">

      <?php $_from = $this->_tpl_vars['cart']['free_offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['offerid'] => $this->_tpl_vars['offer']):
?>
      <tr>
        <td>
          <input type="checkbox" name="free_offers[<?php echo $this->_tpl_vars['offer']['offerid']; ?>
]" id="free_offers_<?php echo $this->_tpl_vars['offer']['offerid']; ?>
" value="Y" onclick="javascript: add_remove_free_offer('<?php echo $this->_tpl_vars['offer']['offerid']; ?>
', this.checked);"<?php if ($this->_tpl_vars['cart']['applied_free_offers'][$this->_tpl_vars['offer']['offerid']] == 'Y'): ?> checked="checked"<?php elseif ($this->_tpl_vars['offer']['amount_min'] > $this->_tpl_vars['cart']['remained_points']): ?> disabled="disabled"<?php endif; ?> />
        </td>
        <td><label class="cart-free-offer-title" for="free_offers_<?php echo $this->_tpl_vars['offer']['offerid']; ?>
"><?php echo $this->_tpl_vars['offer']['offer_name']; ?>
</label></td>
        <td>(<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_ttl_bonus_points_N'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'amount', $this->_tpl_vars['offer']['amount_min']) : smarty_modifier_substitute($_tmp, 'amount', $this->_tpl_vars['offer']['amount_min'])); ?>
)</td>
        <td class="offers-more-info"><a href="offers.php?mode=offer&amp;offerid=<?php echo $this->_tpl_vars['offer']['offerid']; ?>
"><?php echo $this->_tpl_vars['lng']['lbl_details']; ?>
</a></td>
      </tr>
      <?php endforeach; endif; unset($_from); ?>

    </table>

  </div>

  <br />

  <div>

    <small>*&nbsp;<?php echo $this->_tpl_vars['lng']['txt_sp_reduce_points_balance_note']; ?>
</small>

  </div>

  <br />

  <div>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_sp_apply_offers'],'href' => "javascript: apply_free_offers();")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  </div>

  <br />

  <div>

    <table cellpadding="1" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_ttl_bonus_points'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">

      <tr>
        <td><?php echo $this->_tpl_vars['lng']['lbl_sp_current_bp_balance']; ?>
:</td>
        <td><strong><?php echo $this->_tpl_vars['bonus']['points']; ?>
</strong></td>
      </tr>

      <tr>
        <td><?php echo $this->_tpl_vars['lng']['lbl_sp_remaining_bp_balance']; ?>
:</td>
        <td><strong><span id="remained_bp"><?php echo $this->_tpl_vars['cart']['remained_points']; ?>
</span></strong></td>
      </tr>

    </table>

  </div>

  <hr />

<?php endif; ?>