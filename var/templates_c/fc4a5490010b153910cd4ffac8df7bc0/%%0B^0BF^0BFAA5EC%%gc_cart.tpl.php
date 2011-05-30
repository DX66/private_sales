<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Gift_Certificates/gc_cart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'currency', 'modules/Gift_Certificates/gc_cart.tpl', 51, false),array('function', 'alter_currency', 'modules/Gift_Certificates/gc_cart.tpl', 52, false),)), $this); ?>
<?php func_load_lang($this, "modules/Gift_Certificates/gc_cart.tpl","lbl_purchased,lbl_gift_certificate,lbl_recipient,lbl_email,lbl_mail_address,lbl_phone,lbl_amount"); ?><?php if ($this->_tpl_vars['giftcerts_data'] != ""): ?>

  <?php $_from = $this->_tpl_vars['giftcerts_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['gcindex'] => $this->_tpl_vars['gc']):
?>

    <table cellspacing="0" class="item giftcert-item width-100">
      <tr>
        <td class="image">
          <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" />
        </td>
        <td class="details">

          <?php if ($this->_tpl_vars['g']['amount_purchased'] > 1): ?>
            <div class="product-details-title"><?php echo $this->_tpl_vars['lng']['lbl_purchased']; ?>
</div>
          <?php endif; ?>

          <div class="product-title"><?php echo $this->_tpl_vars['lng']['lbl_gift_certificate']; ?>
</div>

          <div class="giftcert-item-row">
            <span class="giftcert-item-subtitle"><?php echo $this->_tpl_vars['lng']['lbl_recipient']; ?>
:</span>
            <?php echo $this->_tpl_vars['gc']['recipient']; ?>

          </div>

          <?php if ($this->_tpl_vars['gc']['send_via'] == 'E'): ?>
            <div class="giftcert-item-row">
              <span class="giftcert-item-subtitle"><?php echo $this->_tpl_vars['lng']['lbl_email']; ?>
:</span>
              <?php echo $this->_tpl_vars['gc']['recipient_email']; ?>

            </div>

          <?php elseif ($this->_tpl_vars['gc']['send_via'] == 'P'): ?>

            <div class="giftcert-item-row">
              <span class="giftcert-item-subtitle"><?php echo $this->_tpl_vars['lng']['lbl_mail_address']; ?>
:</span>
              <?php echo $this->_tpl_vars['gc']['recipient_address']; ?>
, <?php echo $this->_tpl_vars['gc']['recipient_city']; ?>
, <?php if ($this->_tpl_vars['config']['General']['use_counties'] == 'Y'): ?><?php echo $this->_tpl_vars['gc']['recipient_countyname']; ?>
 <?php endif; ?><?php echo $this->_tpl_vars['gc']['recipient_state']; ?>
 <?php echo $this->_tpl_vars['gc']['recipient_country']; ?>
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/zipcode.tpl", 'smarty_include_vars' => array('val' => $this->_tpl_vars['giftcert']['recipient_zipcode'],'zip4' => $this->_tpl_vars['giftcert']['recipient_zip4'],'static' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </div>

            <?php if ($this->_tpl_vars['gc']['recipient_phone']): ?>
              <div class="giftcert-item-row">
                <span class="giftcert-item-subtitle"><?php echo $this->_tpl_vars['lng']['lbl_phone']; ?>
:</span>
                <?php echo $this->_tpl_vars['gc']['recipient_phone']; ?>

              </div>
            <?php endif; ?>

          <?php endif; ?>

          <div class="giftcert-item-row">
            <span class="giftcert-item-subtitle"><?php echo $this->_tpl_vars['lng']['lbl_amount']; ?>
:</span>
            <span class="price"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['gc']['amount']), $this);?>
</span>
            <span class="market-price"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['gc']['amount']), $this);?>
</span>
          </div>

        </td>
      </tr>

      <?php if ($this->_tpl_vars['active_modules']['Wishlist'] != "" && $this->_tpl_vars['wl_giftcerts'] != ""): ?>

        <tr>
          <td class="buttons-row">

            <?php if ($this->_tpl_vars['giftregistry'] == ""): ?>
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/delete_item.tpl", 'smarty_include_vars' => array('href' => "cart.php?mode=wldelete&wlitem=".($this->_tpl_vars['gc']['wishlistid'])."&eventid=".($this->_tpl_vars['eventid']),'style' => 'link','additional_button_class' => "simple-delete-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php endif; ?>

          </td>
          <td class="buttons-row">

            <?php if ($this->_tpl_vars['allow_edit'] == 'Y'): ?>
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/modify.tpl", 'smarty_include_vars' => array('href' => "giftcert.php?gcindex=".($this->_tpl_vars['gc']['wishlistid'])."&action=wl",'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              <div class="button-separator"></div>
              <div class="button-separator"></div>
              <div class="button-separator"></div>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['login']): ?>

              <?php if ($this->_tpl_vars['giftregistry'] == ""): ?>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/add_to_cart.tpl", 'smarty_include_vars' => array('href' => "cart.php?mode=wl2cart&wlitem=".($this->_tpl_vars['gc']['wishlistid']),'additional_button_class' => "light-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              <?php else: ?>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/add_to_cart.tpl", 'smarty_include_vars' => array('href' => "cart.php?mode=wl2cart&fwlitem=".($this->_tpl_vars['gc']['wishlistid'])."&eventid=".($this->_tpl_vars['eventid']),'additional_button_class' => "light-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              <?php endif; ?>

            <?php endif; ?>

          </td>
        </tr>

        <?php if ($this->_tpl_vars['active_modules']['Gift_Registry']): ?>
          <tr>
            <td>&nbsp;</td>
            <td>
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Gift_Registry/giftreg_wishlist.tpl", 'smarty_include_vars' => array('wlitem_data' => $this->_tpl_vars['gc'],'is_gc' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </td>
          </tr>
        <?php endif; ?>

      <?php else: ?>

        <tr>
          <td class="buttons-row">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/delete_item.tpl", 'smarty_include_vars' => array('href' => "giftcert.php?mode=delgc&gcindex=".($this->_tpl_vars['gcindex']),'style' => 'link','additional_button_class' => "simple-delete-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
           </td>
          <td class="buttons-row">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/modify.tpl", 'smarty_include_vars' => array('href' => "giftcert.php?gcindex=".($this->_tpl_vars['gcindex']),'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          </td>
        </tr>

      <?php endif; ?>

    </table>

    <hr />

  <?php endforeach; endif; unset($_from); ?>

<?php endif; ?>