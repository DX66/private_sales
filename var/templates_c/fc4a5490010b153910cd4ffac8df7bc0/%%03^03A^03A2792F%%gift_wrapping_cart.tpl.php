<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Gift_Registry/gift_wrapping_cart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'currency', 'modules/Gift_Registry/gift_wrapping_cart.tpl', 10, false),array('modifier', 'escape', 'modules/Gift_Registry/gift_wrapping_cart.tpl', 21, false),)), $this); ?>
<?php func_load_lang($this, "modules/Gift_Registry/gift_wrapping_cart.tpl","lbl_giftreg_use_wrapping,lbl_giftreg_sum_up_cost_note,lbl_giftreg_add_message,lbl_giftreg_update_giftwrap"); ?><?php if ($this->_tpl_vars['display_giftwrap_section']): ?>
<div class="giftwrapping-cart">
  <div class="giftwrap-option">
    <label for="need_giftwrap">
      <input type="checkbox" id="need_giftwrap" name="need_giftwrap" value="Y"<?php if ($this->_tpl_vars['cart']['need_giftwrap'] == 'Y'): ?> checked="checked"<?php endif; ?> />
      <?php echo $this->_tpl_vars['lng']['lbl_giftreg_use_wrapping']; ?>
<?php if ($this->_tpl_vars['cart']['taxed_giftwrap_cost'] > 0): ?> (<?php echo smarty_function_currency(array('value' => $this->_tpl_vars['cart']['taxed_giftwrap_cost'],'display_sign' => 1), $this);?>
)<?php endif; ?>
    </label>
  </div>
  <?php if ($this->_tpl_vars['cart']['taxed_giftwrap_cost'] > 0 && ! $this->_tpl_vars['single_mode'] && $this->_tpl_vars['config']['General']['sum_up_wrapping_cost'] == 'Y' && $this->_tpl_vars['cart']['is_multiorder'] == 'Y'): ?>
    <div class="giftwrap-cart-note"><?php echo $this->_tpl_vars['lng']['lbl_giftreg_sum_up_cost_note']; ?>
</div>
  <?php endif; ?>
  <?php if ($this->_tpl_vars['config']['General']['enable_greeting_message']): ?>
    <div class="giftwrap-message-text" id="giftrap_message"<?php if ($this->_tpl_vars['cart']['need_giftwrap'] != 'Y'): ?> style="display: none;"<?php endif; ?>>
      <div class="giftwrap-message-label">
        <?php echo $this->_tpl_vars['lng']['lbl_giftreg_add_message']; ?>
:
      </div>
      <textarea class="message-text" name="giftwrap_message" rows="5" cols="20"><?php echo ((is_array($_tmp=$this->_tpl_vars['cart']['giftwrap_message'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
   </div>
  <?php endif; ?>
  
  <div class="button-row">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_giftreg_update_giftwrap'],'additional_button_class' => "light-button",'href' => "javascript: $('input[name=action]', this.form).val('giftwrap_update'); this.form.submit();")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </div>

</div>
<hr />
<?php endif; ?>

<?php echo '
<script type="text/javascript">
//<![CDATA[
$("#need_giftwrap").bind(\'click\',
  function(e) {
    if ($(this).is(\':checked\') && !$(\'#giftrap_message\').is(\':visible\'))
      $(\'#giftrap_message\').fadeIn(\'fast\');
    else if ($(\'#giftrap_message\').is(\':visible\'))
      $(\'#giftrap_message\').fadeOut(\'fast\');
  }
);
//]]>
</script>
'; ?>
