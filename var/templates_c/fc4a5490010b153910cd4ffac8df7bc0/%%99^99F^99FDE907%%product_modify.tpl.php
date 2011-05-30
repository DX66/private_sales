<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:40
         compiled from main/product_modify.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'main/product_modify.tpl', 11, false),array('modifier', 'escape', 'main/product_modify.tpl', 41, false),array('modifier', 'amp', 'main/product_modify.tpl', 45, false),array('function', 'cycle', 'main/product_modify.tpl', 39, false),)), $this); ?>
<?php func_load_lang($this, "main/product_modify.tpl","lbl_sku,lbl_product,lbl_product_list,txt_edit_product_group,txt_add_product_options_note,lbl_product_options_help,txt_cant_create_product_warning,lbl_register_provider,lbl_warning"); ?><a name="main"></a>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "page_title.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['page_title'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['product']): ?>
<span class='product-title'>
  <?php echo ((is_array($_tmp=$this->_tpl_vars['product']['product'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 30, "...", false) : smarty_modifier_truncate($_tmp, 30, "...", false)); ?>

</span>
<br />
<?php endif; ?>

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
window.name="prodmodwin";
//]]>
</script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/popup_image_selection.js"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/multirow.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['products'] && $this->_tpl_vars['geid']): ?>
<br />
<?php ob_start(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/navigation.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td><?php echo $this->_tpl_vars['lng']['lbl_sku']; ?>
</td>
  <td><?php echo $this->_tpl_vars['lng']['lbl_product']; ?>
</td>
</tr>

<?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>

<tr<?php echo smarty_function_cycle(array('name' => 'ge','values' => ', class="TableSubHead"'), $this);?>
>
  <td><?php if ($this->_tpl_vars['productid'] == $this->_tpl_vars['v']['productid']): ?><b><?php else: ?><a href="product_modify.php?productid=<?php echo $this->_tpl_vars['v']['productid']; ?>
<?php if ($this->_tpl_vars['section'] != 'main'): ?>&amp;section=<?php echo $this->_tpl_vars['section']; ?>
<?php endif; ?>&amp;geid=<?php echo $this->_tpl_vars['geid']; ?>
"><?php endif; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['productcode'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

<?php if ($this->_tpl_vars['productid'] == $this->_tpl_vars['v']['productid']): ?></b><?php else: ?></a><?php endif; ?>
</td>
  <td><?php if ($this->_tpl_vars['productid'] == $this->_tpl_vars['v']['productid']): ?><b><?php else: ?><a href="product_modify.php?productid=<?php echo $this->_tpl_vars['v']['productid']; ?>
<?php if ($this->_tpl_vars['section'] != 'main'): ?>&amp;section=<?php echo $this->_tpl_vars['section']; ?>
<?php endif; ?>&amp;geid=<?php echo $this->_tpl_vars['geid']; ?>
"><?php endif; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['product'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>

<?php if ($this->_tpl_vars['productid'] == $this->_tpl_vars['v']['productid']): ?></b><?php else: ?></a><?php endif; ?>
</td>
</tr>

<?php endforeach; endif; unset($_from); ?>

</table>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/navigation.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['dialog'],'title' => $this->_tpl_vars['lng']['lbl_product_list'],'extra' => "width='100%'")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="product-details-geid-info">
<?php echo $this->_tpl_vars['lng']['txt_edit_product_group']; ?>

</div>
<div class="product-details-geid">
<?php endif; ?>

<br />

<?php if ($this->_tpl_vars['section'] == 'main'): ?>
<a name="section_main"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/product_details.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['section'] == 'lng'): ?>
<a name="section_lng"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/products_lng.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Subscriptions'] && $this->_tpl_vars['section'] == 'subscr' && ! $this->_tpl_vars['is_pconf']): ?>
<a name="section_subscr"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Subscriptions/subscription_plans.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Product_Options'] && $this->_tpl_vars['section'] == 'options'): ?>
<a name="section_options"></a>
<?php echo $this->_tpl_vars['lng']['txt_add_product_options_note']; ?>
<br />
<br />
<div align="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_product_options_help'],'href' => "javascript:window.open('popup_info.php?action=OPT','OPT_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
<br />
<?php if ($this->_tpl_vars['submode'] == 'product_options_add' || $this->_tpl_vars['product_options'] == '' || $this->_tpl_vars['product_option'] != ''): ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Options/add_product_options.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Options/product_options.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Product_Options'] && $this->_tpl_vars['product']['is_variants'] == 'Y' && $this->_tpl_vars['section'] == 'variants' && ! $this->_tpl_vars['is_pconf']): ?>
<a name="section_variants"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Options/product_variants.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Product_Configurator'] && $this->_tpl_vars['section'] == 'pclass' && ! $this->_tpl_vars['is_pconf']): ?>
<a name="section_pclass"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Configurator/pconf_classification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Wholesale_Trading'] && $this->_tpl_vars['product']['is_variants'] != 'Y' && $this->_tpl_vars['section'] == 'wholesale' && ! $this->_tpl_vars['is_pconf']): ?>
<a name="section_wholesale"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Wholesale_Trading/product_wholesale.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Upselling_Products'] && $this->_tpl_vars['section'] == 'upselling'): ?>
<a name="section_upselling"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Upselling_Products/product_links.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Detailed_Product_Images'] && $this->_tpl_vars['section'] == 'images'): ?>
<a name="section_images"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Detailed_Product_Images/product_images_modify.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Magnifier'] && $this->_tpl_vars['section'] == 'zoomer'): ?>
<a name="section_zoomer"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Magnifier/product_magnifier_modify.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Customer_Reviews'] && $this->_tpl_vars['section'] == 'reviews'): ?>
<a name="section_reviews"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Customer_Reviews/admin_reviews.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Feature_Comparison'] && $this->_tpl_vars['section'] == 'feature_class' && ! $this->_tpl_vars['is_pconf']): ?>
<a name="section_feature_class"></a>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Feature_Comparison/product_class.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['product'] && $this->_tpl_vars['geid']): ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['section'] == 'error'): ?>
<?php ob_start(); ?>
<br />
<?php echo $this->_tpl_vars['lng']['txt_cant_create_product_warning']; ?>

<br /><br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_register_provider'],'href' => "user_add.php?usertype=P")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['dialog'],'title' => $this->_tpl_vars['lng']['lbl_warning'],'extra' => "width='100%'")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>