<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:26
         compiled from customer/main/address.tpl */ ?>
<?php func_load_lang($this, "customer/main/address.tpl","lbl_edit_address,lbl_new_address,lbl_address_book,lbl_use_as_b_address,lbl_use_as_s_address,lbl_save,lbl_back"); ?>  
<?php if ($this->_tpl_vars['av_error']): ?>

  <?php if ($this->_tpl_vars['login'] != ''): ?>
    <?php $this->assign('av_script_url', "popup_address.php?id=".($this->_tpl_vars['id'])); ?>
  <?php endif; ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/register.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  
<?php else: ?>
  
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_zipcode_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "change_states_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  
  <?php if ($this->_tpl_vars['id'] > 0): ?>
    <h1><?php echo $this->_tpl_vars['lng']['lbl_edit_address']; ?>
</h1>
  <?php else: ?>
    <h1><?php echo $this->_tpl_vars['lng']['lbl_new_address']; ?>
</h1>
  <?php endif; ?>
  
  <?php if ($this->_tpl_vars['reg_error'] != ''): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mark_required_fields_js.tpl", 'smarty_include_vars' => array('form' => 'addressbook','errfields' => $this->_tpl_vars['reg_error']['fields'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <p class="error-message"><?php echo $this->_tpl_vars['reg_error']['errdesc']; ?>
</p>
  <?php endif; ?>
  
  <form action="popup_address.php" method="post" name="addressbook" onsubmit="javascript: return check_zip_code(this);"> 
  <input type="hidden" name="mode" value="<?php if ($this->_tpl_vars['id'] > 0): ?>update<?php else: ?>add<?php endif; ?>" />
  <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['id']; ?>
" />
  <input type="hidden" name="for" value="<?php echo $this->_tpl_vars['for']; ?>
" />
  
  <table cellpadding="3" cellspacing="1" width="100%" summary="<?php echo $this->_tpl_vars['lng']['lbl_address_book']; ?>
">
    
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/address_fields.tpl", 'smarty_include_vars' => array('address' => $this->_tpl_vars['address'],'name_prefix' => 'posted_data','id_prefix' => '')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  
    <?php if (! $this->_tpl_vars['is_address_book_empty'] && $this->_tpl_vars['address']['default_b'] != 'Y'): ?>
    <tr>
      <td colspan="2">&nbsp;</td>
      <td>
        <label><input type="checkbox" id="default_b" name="posted_data[default_b]" size="32" maxlength="32"<?php if ($this->_tpl_vars['address']['default_b'] == 'Y'): ?> checked="checked"<?php endif; ?>/>&nbsp;<?php echo $this->_tpl_vars['lng']['lbl_use_as_b_address']; ?>
</label>
      </td>
    </tr>
    <?php endif; ?>
  
    <?php if (! $this->_tpl_vars['is_address_book_empty'] && $this->_tpl_vars['address']['default_s'] != 'Y'): ?>
    <tr>
      <td colspan="2">&nbsp;</td>
      <td>
        <label><input type="checkbox" id="default_s" name="posted_data[default_s]" size="32" maxlength="32"<?php if ($this->_tpl_vars['address']['default_s'] == 'Y'): ?> checked="checked"<?php endif; ?>/>&nbsp;<?php echo $this->_tpl_vars['lng']['lbl_use_as_s_address']; ?>
</label>
      </td>
    </tr>
    <?php endif; ?>
  
    <?php if ($this->_tpl_vars['is_address_book_empty']): ?>
      <input type="hidden" name="posted_data[default_b]" value="Y" />
      <input type="hidden" name="posted_data[default_s]" value="Y" />
    <?php endif; ?>
  
  </table>
  <br />
  
  <div class="buttons-row buttons-auto-separator" align="center">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('type' => 'input','button_title' => $this->_tpl_vars['lng']['lbl_save'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php if ($this->_tpl_vars['return']): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_back'],'href' => "javascript: popupOpen('popup_address.php?mode=".($this->_tpl_vars['return'])."&for=".($this->_tpl_vars['for'])."&type=".($this->_tpl_vars['type'])."'); return false;")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
  </div>
  
  </form>

<?php endif; ?>