<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/main/search_result.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/search_result.tpl', 44, false),array('modifier', 'truncate', 'customer/main/search_result.tpl', 136, false),array('modifier', 'amp', 'customer/main/search_result.tpl', 136, false),array('modifier', 'substitute', 'customer/main/search_result.tpl', 226, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/search_result.tpl","lbl_advanced_search,lbl_search_products,lbl_search_for_pattern,lbl_search,lbl_all_word,lbl_any_word,lbl_exact_phrase,lbl_search_in,lbl_product_title,lbl_description,lbl_sku,lbl_search_also_in,lbl_advanced_search_options,lbl_search_in_category,lbl_search_in_subcategories,lbl_manufacturers,lbl_price,lbl_weight,lbl_reset_filter,lbl_search,lbl_search_products,lbl_search_results,txt_N_results_found,txt_displaying_X_Y_results,lbl_search_again,txt_N_results_found,lbl_this_page_url,lbl_products"); ?><?php if ($this->_tpl_vars['mode'] != 'search' || $this->_tpl_vars['products'] == ""): ?>

  <h1><?php echo $this->_tpl_vars['lng']['lbl_advanced_search']; ?>
</h1>

  <script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/reset.js"></script>

<script type="text/javascript">
//<![CDATA[
var searchform_def = [
  ['posted_data[substring]', ''],
  ['posted_data[including]', 'all'],
  ['posted_data[search_in_subcategories]', true],
  ['posted_data[by_title]', true],
  ['posted_data[by_descr]', true],
  ['posted_data[by_keywords]', true],
  ['posted_data[by_sku]', true],
  ['posted_data[price_min]', '<?php echo $this->_tpl_vars['search_prefilled_default']['price_min']; ?>
'],
  ['posted_data[price_max]', '<?php echo $this->_tpl_vars['search_prefilled_default']['price_max']; ?>
'],
  ['posted_data[avail_min]', '0'],
  ['posted_data[weight_min]', '<?php echo $this->_tpl_vars['search_prefilled_default']['weight_min']; ?>
'],
  ['posted_data[weight_max]', '<?php echo $this->_tpl_vars['search_prefilled_default']['weight_max']; ?>
'],
<?php if ($this->_tpl_vars['active_modules']['Extra_Fields'] && $this->_tpl_vars['extra_fields'] != ''): ?>
<?php $_from = $this->_tpl_vars['extra_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
  ['posted_data[extra_fields][<?php echo $this->_tpl_vars['v']['fieldid']; ?>
]', false],
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Manufacturers'] && $this->_tpl_vars['manufacturers'] != '' && $this->_tpl_vars['config']['Search_products']['search_products_manufacturers'] == 'Y'): ?>
  ['posted_data[manufacturers][]', '<?php echo $this->_tpl_vars['search_prefilled_default']['manufacturerids']; ?>
'],
<?php endif; ?>
  ['posted_data[categoryid]', '<?php echo $this->_tpl_vars['search_prefilled_default']['categoryid']; ?>
']
];
//]]>
</script>

  <?php ob_start(); ?>

    <form name="searchform" action="search.php" method="post">
      <input type="hidden" name="mode" value="search" />

      <table cellspacing="0" cellpadding="0" class="width-100" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_search_products'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
        <tbody>
          <tr>
            <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_search_for_pattern']; ?>
:</td>
            <td class="data-input pattern"><input type="text" name="posted_data[substring]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_prefilled']['substring'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></td>
            <td class="search-button">
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_search'],'type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </td>
          </tr>

            <tr>
              <td>&nbsp;</td>
              <td colspan="2" class="input-row">

                <label>
                  <input type="radio" name="posted_data[including]" value="all"<?php if ($this->_tpl_vars['is_empty_search_prefilled'] || $this->_tpl_vars['search_prefilled']['including'] == '' || $this->_tpl_vars['search_prefilled']['including'] == 'all'): ?> checked="checked"<?php endif; ?> />
                  <?php echo $this->_tpl_vars['lng']['lbl_all_word']; ?>

                </label>

                <label>
                  <input type="radio" name="posted_data[including]" value="any"<?php if ($this->_tpl_vars['search_prefilled']['including'] == 'any'): ?> checked="checked"<?php endif; ?> />
                  <?php echo $this->_tpl_vars['lng']['lbl_any_word']; ?>

                </label>

                <label>
                  <input type="radio" name="posted_data[including]" value="phrase"<?php if ($this->_tpl_vars['search_prefilled']['including'] == 'phrase'): ?> checked="checked"<?php endif; ?> />
                  <?php echo $this->_tpl_vars['lng']['lbl_exact_phrase']; ?>

                </label>

              </td>
            </tr>

          <tr>
            <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_search_in']; ?>
:</td>
            <td class="input-row" colspan="2">

              <label>
                <input type="checkbox" name="posted_data[by_title]"<?php if ($this->_tpl_vars['is_empty_search_prefilled'] || $this->_tpl_vars['search_prefilled']['by_title']): ?> checked="checked"<?php endif; ?> />
                <?php echo $this->_tpl_vars['lng']['lbl_product_title']; ?>

              </label>

              <label>
                <input type="checkbox" id="posted_data_by_descr" name="posted_data[by_descr]"<?php if ($this->_tpl_vars['is_empty_search_prefilled'] || $this->_tpl_vars['search_prefilled']['by_descr']): ?> checked="checked"<?php endif; ?> />
                <?php echo $this->_tpl_vars['lng']['lbl_description']; ?>

              </label>

              <label>
                <input type="checkbox" id="posted_data_by_sku" name="posted_data[by_sku]"<?php if ($this->_tpl_vars['is_empty_search_prefilled'] || $this->_tpl_vars['search_prefilled']['by_sku']): ?> checked="checked"<?php endif; ?> />
                <?php echo $this->_tpl_vars['lng']['lbl_sku']; ?>

              </label>

            </td>
          </tr>

          <?php if ($this->_tpl_vars['active_modules']['Extra_Fields'] && $this->_tpl_vars['extra_fields'] != ''): ?>

            <tr>
              <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_search_also_in']; ?>
:</td>
              <td class="search-extra-fields input-row" colspan="2">

                <?php $_from = $this->_tpl_vars['extra_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
                  <label>
                    <input type="checkbox" name="posted_data[extra_fields][<?php echo $this->_tpl_vars['v']['fieldid']; ?>
]"<?php if ($this->_tpl_vars['v']['selected'] == 'Y'): ?> checked="checked"<?php endif; ?> />
                    <?php echo $this->_tpl_vars['v']['field']; ?>

                  </label>
                <?php endforeach; endif; unset($_from); ?>
              </td>
            </tr>

          <?php endif; ?>
        </tbody>

        <?php if ($this->_tpl_vars['config']['Search_products']['search_products_category'] == 'Y' || ( $this->_tpl_vars['active_modules']['Manufacturers'] && $this->_tpl_vars['config']['Search_products']['search_products_manufacturers'] == 'Y' ) || $this->_tpl_vars['config']['Search_products']['search_products_price'] == 'Y' || $this->_tpl_vars['config']['Search_products']['search_products_weight'] == 'Y'): ?>

          <tbody>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2">
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/visiblebox_link.tpl", 'smarty_include_vars' => array('id' => 'adv_search_box','title' => $this->_tpl_vars['lng']['lbl_advanced_search_options'],'visible' => $this->_tpl_vars['search_prefilled']['need_advanced_options'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              </td>
            </tr>
          </tbody>

          <tbody id="adv_search_box"<?php if (! $this->_tpl_vars['search_prefilled']['need_advanced_options']): ?> style="display: none;"<?php endif; ?>>

            <?php if ($this->_tpl_vars['config']['Search_products']['search_products_category'] == 'Y'): ?>
              <tr>
                <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_search_in_category']; ?>
:</td>
                <td class="data-input" colspan="2">
                  <select name="posted_data[categoryid]" class="adv-search-select">
                    <option value="">&nbsp;</option>
                    <?php $_from = $this->_tpl_vars['search_categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                      <option value="<?php echo $this->_tpl_vars['k']; ?>
"<?php if ($this->_tpl_vars['search_prefilled']['categoryid'] == $this->_tpl_vars['v']['categoryid']): ?> selected="selected"<?php endif; ?>><?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE'): ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 60, '...', true, true) : smarty_modifier_truncate($_tmp, 60, '...', true, true)))) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['v'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php endif; ?></option>
                    <?php endforeach; endif; unset($_from); ?>
                  </select>
                </td>
              </tr>

              <tr>
                <td>&nbsp;</td>
                <td colspan="2">

                  <label>
                    <input type="checkbox" name="posted_data[search_in_subcategories]"<?php if ($this->_tpl_vars['is_empty_search_prefilled'] || $this->_tpl_vars['search_prefilled']['search_in_subcategories']): ?> checked="checked"<?php endif; ?> />
                    <?php echo $this->_tpl_vars['lng']['lbl_search_in_subcategories']; ?>

                  </label>
                </td>
              </tr>

            <?php endif; ?>

            <?php if ($this->_tpl_vars['active_modules']['Manufacturers'] && $this->_tpl_vars['manufacturers'] != '' && $this->_tpl_vars['config']['Search_products']['search_products_manufacturers'] == 'Y'): ?>

              <?php ob_start(); ?> 
                <?php unset($this->_sections['mnf']);
$this->_sections['mnf']['name'] = 'mnf';
$this->_sections['mnf']['loop'] = is_array($_loop=$this->_tpl_vars['manufacturers']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['mnf']['show'] = true;
$this->_sections['mnf']['max'] = $this->_sections['mnf']['loop'];
$this->_sections['mnf']['step'] = 1;
$this->_sections['mnf']['start'] = $this->_sections['mnf']['step'] > 0 ? 0 : $this->_sections['mnf']['loop']-1;
if ($this->_sections['mnf']['show']) {
    $this->_sections['mnf']['total'] = $this->_sections['mnf']['loop'];
    if ($this->_sections['mnf']['total'] == 0)
        $this->_sections['mnf']['show'] = false;
} else
    $this->_sections['mnf']['total'] = 0;
if ($this->_sections['mnf']['show']):

            for ($this->_sections['mnf']['index'] = $this->_sections['mnf']['start'], $this->_sections['mnf']['iteration'] = 1;
                 $this->_sections['mnf']['iteration'] <= $this->_sections['mnf']['total'];
                 $this->_sections['mnf']['index'] += $this->_sections['mnf']['step'], $this->_sections['mnf']['iteration']++):
$this->_sections['mnf']['rownum'] = $this->_sections['mnf']['iteration'];
$this->_sections['mnf']['index_prev'] = $this->_sections['mnf']['index'] - $this->_sections['mnf']['step'];
$this->_sections['mnf']['index_next'] = $this->_sections['mnf']['index'] + $this->_sections['mnf']['step'];
$this->_sections['mnf']['first']      = ($this->_sections['mnf']['iteration'] == 1);
$this->_sections['mnf']['last']       = ($this->_sections['mnf']['iteration'] == $this->_sections['mnf']['total']);
?>
                  <option value="<?php echo $this->_tpl_vars['manufacturers'][$this->_sections['mnf']['index']]['manufacturerid']; ?>
"<?php if ($this->_tpl_vars['manufacturers'][$this->_sections['mnf']['index']]['selected'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['manufacturers'][$this->_sections['mnf']['index']]['manufacturer']; ?>
</option>
                <?php endfor; endif; ?>
              <?php $this->_smarty_vars['capture']['manufacturers_items'] = ob_get_contents(); ob_end_clean(); ?>

              <tr>
                <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_manufacturers']; ?>
:</td>
                <td class="data-input" colspan="2">
                  <select name="posted_data[manufacturers][]" multiple="multiple" size="<?php if ($this->_sections['mnf']['total'] > 5): ?>5<?php else: ?><?php echo $this->_sections['mnf']['total']; ?>
<?php endif; ?>">
                    <?php echo $this->_smarty_vars['capture']['manufacturers_items']; ?>

                  </select>
                </td>
              </tr>

            <?php endif; ?>

            <?php if ($this->_tpl_vars['config']['Search_products']['search_products_price'] == 'Y'): ?>
              <tr>
                <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_price']; ?>
 (<?php echo $this->_tpl_vars['config']['General']['currency_symbol']; ?>
):</td>
                <td colspan="2">
                  <input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_prefilled']['price_min'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
                  &nbsp;-&nbsp;
                  <input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_prefilled']['price_max'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
                </td>
              </tr>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['config']['Search_products']['search_products_weight'] == 'Y'): ?>
              <tr>
                <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_weight']; ?>
 (<?php echo $this->_tpl_vars['config']['General']['weight_symbol']; ?>
):</td>
                <td colspan="2">
                  <input type="text" size="10" maxlength="10" name="posted_data[weight_min]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_prefilled']['weight_min'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
                  &nbsp;-&nbsp;
                  <input type="text" size="10" maxlength="10" name="posted_data[weight_max]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_prefilled']['weight_max'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
                </td>
              </tr>
            <?php endif; ?>

            <tr>
              <td class="button-row">
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_reset_filter'],'style' => 'link','href' => "javascript: reset_form('searchform', searchform_def);")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              </td>
              <td class="button-row" colspan="2">
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_search'],'type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              </td>
            </tr>
          </tbody>

        <?php endif; ?>
      </table>

    </form>

  <?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_search_products'],'content' => $this->_smarty_vars['capture']['dialog'],'additional_class' => "adv-search",'noborder' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>

<a name="results"></a>

<?php if ($this->_tpl_vars['mode'] == 'search'): ?>

  <?php if ($this->_tpl_vars['products'] != ""): ?>
    <h1><?php echo $this->_tpl_vars['lng']['lbl_search_results']; ?>
</h1>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['total_items'] > '1'): ?>
    <div class="results-found">
    <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_N_results_found'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'items', $this->_tpl_vars['total_items']) : smarty_modifier_substitute($_tmp, 'items', $this->_tpl_vars['total_items'])); ?>
. <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_displaying_X_Y_results'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'first_item', $this->_tpl_vars['first_item'], 'last_item', $this->_tpl_vars['last_item']) : smarty_modifier_substitute($_tmp, 'first_item', $this->_tpl_vars['first_item'], 'last_item', $this->_tpl_vars['last_item'])); ?>

    </div>
    <div class="search-again">
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_search_again'],'href' => "search.php",'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

  <?php elseif ($this->_tpl_vars['total_items'] == '0'): ?>
    <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_N_results_found'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'items', 0) : smarty_modifier_substitute($_tmp, 'items', 0)); ?>

    <br />
  <?php endif; ?>

  <br />

<?php endif; ?>

<?php if ($this->_tpl_vars['mode'] == 'search' && $this->_tpl_vars['products'] != ""): ?>

  <?php ob_start(); ?>

    <?php if ($this->_tpl_vars['total_pages'] > 2): ?>
      <?php $this->assign('navpage', $this->_tpl_vars['navigation_page']); ?>
    <?php endif; ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/navigation.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/products.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/navigation.tpl", 'smarty_include_vars' => array('per_page' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <?php if ($this->_tpl_vars['search_url'] != ""): ?>
      <div class="right-box"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_url'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" class="small-link"><?php echo $this->_tpl_vars['lng']['lbl_this_page_url']; ?>
</a></div>
    <?php endif; ?>

  <?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_products'],'content' => $this->_smarty_vars['capture']['dialog'],'sort' => true,'additional_class' => "products-dialog")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>