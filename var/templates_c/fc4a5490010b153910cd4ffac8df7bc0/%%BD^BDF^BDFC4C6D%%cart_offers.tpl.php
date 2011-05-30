<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/cart_offers.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'modules/Special_Offers/customer/cart_offers.tpl', 16, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/cart_offers.tpl","lbl_sp_offers_applied_to_cart"); ?><?php if ($this->_tpl_vars['products'] && $this->_tpl_vars['cart']['have_offers'] && $this->_tpl_vars['cart']['applied_offers']): ?>

  <?php ob_start(); ?>

  <?php $_from = $this->_tpl_vars['cart']['applied_offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['offers'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['offers']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['offer']):
        $this->_foreach['offers']['iteration']++;
?>

  <?php if ($this->_tpl_vars['offer']['promo_checkout'] != ""): ?>
    <div>
    <?php if ($this->_tpl_vars['offer']['html_checkout']): ?>
      <?php echo $this->_tpl_vars['offer']['promo_checkout']; ?>

    <?php else: ?>
      <tt><?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['promo_checkout'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</tt>
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
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_sp_offers_applied_to_cart'],'content' => $this->_smarty_vars['capture']['dialog'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>