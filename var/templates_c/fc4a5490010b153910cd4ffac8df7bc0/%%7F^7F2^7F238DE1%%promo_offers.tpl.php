<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/promo_offers.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'modules/Special_Offers/customer/promo_offers.tpl', 18, false),array('modifier', 'substitute', 'modules/Special_Offers/customer/promo_offers.tpl', 34, false),array('function', 'currency', 'modules/Special_Offers/customer/promo_offers.tpl', 32, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/promo_offers.tpl","lbl_sp_get_this_offer,lbl_sp_buy_more,lbl_sp_purchase_following_products,lbl_sp_items,lbl_sp_products_from_cat_s,lbl_sp_items,lbl_or,lbl_sp_also_review_offers"); ?><?php if ($this->_tpl_vars['products'] && $this->_tpl_vars['cart']['promo_offers']): ?>

  <?php ob_start(); ?>

  <?php $_from = $this->_tpl_vars['cart']['promo_offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['offers'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['offers']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['offer']):
        $this->_foreach['offers']['iteration']++;
?>

  <?php if ($this->_tpl_vars['offer']['promo_items_amount']): ?>

  <div>

    <?php if ($this->_tpl_vars['offer']['html_items_amount']): ?>
      <?php echo $this->_tpl_vars['offer']['promo_items_amount']; ?>

    <?php else: ?>
      <tt><?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['promo_items_amount'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</tt>
    <?php endif; ?>

  </div>

  <div><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="1" height="10" alt="" /></div>

  <div>

    <strong><?php echo $this->_tpl_vars['lng']['lbl_sp_get_this_offer']; ?>
:</strong>

    <br /><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="1" height="5" alt="" /><br />

    <?php if ($this->_tpl_vars['offer']['exceed_amount'] > 0): ?>
      <?php ob_start(); ?><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['offer']['exceed_amount']), $this);?>
<?php $this->_smarty_vars['capture']['exceed_amount'] = ob_get_contents(); ob_end_clean(); ?>
      <?php $this->assign('link', "<a href=\"home.php\">".($this->_smarty_vars['capture']['exceed_amount'])."</a>"); ?>
      <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_buy_more'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'amount', $this->_tpl_vars['link']) : smarty_modifier_substitute($_tmp, 'amount', $this->_tpl_vars['link'])); ?>
<br />

      <div><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="1" height="5" alt="" /></div>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['offer']['promo_data']): ?>
      <?php echo $this->_tpl_vars['lng']['lbl_sp_purchase_following_products']; ?>
:<br />
      <ul>
      <?php $_from = $this->_tpl_vars['offer']['promo_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['prod_sets'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['prod_sets']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['product_sets']):
        $this->_foreach['prod_sets']['iteration']++;
?>
        <?php $_from = $this->_tpl_vars['product_sets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['param']):
?>
          <?php if ($this->_tpl_vars['param']['param_type'] == 'P'): ?>
            <li><a href="product.php?productid=<?php echo $this->_tpl_vars['param']['param_id']; ?>
"><?php echo $this->_tpl_vars['param']['product']; ?>
</a> <font class="small-note">[<?php echo $this->_tpl_vars['param']['param_qnty']; ?>
 <?php echo $this->_tpl_vars['lng']['lbl_sp_items']; ?>
]</font></li>
          <?php elseif ($this->_tpl_vars['param']['param_type'] == 'C'): ?>
            <?php $this->assign('link', "<a href=\"home.php?cat=".($this->_tpl_vars['param']['param_id'])."\">".($this->_tpl_vars['param']['category'])."</a>"); ?>
            <li><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_products_from_cat_s'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'cat', $this->_tpl_vars['link']) : smarty_modifier_substitute($_tmp, 'cat', $this->_tpl_vars['link'])); ?>
 <font class="small-note">[<?php echo $this->_tpl_vars['param']['param_qnty']; ?>
 <?php echo $this->_tpl_vars['lng']['lbl_sp_items']; ?>
]</font></li>
          <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
        <?php if (! ($this->_foreach['prod_sets']['iteration'] == $this->_foreach['prod_sets']['total'])): ?>
          - <?php echo $this->_tpl_vars['lng']['lbl_or']; ?>
 - 
        <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
      </ul>
    <?php endif; ?>

  </div>

  <?php if (! ($this->_foreach['offers']['iteration'] == $this->_foreach['offers']['total'])): ?>
  <div><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="1" height="30" alt="" /></div>
  <?php endif; ?>

  <?php endif; ?>

  <?php endforeach; endif; unset($_from); ?>

  <?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_also_review_offers'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)),'content' => $this->_smarty_vars['capture']['dialog'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>