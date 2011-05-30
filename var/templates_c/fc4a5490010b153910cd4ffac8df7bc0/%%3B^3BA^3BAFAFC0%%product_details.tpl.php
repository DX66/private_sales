<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/product_details.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/product_details.tpl', 51, false),array('modifier', 'escape', 'main/product_details.tpl', 114, false),array('modifier', 'substitute', 'main/product_details.tpl', 193, false),array('modifier', 'formatprice', 'main/product_details.tpl', 195, false),array('modifier', 'strip_tags', 'main/product_details.tpl', 404, false),)), $this); ?>
<?php func_load_lang($this, "main/product_details.tpl","lbl_product_owner,lbl_provider,lbl_classification,lbl_main_category,lbl_additional_categories,lbl_manufacturer,lbl_no_manufacturer,lbl_availability,lbl_avail_for_sale,lbl_hidden,lbl_disabled,lbl_bundled,lbl_product_url,lbl_details,lbl_sku,lbl_product_name,lbl_keywords,lbl_short_description,lbl_det_description,txt_html_tags_in_description,lbl_title_tag,lbl_meta_keywords,lbl_meta_description,lbl_price,lbl_pconf_base_price,lbl_note,txt_pvariant_edit_note,lbl_list_price,lbl_quantity_in_stock,lbl_note,txt_pvariant_edit_note,lbl_lowlimit_in_stock,lbl_min_order_amount,lbl_return_time,lbl_weight,lbl_note,txt_pvariant_edit_note,lbl_free_shipping,lbl_no,lbl_yes,lbl_shipping_freight,lbl_small_item,lbl_shipping_box_dimensions,lbl_length,lbl_width,lbl_height,lbl_check_for_unavailable_shipping_methods,lbl_ship_in_separate_box,lbl_items_per_box,lbl_membership,lbl_tax_exempt,lbl_no,lbl_yes,lbl_apply_taxes,lbl_hold_ctrl_key,lbl_click_here_to_manage_taxes,lbl_apply_global_discounts,lbl_gcheckout_product_valid,lbl_apply_changes,lbl_preview,lbl_clone,lbl_delete,lbl_generate_html_links"); ?><?php ob_start(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_clean_url.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/product_details_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_required_fields_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<form action="product_modify.php" method="post" name="modifyform" onsubmit="javascript: return checkRequired(requiredFields)<?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?> &amp;&amp;checkCleanUrl(document.modifyform.clean_url)<?php endif; ?>">
<input type="hidden" name="productid" value="<?php echo $this->_tpl_vars['product']['productid']; ?>
" />
<input type="hidden" name="section" value="main" />
<input type="hidden" name="mode" value="<?php if ($this->_tpl_vars['is_pconf']): ?>pconf<?php else: ?>product_modify<?php endif; ?>" />
<input type="hidden" name="geid" value="<?php echo $this->_tpl_vars['geid']; ?>
" />

<table cellpadding="4" cellspacing="0" width="100%" class="product-details-table">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/image_area.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><br /><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_product_owner'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td class="FormButton" width="10%" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_provider']; ?>
:</td>
  <td class="ProductDetails" width="90%">
<?php if ($this->_tpl_vars['usertype'] == 'A' && $this->_tpl_vars['new_product'] == 1): ?>
  <select name="provider" class="InputWidth">
<?php unset($this->_sections['prov']);
$this->_sections['prov']['name'] = 'prov';
$this->_sections['prov']['loop'] = is_array($_loop=$this->_tpl_vars['providers']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['prov']['show'] = true;
$this->_sections['prov']['max'] = $this->_sections['prov']['loop'];
$this->_sections['prov']['step'] = 1;
$this->_sections['prov']['start'] = $this->_sections['prov']['step'] > 0 ? 0 : $this->_sections['prov']['loop']-1;
if ($this->_sections['prov']['show']) {
    $this->_sections['prov']['total'] = $this->_sections['prov']['loop'];
    if ($this->_sections['prov']['total'] == 0)
        $this->_sections['prov']['show'] = false;
} else
    $this->_sections['prov']['total'] = 0;
if ($this->_sections['prov']['show']):

            for ($this->_sections['prov']['index'] = $this->_sections['prov']['start'], $this->_sections['prov']['iteration'] = 1;
                 $this->_sections['prov']['iteration'] <= $this->_sections['prov']['total'];
                 $this->_sections['prov']['index'] += $this->_sections['prov']['step'], $this->_sections['prov']['iteration']++):
$this->_sections['prov']['rownum'] = $this->_sections['prov']['iteration'];
$this->_sections['prov']['index_prev'] = $this->_sections['prov']['index'] - $this->_sections['prov']['step'];
$this->_sections['prov']['index_next'] = $this->_sections['prov']['index'] + $this->_sections['prov']['step'];
$this->_sections['prov']['first']      = ($this->_sections['prov']['iteration'] == 1);
$this->_sections['prov']['last']       = ($this->_sections['prov']['iteration'] == $this->_sections['prov']['total']);
?>
    <option value="<?php echo $this->_tpl_vars['providers'][$this->_sections['prov']['index']]['id']; ?>
"<?php if ($this->_tpl_vars['product']['provider'] == $this->_tpl_vars['providers'][$this->_sections['prov']['index']]['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['providers'][$this->_sections['prov']['index']]['login']; ?>
 (<?php echo $this->_tpl_vars['providers'][$this->_sections['prov']['index']]['title']; ?>
 <?php echo $this->_tpl_vars['providers'][$this->_sections['prov']['index']]['lastname']; ?>
 <?php echo $this->_tpl_vars['providers'][$this->_sections['prov']['index']]['firstname']; ?>
)</option>
<?php endfor; endif; ?>
  </select>
  <?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && $this->_tpl_vars['product']['provider'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
<?php else: ?>
<?php echo $this->_tpl_vars['provider_info']['title']; ?>
 <?php echo $this->_tpl_vars['provider_info']['lastname']; ?>
 <?php echo $this->_tpl_vars['provider_info']['firstname']; ?>
 (<?php echo $this->_tpl_vars['provider_info']['login']; ?>
)
<?php endif; ?>
  </td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><br /><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_classification'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryid]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_main_category']; ?>
:</td>
  <td class="ProductDetails"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/category_selector.tpl", 'smarty_include_vars' => array('field' => 'categoryid','extra' => ' class="InputWidth"','categoryid' => ((is_array($_tmp=@$this->_tpl_vars['product']['categoryid'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['default_categoryid']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['default_categoryid'])))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && $this->_tpl_vars['product']['categoryid'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryids]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_additional_categories']; ?>
:</td>
  <td class="ProductDetails">
  <select name="categoryids[]" class="InputWidth" multiple="multiple" size="8">
<?php $_from = $this->_tpl_vars['allcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['catid'] => $this->_tpl_vars['c']):
?>
    <option value="<?php echo $this->_tpl_vars['catid']; ?>
"<?php if ($this->_tpl_vars['product']['add_categoryids'][$this->_tpl_vars['catid']]): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['c']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
  </select>
  </td>
</tr>

<?php if ($this->_tpl_vars['active_modules']['Manufacturers'] != "" && ! $this->_tpl_vars['is_pconf']): ?>
<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[manufacturer]" /></td><?php endif; ?>
    <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_manufacturer']; ?>
:</td>
    <td class="ProductDetails">
  <select name="manufacturerid">
      <option value=''<?php if ($this->_tpl_vars['product']['manufacturerid'] == ''): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_no_manufacturer']; ?>
</option>
    <?php $_from = $this->_tpl_vars['manufacturers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
      <option value='<?php echo $this->_tpl_vars['v']['manufacturerid']; ?>
'<?php if ($this->_tpl_vars['v']['manufacturerid'] == $this->_tpl_vars['product']['manufacturerid']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['manufacturer']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
    </select>
  </td>
</tr>
<?php endif; ?>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[forsale]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_availability']; ?>
:</td>
  <td class="ProductDetails">
  <select name="forsale">
    <option value="Y"<?php if ($this->_tpl_vars['product']['forsale'] == 'Y' || $this->_tpl_vars['product']['forsale'] == ""): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_avail_for_sale']; ?>
</option>
    <option value="H"<?php if ($this->_tpl_vars['product']['forsale'] == 'H'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_hidden']; ?>
</option>
    <option value="N"<?php if ($this->_tpl_vars['product']['forsale'] != 'Y' && $this->_tpl_vars['product']['forsale'] != "" && $this->_tpl_vars['product']['forsale'] != 'H' && ( $this->_tpl_vars['product']['forsale'] != 'B' || ! $this->_tpl_vars['active_modules']['Product_Configurator'] )): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_disabled']; ?>
</option>
<?php if ($this->_tpl_vars['active_modules']['Product_Configurator'] && ! $this->_tpl_vars['is_pconf']): ?>
    <option value="B"<?php if ($this->_tpl_vars['product']['forsale'] == 'B'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_bundled']; ?>
</option>
<?php endif; ?>
  </select>
  </td>
</tr>

<?php if ($this->_tpl_vars['product']['internal_url']): ?>
<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_product_url']; ?>
:</td>
  <td class="ProductDetails"><a href="<?php echo $this->_tpl_vars['product']['internal_url']; ?>
"><?php echo $this->_tpl_vars['product']['internal_url']; ?>
</a></td>
</tr>
<?php endif; ?>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><br /><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_details'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[productcode]" disabled="disabled"/></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_sku']; ?>
:</td>
  <td class="ProductDetails"><input type="text" name="productcode" id="productcode" size="20" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['product']['productcode'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="InputWidth" /></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[product]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_product_name']; ?>
* :</td>
  <td class="ProductDetails"> 
  <input type="text" name="product" id="product" size="45" class="InputWidth" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['product']['product'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>onchange="javascript: if (this.form.clean_url.value == '') copy_clean_url(this, this.form.clean_url)"<?php endif; ?> />
  <?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && $this->_tpl_vars['product']['product'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
  </td>
</tr>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/clean_url_field.tpl", 'smarty_include_vars' => array('clean_url' => $this->_tpl_vars['product']['clean_url'],'clean_urls_history' => $this->_tpl_vars['product']['clean_urls_history'],'clean_url_fill_error' => $this->_tpl_vars['top_message']['clean_url_fill_error'],'tooltip_id' => 'clean_url_tooltip_link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[keywords]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_keywords']; ?>
:</td>
  <td class="ProductDetails"><input type="text" name="keywords" class="InputWidth" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['product']['keywords'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" /></td>
</tr>

<?php if ($this->_tpl_vars['active_modules']['Egoods'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Egoods/egoods.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[descr]" /></td><?php endif; ?>
  <td colspan="2" class="FormButton">
<div<?php if ($this->_tpl_vars['active_modules']['HTML_Editor'] && ! $this->_tpl_vars['html_editor_disabled']): ?> class="description"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_short_description']; ?>
* :</div>
<div class="description-data">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/textarea.tpl", 'smarty_include_vars' => array('name' => 'descr','cols' => 45,'rows' => 8,'data' => $this->_tpl_vars['product']['descr'],'width' => "100%",'btn_rows' => 4)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && ( $this->_tpl_vars['product']['descr'] == "" || $this->_tpl_vars['product']['xss_descr'] == 'Y' )): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</div>
  </td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[fulldescr]" /></td><?php endif; ?>
  <td colspan="2" class="FormButton">
<div<?php if ($this->_tpl_vars['active_modules']['HTML_Editor'] && ! $this->_tpl_vars['html_editor_disabled']): ?> class="description"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_det_description']; ?>
:</div>
<div class="description-data">
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/textarea.tpl", 'smarty_include_vars' => array('name' => 'fulldescr','cols' => 45,'rows' => 12,'class' => 'InputWidth','data' => $this->_tpl_vars['product']['fulldescr'],'width' => "100%",'btn_rows' => 4)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php if ($this->_tpl_vars['product']['xss_fulldescr'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</div>
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><?php echo $this->_tpl_vars['lng']['txt_html_tags_in_description']; ?>
</td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[title_tag]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_title_tag']; ?>
:</td>
  <td class="ProductDetails"><textarea name="title_tag" cols="45" rows="6" class="InputWidth"><?php echo $this->_tpl_vars['product']['title_tag']; ?>
</textarea></td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[meta_keywords]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_meta_keywords']; ?>
:</td>
  <td class="ProductDetails"><textarea name="meta_keywords" cols="45" rows="6" class="InputWidth"><?php echo $this->_tpl_vars['product']['meta_keywords']; ?>
</textarea></td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[meta_description]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_meta_description']; ?>
:</td>
  <td class="ProductDetails"><textarea name="meta_description" cols="45" rows="6" class="InputWidth"><?php echo $this->_tpl_vars['product']['meta_description']; ?>
</textarea></td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><hr /></td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><?php if ($this->_tpl_vars['product']['is_variants'] == 'Y'): ?>&nbsp;<?php else: ?><input type="checkbox" value="Y" name="fields[price]" /><?php endif; ?></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php if (! $this->_tpl_vars['is_pconf']): ?><?php echo $this->_tpl_vars['lng']['lbl_price']; ?>
<?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_pconf_base_price']; ?>
<?php endif; ?> (<?php echo $this->_tpl_vars['config']['General']['currency_symbol']; ?>
):</td>
  <td class="ProductDetails">
<?php if ($this->_tpl_vars['product']['is_variants'] == 'Y'): ?>
<b><?php echo $this->_tpl_vars['lng']['lbl_note']; ?>
:</b> <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_pvariant_edit_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'href', $this->_tpl_vars['variant_href']) : smarty_modifier_substitute($_tmp, 'href', $this->_tpl_vars['variant_href'])); ?>

<?php else: ?>
  <input type="text" name="price" size="18" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['product']['price'])) ? $this->_run_mod_handler('formatprice', true, $_tmp) : smarty_modifier_formatprice($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
" />
  <?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && $this->_tpl_vars['product']['price'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
<?php endif; ?>
  </td>
</tr>

<?php if (! $this->_tpl_vars['is_pconf']): ?>
<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[list_price]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_list_price']; ?>
 <span class="Text">(<?php echo $this->_tpl_vars['config']['General']['currency_symbol']; ?>
):</span></td>
  <td class="ProductDetails"><input type="text" name="list_price" size="18" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['product']['list_price'])) ? $this->_run_mod_handler('formatprice', true, $_tmp) : smarty_modifier_formatprice($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
" /></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><?php if ($this->_tpl_vars['product']['is_variants'] == 'Y'): ?>&nbsp;<?php else: ?><input type="checkbox" value="Y" name="fields[avail]" /><?php endif; ?></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_quantity_in_stock']; ?>
:</td>
  <td class="ProductDetails">
<?php if ($this->_tpl_vars['product']['is_variants'] == 'Y'): ?>
<b><?php echo $this->_tpl_vars['lng']['lbl_note']; ?>
:</b> <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_pvariant_edit_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'href', $this->_tpl_vars['variant_href']) : smarty_modifier_substitute($_tmp, 'href', $this->_tpl_vars['variant_href'])); ?>

<?php else: ?>
  <input type="text" name="avail" size="18" value="<?php if ($this->_tpl_vars['product']['productid'] == ""): ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['avail'])) ? $this->_run_mod_handler('default', true, $_tmp, 1000) : smarty_modifier_default($_tmp, 1000)); ?>
<?php else: ?><?php echo $this->_tpl_vars['product']['avail']; ?>
<?php endif; ?>" />
  <?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && $this->_tpl_vars['product']['avail'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
<?php endif; ?>
  </td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[low_avail_limit]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_lowlimit_in_stock']; ?>
:</td>
  <td class="ProductDetails"> 
  <input type="text" name="low_avail_limit" size="18" value="<?php if ($this->_tpl_vars['product']['productid'] == ""): ?>10<?php else: ?><?php echo $this->_tpl_vars['product']['low_avail_limit']; ?>
<?php endif; ?>" />
  <?php if ($this->_tpl_vars['top_message']['fillerror'] != "" && $this->_tpl_vars['product']['low_avail_limit'] <= 0): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
  </td>
</tr>
<?php endif; ?>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[min_amount]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_min_order_amount']; ?>
:</td>
  <td class="ProductDetails"><input type="text" name="min_amount" size="18" value="<?php if ($this->_tpl_vars['product']['productid'] == ""): ?>1<?php else: ?><?php echo $this->_tpl_vars['product']['min_amount']; ?>
<?php endif; ?>" /></td>
</tr>

<?php if ($this->_tpl_vars['active_modules']['RMA'] != '' && ! $this->_tpl_vars['is_pconf']): ?>
<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[return_time]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_return_time']; ?>
:</td>
  <td class="ProductDetails"><input type="text" name="return_time" size="18" value="<?php echo $this->_tpl_vars['product']['return_time']; ?>
" /></td>
</tr>
<?php endif; ?>

<?php if (! $this->_tpl_vars['is_pconf']): ?>
<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><hr /></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><?php if ($this->_tpl_vars['product']['is_variants'] == 'Y'): ?>&nbsp;<?php else: ?><input type="checkbox" value="Y" name="fields[weight]" /><?php endif; ?></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_weight']; ?>
 (<?php echo $this->_tpl_vars['config']['General']['weight_symbol']; ?>
):</td>
  <td class="ProductDetails"> 
<?php if ($this->_tpl_vars['product']['is_variants'] == 'Y'): ?>
<b><?php echo $this->_tpl_vars['lng']['lbl_note']; ?>
:</b> <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_pvariant_edit_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'href', $this->_tpl_vars['variant_href']) : smarty_modifier_substitute($_tmp, 'href', $this->_tpl_vars['variant_href'])); ?>

<?php else: ?>
  <input type="text" name="weight" size="18" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['product']['weight'])) ? $this->_run_mod_handler('formatprice', true, $_tmp) : smarty_modifier_formatprice($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
" />
<?php endif; ?>
  </td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_shipping]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_free_shipping']; ?>
:</td>
  <td class="ProductDetails">
  <select name="free_shipping">
    <option value='N'<?php if ($this->_tpl_vars['product']['free_shipping'] == 'N'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_no']; ?>
</option>
    <option value='Y'<?php if ($this->_tpl_vars['product']['free_shipping'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_yes']; ?>
</option>
  </select> 
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[shipping_freight]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_shipping_freight']; ?>
 (<?php echo $this->_tpl_vars['config']['General']['currency_symbol']; ?>
):</td>
  <td class="ProductDetails">
  <input type="text" name="shipping_freight" size="18" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['product']['shipping_freight'])) ? $this->_run_mod_handler('formatprice', true, $_tmp) : smarty_modifier_formatprice($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
" />
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[small_item]" /></td><?php endif; ?>
  <td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_small_item']; ?>
:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="small_item" value="Y"<?php if ($this->_tpl_vars['product']['small_item'] != 'Y'): ?> checked="checked"<?php endif; ?> onclick="javascript: switchPDims(this);" />
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[dimensions]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_shipping_box_dimensions']; ?>
 (<?php echo $this->_tpl_vars['config']['General']['dimensions_symbol']; ?>
):</td>
  <td class="ProductDetails">
  <table cellpadding="0" cellspacing="1" border="0" width="100%">
  <tr>
    <td colspan="2"><?php echo $this->_tpl_vars['lng']['lbl_length']; ?>
</td>
    <td colspan="2"><?php echo $this->_tpl_vars['lng']['lbl_width']; ?>
</td>
    <td colspan="3"><?php echo $this->_tpl_vars['lng']['lbl_height']; ?>
</td>
  </tr>
  <tr>
    <td><input type="text" name="length" size="6" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['length'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
"<?php if ($this->_tpl_vars['product']['small_item'] == 'Y'): ?> disabled="disabled"<?php endif; ?> /></td>
    <td>&nbsp;x&nbsp;</td>
    <td><input type="text" name="width" size="6" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['width'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
"<?php if ($this->_tpl_vars['product']['small_item'] == 'Y'): ?> disabled="disabled"<?php endif; ?> /></td>
    <td>&nbsp;x&nbsp;</td>
    <td><input type="text" name="height" size="6" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['height'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['zero']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['zero'])); ?>
"<?php if ($this->_tpl_vars['product']['small_item'] == 'Y'): ?> disabled="disabled"<?php endif; ?> /></td>
    <td align="center" width="100%"><?php if ($this->_tpl_vars['new_product'] == 1): ?>&nbsp;<?php else: ?><a href="javascript:void(0);" onclick="javascript: popupOpen('unavailable_shipping.php?id=<?php echo $this->_tpl_vars['product']['productid']; ?>
', '', {width:350,height:500});"><?php echo $this->_tpl_vars['lng']['lbl_check_for_unavailable_shipping_methods']; ?>
</a><?php endif; ?></td>
  </tr>
  </table>
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[separate_box]" /></td><?php endif; ?>
  <td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_ship_in_separate_box']; ?>
:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="separate_box" value="Y"<?php if ($this->_tpl_vars['product']['separate_box'] == 'Y'): ?> checked="checked"<?php endif; ?><?php if ($this->_tpl_vars['product']['small_item'] == 'Y'): ?> disabled="disabled"<?php endif; ?> onclick="javascript: switchSSBox(this);" />
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[items_per_box]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_items_per_box']; ?>
:</td>
  <td class="ProductDetails">
  <input type="text" name="items_per_box" size="18" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product']['items_per_box'])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)); ?>
"<?php if ($this->_tpl_vars['product']['small_item'] == 'Y' || $this->_tpl_vars['product']['separate_box'] != 'Y'): ?> disabled="disabled"<?php endif; ?> />
  </td>
</tr>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><hr /></td>
</tr>
<?php endif; ?> 
<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[membershipids]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_membership']; ?>
:</td>
  <td class="ProductDetails"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/membership_selector.tpl", 'smarty_include_vars' => array('data' => $this->_tpl_vars['product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_tax]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_tax_exempt']; ?>
:</td>
  <td class="ProductDetails">
  <select name="free_tax"<?php if ($this->_tpl_vars['taxes']): ?> onchange="javascript: ChangeTaxesBoxStatus(this);"<?php endif; ?>>
    <option value='N'<?php if ($this->_tpl_vars['product']['free_tax'] == 'N'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_no']; ?>
</option>
    <option value='Y'<?php if ($this->_tpl_vars['product']['free_tax'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_yes']; ?>
</option>
  </select> 
  </td>
</tr>

<?php if ($this->_tpl_vars['taxes']): ?>
<tr> 
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[taxes]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_apply_taxes']; ?>
:</td>
  <td class="ProductDetails"> 
  <select name="taxes[]" multiple="multiple"<?php if ($this->_tpl_vars['product']['free_tax'] == 'Y'): ?> disabled="disabled"<?php endif; ?>>
  <?php unset($this->_sections['tax']);
$this->_sections['tax']['name'] = 'tax';
$this->_sections['tax']['loop'] = is_array($_loop=$this->_tpl_vars['taxes']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['tax']['show'] = true;
$this->_sections['tax']['max'] = $this->_sections['tax']['loop'];
$this->_sections['tax']['step'] = 1;
$this->_sections['tax']['start'] = $this->_sections['tax']['step'] > 0 ? 0 : $this->_sections['tax']['loop']-1;
if ($this->_sections['tax']['show']) {
    $this->_sections['tax']['total'] = $this->_sections['tax']['loop'];
    if ($this->_sections['tax']['total'] == 0)
        $this->_sections['tax']['show'] = false;
} else
    $this->_sections['tax']['total'] = 0;
if ($this->_sections['tax']['show']):

            for ($this->_sections['tax']['index'] = $this->_sections['tax']['start'], $this->_sections['tax']['iteration'] = 1;
                 $this->_sections['tax']['iteration'] <= $this->_sections['tax']['total'];
                 $this->_sections['tax']['index'] += $this->_sections['tax']['step'], $this->_sections['tax']['iteration']++):
$this->_sections['tax']['rownum'] = $this->_sections['tax']['iteration'];
$this->_sections['tax']['index_prev'] = $this->_sections['tax']['index'] - $this->_sections['tax']['step'];
$this->_sections['tax']['index_next'] = $this->_sections['tax']['index'] + $this->_sections['tax']['step'];
$this->_sections['tax']['first']      = ($this->_sections['tax']['iteration'] == 1);
$this->_sections['tax']['last']       = ($this->_sections['tax']['iteration'] == $this->_sections['tax']['total']);
?>
  <option value="<?php echo $this->_tpl_vars['taxes'][$this->_sections['tax']['index']]['taxid']; ?>
"<?php if ($this->_tpl_vars['taxes'][$this->_sections['tax']['index']]['selected'] > 0): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['taxes'][$this->_sections['tax']['index']]['tax_name']; ?>
</option>
  <?php endfor; endif; ?>
  </select>
  <br /><?php echo $this->_tpl_vars['lng']['lbl_hold_ctrl_key']; ?>

  <?php if ($this->_tpl_vars['is_admin_user']): ?><br /><a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/taxes.php" class="SmallNote" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_click_here_to_manage_taxes']; ?>
</a><?php endif; ?>
  </td>
</tr>
<?php endif; ?>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[discount_avail]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_apply_global_discounts']; ?>
:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="discount_avail" value="Y"<?php if ($this->_tpl_vars['product']['productid'] == "" || $this->_tpl_vars['product']['discount_avail'] == 'Y'): ?> checked="checked"<?php endif; ?> />
  </td>
</tr>

<?php if ($this->_tpl_vars['gcheckout_enabled']): ?>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[valid_for_gcheckout]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['lng']['lbl_gcheckout_product_valid']; ?>
:</td>
  <td class="ProductDetails">
  <input type="hidden" name="valid_for_gcheckout" value="N" />
  <input type="checkbox" name="valid_for_gcheckout" value="Y"<?php if ($this->_tpl_vars['product']['productid'] == "" || $this->_tpl_vars['product']['valid_for_gcheckout'] == 'Y'): ?> checked="checked"<?php endif; ?> />
  </td>
</tr>

<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Extra_Fields'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Extra_Fields/product_modify.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Special_Offers']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/product_modify.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2" align="center">
    <br /><br />
    <div id="sticky_content">
    <table width="100%">
      <tr>
        <td width="120" align="left" class="main-button">
          <input type="submit" class="big-main-button" value=" <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_apply_changes'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 " />
        </td>
        <td width="100%" align="right">
          <?php if ($this->_tpl_vars['product']['productid'] > 0): ?>
            <input type="button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_preview'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="javascript: submitForm(this.form, 'details');" /> &nbsp;&nbsp;&nbsp;
            <input type="button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_clone'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="javascript: submitForm(this.form, 'clone');" />&nbsp;&nbsp;&nbsp;
            <input type="button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_delete'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="javascript: submitForm(this.form, 'delete');" />&nbsp;&nbsp;&nbsp;
            <input type="button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_generate_html_links'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="javascript: submitForm(this.form, 'links');" />
          <?php endif; ?>
        </td>
      </tr>
    </table>
    </div>
  </td>
</tr>

</table>
</form>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['new_product'] != '1' && $this->_tpl_vars['geid'] == ''): ?>
  <br />
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/clean_urls.tpl", 'smarty_include_vars' => array('resource_name' => 'productid','resource_id' => $this->_tpl_vars['productid'],'clean_url_action' => "product_modify.php",'clean_urls_history_mode' => 'clean_urls_history','clean_urls_history' => $this->_tpl_vars['product']['clean_urls_history'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>