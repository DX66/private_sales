<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/product_details_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip_tags', 'main/product_details_js.tpl', 8, false),array('modifier', 'wm_remove', 'main/product_details_js.tpl', 8, false),array('modifier', 'escape', 'main/product_details_js.tpl', 8, false),)), $this); ?>
<?php func_load_lang($this, "main/product_details_js.tpl","lbl_sku,lbl_product_name,lbl_clean_url,lbl_description"); ?><script type="text/javascript">
//<![CDATA[
var requiredFields = [
  ['productcode', "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sku'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", false],
  ['product', "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_product_name'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", false],
  <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?> ['clean_url', "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_clean_url'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", false], <?php endif; ?>
  ['descr', "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_description'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", false]
];

<?php echo '
if ($.browser.mozilla) {
  $.event.add(
    window,
    "load",
    function() {
      var fld = $(\'select[name="membershipids[]"]\', document.modifyform).parents(\'tr\').get(0);
      if (fld) {
        fld.style.display = \'none\';
        setTimeout(
          function() {
            fld.style.display = \'\';
          },
          200
        );
      }
    }
  );
}

function ChangeTaxesBoxStatus(s) {
  if (s.form.elements.namedItem(\'taxes[]\'))
    s.form.elements.namedItem(\'taxes[]\').disabled = s.options[s.selectedIndex].value == \'Y\';
}

function switchPDims(c) {
  var names = [\'length\', \'width\', \'height\', \'separate_box\', \'items_per_box\'];
  for (var i = 0; i < names.length; i++) {
    var e = c.form.elements.namedItem(names[i]);
    if (e) {
      e.disabled = !c.checked;

      // "Ship in a separate box" depends on "Use the dimensions of this product for shipping cost calculation" bt:84873
      if (names[i] == \'separate_box\' && !c.checked)
        e.checked = false;
    }  
  }

  switchSSBox(c.form.elements.namedItem(\'separate_box\'));
}

function switchSSBox(c) {
  if (!c)
    return;

  c.form.elements.namedItem(\'items_per_box\').disabled = !c.checked || !c.form.elements.namedItem(\'small_item\').checked;
}
'; ?>

//]]>
</script>