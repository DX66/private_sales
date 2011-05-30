<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/main/buy_now.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/main/buy_now.tpl', 15, false),array('modifier', 'escape', 'customer/main/buy_now.tpl', 26, false),array('modifier', 'substitute', 'customer/main/buy_now.tpl', 79, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/buy_now.tpl","lbl_quantity,txt_out_of_stock,lbl_product_quantity_from_to,txt_out_of_stock,txt_need_min_amount"); ?><div class="buy-now">

<script type="text/javascript">
//<![CDATA[
products_data[<?php echo $this->_tpl_vars['product']['productid']; ?>
].quantity = <?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['avail'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
products_data[<?php echo $this->_tpl_vars['product']['productid']; ?>
].min_quantity = <?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['appearance']['min_quantity'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
//]]>
</script>

  <?php $this->assign('product_key', ($this->_tpl_vars['product']['productid'])."_".($this->_tpl_vars['product']['add_date'])."_".($this->_tpl_vars['featured'])); ?>
  <?php if ($this->_tpl_vars['product']['appearance']['buy_now_form_enabled']): ?>

    <form name="orderform_<?php echo $this->_tpl_vars['product_key']; ?>
" method="<?php if ($this->_tpl_vars['product']['appearance']['buy_now_cart_enabled']): ?>post<?php else: ?>get<?php endif; ?>" action="<?php if ($this->_tpl_vars['product']['appearance']['buy_now_cart_enabled']): ?>cart.php<?php else: ?>product.php<?php endif; ?>" onsubmit="javascript: return check_quantity(<?php echo $this->_tpl_vars['product']['productid']; ?>
, '<?php echo $this->_tpl_vars['featured']; ?>
')<?php if ($this->_tpl_vars['config']['General']['ajax_add2cart'] == 'Y' && $this->_tpl_vars['config']['General']['redirect_to_cart'] != 'Y' && $this->_tpl_vars['product']['appearance']['buy_now_cart_enabled']): ?> &amp;&amp; !ajax.widgets.add2cart(this)<?php endif; ?>;">
      <input type="hidden" name="mode" value="add" />
      <input type="hidden" name="productid" value="<?php echo $this->_tpl_vars['product']['productid']; ?>
" />
      <input type="hidden" name="cat" value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['cat'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['smarty_get_cat']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['smarty_get_cat'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      <input type="hidden" name="page" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['smarty_get_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      <input type="hidden" name="is_featured_product" value="<?php echo $this->_tpl_vars['featured']; ?>
" />

      <?php if ($this->_tpl_vars['active_modules']['Special_Offers'] == 'Y' && $this->_tpl_vars['product']['use_special_price'] && $this->_tpl_vars['product']['special_price'] == 0): ?>
        <input type="hidden" name="is_free_product" value="Y" />
      <?php endif; ?>

  <?php endif; ?>

  <?php if (( $this->_tpl_vars['product']['price'] == 0 && ! $this->_tpl_vars['product']['appearance']['empty_stock'] ) && ( $this->_tpl_vars['active_modules']['Special_Offers'] != 'Y' || $this->_tpl_vars['product']['use_special_price'] == '' )): ?>

    <?php $this->assign('button_href', ((is_array($_tmp=$this->_tpl_vars['smarty_get_page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html'))); ?>

    <?php if ($this->_tpl_vars['is_matrix_view']): ?>
      <div class="quantity-empty"></div>
    <?php endif; ?>

    <form action="product.php" method="get" name="buynowform<?php echo $this->_tpl_vars['product']['productid']; ?>
">
      <input type="hidden" name="productid" value="<?php echo $this->_tpl_vars['product']['productid']; ?>
" />
      <input type="hidden" name="cat" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['smarty_get_cat'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
      <input type="hidden" name="page" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['smarty_get_page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
      <input type="hidden" name="is_featured_product" value="<?php echo $this->_tpl_vars['featured']; ?>
" />
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/buy_now.tpl", 'smarty_include_vars' => array('additional_button_class' => "main-button",'type' => 'input','button_href' => $this->_tpl_vars['button_href'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </form>

  <?php else: ?>

    <?php if ($this->_tpl_vars['product']['appearance']['buy_now_cart_enabled']): ?>

      <?php if ($this->_tpl_vars['product']['appearance']['force_1_amount']): ?>

        <?php if ($this->_tpl_vars['is_matrix_view']): ?>
          <div class="quantity-empty"></div>
        <?php endif; ?>
        <input type="hidden" name="amount" value="1" />

      <?php else: ?>

        <div class="quantity">
          <span class="quantity-title"><?php echo $this->_tpl_vars['lng']['lbl_quantity']; ?>
</span>

          <?php if ($this->_tpl_vars['product']['appearance']['empty_stock']): ?>

            <span class="out-of-stock"><?php echo $this->_tpl_vars['lng']['txt_out_of_stock']; ?>
</span>

          <?php else: ?>
            
            <?php if ($this->_tpl_vars['product']['appearance']['quantity_input_box_enabled']): ?>

              <input type="text" id="product_avail_<?php echo $this->_tpl_vars['product']['productid']; ?>
<?php echo $this->_tpl_vars['featured']; ?>
" name="amount" maxlength="11" size="6" onchange="javascript: return check_quantity(<?php echo $this->_tpl_vars['product']['productid']; ?>
, '<?php echo $this->_tpl_vars['featured']; ?>
');" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['appearance']['min_quantity'])) ? $this->_run_mod_handler('default', true, $_tmp, '1') : smarty_modifier_default($_tmp, '1')); ?>
"/>

              <?php if ($this->_tpl_vars['config']['Appearance']['show_in_stock'] == 'Y'): ?>
              <span class="quantity-text"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_product_quantity_from_to'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'min', $this->_tpl_vars['product']['appearance']['min_quantity'], 'max', $this->_tpl_vars['product']['avail']) : smarty_modifier_substitute($_tmp, 'min', $this->_tpl_vars['product']['appearance']['min_quantity'], 'max', $this->_tpl_vars['product']['avail'])); ?>
</span>
              <?php endif; ?>
 
            <?php else: ?>

             <select name="amount">
               <?php unset($this->_sections['quantity']);
$this->_sections['quantity']['name'] = 'quantity';
$this->_sections['quantity']['loop'] = is_array($_loop=$this->_tpl_vars['product']['appearance']['loop_quantity']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['quantity']['start'] = (int)$this->_tpl_vars['product']['appearance']['min_quantity'];
$this->_sections['quantity']['show'] = true;
$this->_sections['quantity']['max'] = $this->_sections['quantity']['loop'];
$this->_sections['quantity']['step'] = 1;
if ($this->_sections['quantity']['start'] < 0)
    $this->_sections['quantity']['start'] = max($this->_sections['quantity']['step'] > 0 ? 0 : -1, $this->_sections['quantity']['loop'] + $this->_sections['quantity']['start']);
else
    $this->_sections['quantity']['start'] = min($this->_sections['quantity']['start'], $this->_sections['quantity']['step'] > 0 ? $this->_sections['quantity']['loop'] : $this->_sections['quantity']['loop']-1);
if ($this->_sections['quantity']['show']) {
    $this->_sections['quantity']['total'] = min(ceil(($this->_sections['quantity']['step'] > 0 ? $this->_sections['quantity']['loop'] - $this->_sections['quantity']['start'] : $this->_sections['quantity']['start']+1)/abs($this->_sections['quantity']['step'])), $this->_sections['quantity']['max']);
    if ($this->_sections['quantity']['total'] == 0)
        $this->_sections['quantity']['show'] = false;
} else
    $this->_sections['quantity']['total'] = 0;
if ($this->_sections['quantity']['show']):

            for ($this->_sections['quantity']['index'] = $this->_sections['quantity']['start'], $this->_sections['quantity']['iteration'] = 1;
                 $this->_sections['quantity']['iteration'] <= $this->_sections['quantity']['total'];
                 $this->_sections['quantity']['index'] += $this->_sections['quantity']['step'], $this->_sections['quantity']['iteration']++):
$this->_sections['quantity']['rownum'] = $this->_sections['quantity']['iteration'];
$this->_sections['quantity']['index_prev'] = $this->_sections['quantity']['index'] - $this->_sections['quantity']['step'];
$this->_sections['quantity']['index_next'] = $this->_sections['quantity']['index'] + $this->_sections['quantity']['step'];
$this->_sections['quantity']['first']      = ($this->_sections['quantity']['iteration'] == 1);
$this->_sections['quantity']['last']       = ($this->_sections['quantity']['iteration'] == $this->_sections['quantity']['total']);
?>
                 <option value="<?php echo $this->_sections['quantity']['index']; ?>
"<?php if ($this->_tpl_vars['smarty_get_quantity'] == $this->_sections['quantity']['index']): ?> selected="selected"<?php endif; ?>><?php echo $this->_sections['quantity']['index']; ?>
</option>
               <?php endfor; endif; ?>
             </select>

            <?php endif; ?>

          <?php endif; ?>

        </div>

      <?php endif; ?>

    <?php elseif ($this->_tpl_vars['product']['appearance']['empty_stock'] && ! $this->_tpl_vars['product']['variantid']): ?>

      <div class="quantity"><strong><?php echo $this->_tpl_vars['lng']['txt_out_of_stock']; ?>
</strong></div>

    <?php elseif ($this->_tpl_vars['is_matrix_view']): ?>

      <div class="quantity-empty"></div>

    <?php else: ?>

      <br />

    <?php endif; ?>

    <?php if ($this->_tpl_vars['product']['appearance']['buy_now_buttons_enabled']): ?>

      <?php if ($this->_tpl_vars['is_matrix_view']): ?>

        <div class="button-row">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/buy_now.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div>
        <?php if ($this->_tpl_vars['product']['appearance']['dropout_actions']): ?>
          <div class="button-row">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/add_to_list.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['product']['productid'],'form_name' => "orderform_".($this->_tpl_vars['product_key']),'prefix' => $this->_tpl_vars['product_key'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          </div>
        <?php elseif ($this->_tpl_vars['active_modules']['Wishlist'] && ( $this->_tpl_vars['config']['Wishlist']['add2wl_unlogged_user'] == 'Y' || $this->_tpl_vars['login'] != "" )): ?>
          <div class="button-row">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/add_to_wishlist.tpl", 'smarty_include_vars' => array('href' => "javascript: submitForm(document.orderform_".($this->_tpl_vars['product_key']).", 'add2wl'); return false;")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          </div>
        <?php endif; ?>

      <?php else: ?>

        <div class="buttons-row">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/buy_now.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php if ($this->_tpl_vars['product']['appearance']['dropout_actions']): ?>
            <div class="button-separator"></div>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/add_to_list.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['product']['productid'],'form_name' => "orderform_".($this->_tpl_vars['product_key']),'prefix' => $this->_tpl_vars['product_key'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php elseif ($this->_tpl_vars['active_modules']['Wishlist'] && ( $this->_tpl_vars['config']['Wishlist']['add2wl_unlogged_user'] == 'Y' || $this->_tpl_vars['login'] != "" )): ?>
            <div class="button-separator"></div>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/add_to_wishlist.tpl", 'smarty_include_vars' => array('href' => "javascript: submitForm(document.orderform_".($this->_tpl_vars['product_key']).", 'add2wl'); return false;")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php endif; ?>
        </div>
        <div class="clearing"></div>

      <?php endif; ?>

    <?php endif; ?>

    <?php if ($this->_tpl_vars['product']['min_amount'] > 1): ?>
      <div class="product-details-title"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_need_min_amount'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'items', $this->_tpl_vars['product']['min_amount']) : smarty_modifier_substitute($_tmp, 'items', $this->_tpl_vars['product']['min_amount'])); ?>
</div>
    <?php endif; ?>

  <?php endif; ?>

  <?php if ($this->_tpl_vars['product']['appearance']['buy_now_form_enabled']): ?>
    </form>
  <?php endif; ?>

</div>