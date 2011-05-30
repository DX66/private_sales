<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/main/products_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'customer/main/products_list.tpl', 19, false),array('modifier', 'default', 'customer/main/products_list.tpl', 25, false),array('modifier', 'amp', 'customer/main/products_list.tpl', 40, false),array('modifier', 'escape', 'customer/main/products_list.tpl', 43, false),array('function', 'interline', 'customer/main/products_list.tpl', 23, false),array('function', 'currency', 'customer/main/products_list.tpl', 71, false),array('function', 'alter_currency', 'customer/main/products_list.tpl', 72, false),array('function', 'include_cache', 'customer/main/products_list.tpl', 112, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/products_list.tpl","lbl_see_details,lbl_sku,lbl_our_price,lbl_market_price,lbl_save_price,lbl_enter_your_price,lbl_enter_your_price_note"); ?><div class="products products-list">
  <?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['products'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['products']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['product']):
        $this->_foreach['products']['iteration']++;
?>

<script type="text/javascript">
//<![CDATA[
products_data[<?php echo $this->_tpl_vars['product']['productid']; ?>
] = {};
//]]>
</script>

    <?php if ($this->_tpl_vars['active_modules']['Product_Configurator'] && $this->_tpl_vars['is_pconf'] && $this->_tpl_vars['current_product']): ?>
      <?php $this->assign('url', "product.php?productid=".($this->_tpl_vars['product']['productid'])."&amp;pconf=".($this->_tpl_vars['current_product']['productid'])."&amp;slot=".($this->_tpl_vars['slot'])); ?>
    <?php else: ?>
      <?php $this->assign('url', "product.php?productid=".($this->_tpl_vars['product']['productid'])."&amp;cat=".($this->_tpl_vars['cat'])."&amp;page=".($this->_tpl_vars['navigation_page'])); ?>
      <?php if ($this->_tpl_vars['featured'] == 'Y'): ?>
        <?php $this->assign('url', ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('cat', true, $_tmp, "&amp;featured=Y") : smarty_modifier_cat($_tmp, "&amp;featured=Y"))); ?>
      <?php endif; ?>
    <?php endif; ?>

    <div<?php echo smarty_function_interline(array('name' => 'products','additional_class' => 'item'), $this);?>
>

      <div class="image"<?php if ($this->_tpl_vars['config']['Appearance']['thumbnail_width'] > 0 || $this->_tpl_vars['product']['tmbn_x'] > 0): ?> style="width: <?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['max_images_width'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['Appearance']['thumbnail_width']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['Appearance']['thumbnail_width'])))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['product']['tmbn_x']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['product']['tmbn_x'])); ?>
px;"<?php endif; ?>>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "product_thumbnail.tpl", 'smarty_include_vars' => array('productid' => $this->_tpl_vars['product']['productid'],'image_x' => $this->_tpl_vars['product']['tmbn_x'],'image_y' => $this->_tpl_vars['product']['tmbn_y'],'product' => $this->_tpl_vars['product']['product'],'tmbn_url' => $this->_tpl_vars['product']['tmbn_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a>

        <?php if ($this->_tpl_vars['active_modules']['Special_Offers']): ?>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/product_offer_thumb.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
" class="see-details"><?php echo $this->_tpl_vars['lng']['lbl_see_details']; ?>
</a>

        <?php if ($this->_tpl_vars['active_modules']['Feature_Comparison']): ?>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Feature_Comparison/compare_checkbox.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

      </div>
      <div class="details"<?php if ($this->_tpl_vars['config']['Appearance']['thumbnail_width'] > 0 || $this->_tpl_vars['product']['tmbn_x'] > 0): ?> style="margin-left: <?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['max_images_width'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['Appearance']['thumbnail_width']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['Appearance']['thumbnail_width'])))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['product']['tmbn_x']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['product']['tmbn_x'])); ?>
px;"<?php endif; ?>>

        <a href="<?php echo $this->_tpl_vars['url']; ?>
" class="product-title"><?php echo ((is_array($_tmp=$this->_tpl_vars['product']['product'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</a>

        <?php if ($this->_tpl_vars['config']['Appearance']['display_productcode_in_list'] == 'Y' && $this->_tpl_vars['product']['productcode'] != ""): ?>
          <div class="sku"><?php echo $this->_tpl_vars['lng']['lbl_sku']; ?>
: <span class="sku-value"><?php echo ((is_array($_tmp=$this->_tpl_vars['product']['productcode'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span></div>
        <?php endif; ?>

        <div class="descr"><?php echo ((is_array($_tmp=$this->_tpl_vars['product']['descr'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</div>

        <?php if ($this->_tpl_vars['product']['rating_data']): ?>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Customer_Reviews/vote_bar.tpl", 'smarty_include_vars' => array('rating' => $this->_tpl_vars['product']['rating_data'],'productid' => $this->_tpl_vars['product']['productid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <hr />

        <?php if ($this->_tpl_vars['product']['product_type'] == 'C'): ?>

          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/details.tpl", 'smarty_include_vars' => array('href' => $this->_tpl_vars['url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        <?php else: ?>

          <?php if ($this->_tpl_vars['active_modules']['Subscriptions'] != "" && ( $this->_tpl_vars['product']['catalogprice'] > 0 || $this->_tpl_vars['product']['sub_priceplan'] > 0 )): ?>

            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Subscriptions/subscription_info_inlist.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

          <?php else: ?>

            <?php if (! $this->_tpl_vars['product']['appearance']['is_auction']): ?>

              <?php if ($this->_tpl_vars['product']['appearance']['has_price']): ?>

                <div class="price-row<?php if ($this->_tpl_vars['active_modules']['Special_Offers'] != "" && $this->_tpl_vars['product']['use_special_price'] != ""): ?> special-price-row<?php endif; ?>">
                  <span class="price"><?php echo $this->_tpl_vars['lng']['lbl_our_price']; ?>
:</span> <span class="price-value"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['product']['taxed_price']), $this);?>
</span>
                  <span class="market-price"><?php echo smarty_function_alter_currency(array('value' => $this->_tpl_vars['product']['taxed_price']), $this);?>
</span>
                </div>

                <?php if ($this->_tpl_vars['product']['appearance']['has_market_price'] && $this->_tpl_vars['product']['appearance']['market_price_discount'] > 0): ?>
                  <div class="market-price">
                    <?php echo $this->_tpl_vars['lng']['lbl_market_price']; ?>
: <span class="market-price-value"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['product']['list_price']), $this);?>
</span>

                    <?php if ($this->_tpl_vars['product']['appearance']['market_price_discount'] > 0): ?>
                      <?php if ($this->_tpl_vars['config']['General']['alter_currency_symbol'] != ""): ?>, <?php endif; ?>
                      <span class="price-save"><?php echo $this->_tpl_vars['lng']['lbl_save_price']; ?>
 <?php echo $this->_tpl_vars['product']['appearance']['market_price_discount']; ?>
%</span>
                    <?php endif; ?>

                  </div>
                <?php endif; ?>

                <?php if ($this->_tpl_vars['product']['taxes']): ?>
                  <div class="taxes">
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/taxed_price.tpl", 'smarty_include_vars' => array('taxes' => $this->_tpl_vars['product']['taxes'],'is_subtax' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  </div>
                <?php endif; ?>

              <?php endif; ?>

              <?php if ($this->_tpl_vars['active_modules']['Special_Offers'] != "" && $this->_tpl_vars['product']['use_special_price'] != ""): ?>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/product_special_price.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              <?php endif; ?>

            <?php else: ?>

              <span class="price"><?php echo $this->_tpl_vars['lng']['lbl_enter_your_price']; ?>
</span><br />
              <?php echo $this->_tpl_vars['lng']['lbl_enter_your_price_note']; ?>


            <?php endif; ?>

          <?php endif; ?>

          <?php if ($this->_tpl_vars['active_modules']['Product_Configurator'] && $this->_tpl_vars['is_pconf'] && $this->_tpl_vars['current_product']): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Configurator/pconf_add_form.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php elseif ($this->_tpl_vars['product']['appearance']['buy_now_enabled'] && $this->_tpl_vars['product']['product_type'] != 'C'): ?>
            <?php if ($this->_tpl_vars['login'] != ""): ?>
              <?php echo smarty_function_include_cache(array('file' => "customer/main/buy_now.tpl",'product' => $this->_tpl_vars['product'],'cat' => $this->_tpl_vars['cat'],'featured' => $this->_tpl_vars['featured'],'is_matrix_view' => $this->_tpl_vars['is_matrix_view'],'login' => '1','smarty_get_cat' => $_GET['cat'],'smarty_get_page' => $_GET['page'],'smarty_get_quantity' => $_GET['quantity']), $this);?>

            <?php else: ?>
              <?php echo smarty_function_include_cache(array('file' => "customer/main/buy_now.tpl",'product' => $this->_tpl_vars['product'],'cat' => $this->_tpl_vars['cat'],'featured' => $this->_tpl_vars['featured'],'is_matrix_view' => $this->_tpl_vars['is_matrix_view'],'login' => "",'smarty_get_cat' => $_GET['cat'],'smarty_get_page' => $_GET['page'],'smarty_get_quantity' => $_GET['quantity']), $this);?>

            <?php endif; ?>
          <?php endif; ?>

        <?php endif; ?>

      </div>

      <div class="clearing"></div>
    </div>

  <?php endforeach; endif; unset($_from); ?>

</div>