<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:36
         compiled from customer/main/register_address_info.tpl */ ?>
<?php func_load_lang($this, "customer/main/register_address_info.tpl","lbl_billing_address,lbl_ship_to_different_address"); ?><?php if ($this->_tpl_vars['need_address_info']): ?>

  <?php if ($this->_tpl_vars['hide_header'] == ''): ?>
    <tr>
      <td colspan="3" class="register-section-title">
        <div><label><?php echo $this->_tpl_vars['lng']['lbl_billing_address']; ?>
</label></div>
      </td>
    </tr>
  <?php endif; ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/address_fields.tpl", 'smarty_include_vars' => array('default_fields' => $this->_tpl_vars['address_fields'],'address' => $this->_tpl_vars['userinfo']['address']['B'],'id_prefix' => 'b_','name_prefix' => "address_book[B]",'zip_section' => 'billing')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <?php if ($this->_tpl_vars['config']['Shipping']['need_shipping_section'] == 'Y'): ?>

    <?php if ($this->_tpl_vars['hide_header'] == ''): ?>
      <tr>
        <td class="register-section-title register-exp-section<?php if (! $this->_tpl_vars['ship2diff']): ?> register-sec-minimized<?php endif; ?>" colspan="3">
          <div>
            <label class="pointer" for="ship2diff"><?php echo $this->_tpl_vars['lng']['lbl_ship_to_different_address']; ?>
</label>
            <input type="checkbox" id="ship2diff" name="ship2diff" value="Y"<?php if ($this->_tpl_vars['ship2diff']): ?> checked="checked"<?php endif; ?> />
          </div>
        </td>
      </tr>
    <?php endif; ?>

    </tbody>
    <tbody id="ship2diff_box">

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/address_fields.tpl", 'smarty_include_vars' => array('default_fields' => $this->_tpl_vars['address_fields'],'address' => $this->_tpl_vars['userinfo']['address']['S'],'id_prefix' => 's_','name_prefix' => "address_book[S]",'zip_section' => 'shipping')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    </tbody>
    <tbody>

  <?php endif; ?>
<?php endif; ?>