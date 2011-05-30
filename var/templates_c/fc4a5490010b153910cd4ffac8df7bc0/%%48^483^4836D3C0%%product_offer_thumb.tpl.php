<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from modules/Special_Offers/customer/product_offer_thumb.tpl */ ?>
<?php if ($this->_tpl_vars['product']['have_offers'] && $this->_tpl_vars['config']['Special_Offers']['offers_show_thumb_on_lists'] == 'Y'): ?>
  <a href="offers.php?mode=product&amp;productid=<?php echo $this->_tpl_vars['product']['productid']; ?>
" class="offers-thumbnail"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" /></a>
<?php endif; ?>