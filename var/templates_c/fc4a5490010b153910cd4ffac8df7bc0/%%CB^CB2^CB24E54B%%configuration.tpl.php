<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:19
         compiled from admin/main/configuration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'admin/main/configuration.tpl', 9, false),array('modifier', 'escape', 'admin/main/configuration.tpl', 26, false),array('modifier', 'default', 'admin/main/configuration.tpl', 175, false),array('modifier', 'formatnumeric', 'admin/main/configuration.tpl', 206, false),array('modifier', 'strip_tags', 'admin/main/configuration.tpl', 346, false),array('function', 'getvar', 'admin/main/configuration.tpl', 67, false),array('function', 'cycle', 'admin/main/configuration.tpl', 166, false),array('function', 'assign_ext', 'admin/main/configuration.tpl', 246, false),array('function', 'inc', 'admin/main/configuration.tpl', 262, false),)), $this); ?>
<?php func_load_lang($this, "admin/main/configuration.tpl","lbl_htaccess_warning,lbl_usps_labels_help,lbl_signup_for_gcheckout,txt_gcheckout_setup_note,lbl_signup_for_acheckout,txt_acheckout_setup_note,google_analytics_info,txt_clean_url_htaccess_info,lbl_select_skin,lbl_enabled,txt_speedup_description,lbl_warning,lbl_note,lbl_apply_changes,lbl_note,lbl_company_location_country_note,lbl_test_realtime_calculation,txt_test_realtime_calculation_text,lbl_package_weight,lbl_test,lbl_test_data_encryption,lbl_test_data_encryption_link"); ?><?php if ($this->_tpl_vars['htaccess_warning'][$this->_tpl_vars['option']] == 'Y'): ?>

<?php ob_start(); ?>

<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_htaccess_warning'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'htaccess', $this->_tpl_vars['htaccess_path']) : smarty_modifier_substitute($_tmp, 'htaccess', $this->_tpl_vars['htaccess_path'])); ?>


<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "location.tpl", 'smarty_include_vars' => array('location' => "",'alt_content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"','newid' => 'htaccess_warning','alt_type' => 'W')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "page_title.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['option_title'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "permanent_warning.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>

<?php $this->assign('cycle_name', 'sep'); ?>

<?php if ($this->_tpl_vars['option'] != 'User_Profiles' && $this->_tpl_vars['option'] != 'Contact_Us' && $this->_tpl_vars['option'] != 'Search_products'): ?>
  <form action="configuration.php?option=<?php echo ((is_array($_tmp=$this->_tpl_vars['option'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" method="post" name="processform" onsubmit="return validateFields()">
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'Shipping_Label_Generator'): ?>
<table cellpadding="3" cellspacing="1" width="100%">
<tr>
  <td>
    <div align="right">
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_usps_labels_help'],'href' => "javascript:window.open('popup_info.php?action=TSTLBL','TSTLBL_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>
  </td>
</tr>
</table>
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'User_Profiles'): ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/main/user_profiles.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php elseif ($this->_tpl_vars['option'] == 'Contact_Us'): ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/main/contact_us_profiles.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php elseif ($this->_tpl_vars['option'] == 'Search_products'): ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/main/search_products_form.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php else: ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/main/conf_fields_validation_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['option'] == 'Google_Checkout'): ?>
  <div align="right">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_signup_for_gcheckout'],'href' => "http://checkout.google.com/sell?promo=sequaliteamsoftware",'target' => '_blank')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><br />
  </div>
  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_gcheckout_setup_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'callback_url', $this->_tpl_vars['gcheckout_callback_url']) : smarty_modifier_substitute($_tmp, 'callback_url', $this->_tpl_vars['gcheckout_callback_url'])); ?>
<br />
  <br />
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Google_Checkout/gcheckout_requirements.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'Amazon_Checkout'): ?>
  <?php echo smarty_function_getvar(array('var' => 'amazon_merchant_URL'), $this);?>

  <div align="right">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_signup_for_acheckout'],'href' => "https://payments.amazon.com/sdui/sdui/business/cba#getting-started",'target' => '_blank')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><br />
  </div>
  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_acheckout_setup_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'callback_url', $this->_tpl_vars['amazon_merchant_URL']) : smarty_modifier_substitute($_tmp, 'callback_url', $this->_tpl_vars['amazon_merchant_URL'])); ?>
<br />
  <br />
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'Image_Verification'): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Image_Verification/spambot_requirements.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'Google_Analytics'): ?>
  <?php echo $this->_tpl_vars['lng']['google_analytics_info']; ?>
<br />
  <br />
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'SEO'): ?>
  <?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_clean_url_htaccess_info'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'clean_url_htaccess', $this->_tpl_vars['clean_url_htaccess']) : smarty_modifier_substitute($_tmp, 'clean_url_htaccess', $this->_tpl_vars['clean_url_htaccess'])))) ? $this->_run_mod_handler('substitute', true, $_tmp, 'htaccess', $this->_tpl_vars['clean_url_htaccess_path']) : smarty_modifier_substitute($_tmp, 'htaccess', $this->_tpl_vars['clean_url_htaccess_path'])))) ? $this->_run_mod_handler('substitute', true, $_tmp, 'clean_url_test_url', $this->_tpl_vars['clean_url_test_url']) : smarty_modifier_substitute($_tmp, 'clean_url_test_url', $this->_tpl_vars['clean_url_test_url'])); ?>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'XPayments_Connector'): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/XPayments_Connector/config_recommends.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" class="general-settings">

<?php $this->assign('first_row', 1); ?>

<?php if ($this->_tpl_vars['option'] == 'Appearance'): ?>
  <tr>
    <td>
<script type="text/javascript">
//<![CDATA[
var previewShots = [];
<?php $_from = $this->_tpl_vars['alt_skins_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['alt_skin']):
?>
previewShots['<?php echo $this->_tpl_vars['id']; ?>
']='<?php echo ((is_array($_tmp=$this->_tpl_vars['alt_skin']['screenshot'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
<?php endforeach; endif; unset($_from); ?>
var ssPreviewSrc = '<?php echo ((is_array($_tmp=$this->_tpl_vars['alt_skin_info']['screenshot'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
//]]>
</script>
    </td>
    <td valign="top">
      <strong><?php echo $this->_tpl_vars['lng']['lbl_select_skin']; ?>
</strong>
      <br /><br />
      <select name="alt_skin" id="alt_skin_id" onchange="javascript:$('#alt_image').attr('src', previewShots[this.value]);">
      <?php $_from = $this->_tpl_vars['alt_skins_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['alt_skin']):
?>
      <option value="<?php echo $this->_tpl_vars['id']; ?>
"<?php if ($this->_tpl_vars['alt_skin'] == $this->_tpl_vars['alt_skin_info']): ?> selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['alt_skin']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</option>
      <?php endforeach; endif; unset($_from); ?>
      </select>
<script type="text/javascript">
//<![CDATA[
$('#alt_image').attr('src', ssPreviewSrc);
//]]>
</script>
    </td>
    <td valign="top" id="alt_image_td">
      <img id="alt_image" src="<?php echo ((is_array($_tmp=$this->_tpl_vars['alt_skin_info']['screenshot'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" alt="" />
    </td>
  </tr>
<?php endif; ?>

<?php unset($this->_sections['cat_num']);
$this->_sections['cat_num']['name'] = 'cat_num';
$this->_sections['cat_num']['loop'] = is_array($_loop=$this->_tpl_vars['configuration']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cat_num']['show'] = true;
$this->_sections['cat_num']['max'] = $this->_sections['cat_num']['loop'];
$this->_sections['cat_num']['step'] = 1;
$this->_sections['cat_num']['start'] = $this->_sections['cat_num']['step'] > 0 ? 0 : $this->_sections['cat_num']['loop']-1;
if ($this->_sections['cat_num']['show']) {
    $this->_sections['cat_num']['total'] = $this->_sections['cat_num']['loop'];
    if ($this->_sections['cat_num']['total'] == 0)
        $this->_sections['cat_num']['show'] = false;
} else
    $this->_sections['cat_num']['total'] = 0;
if ($this->_sections['cat_num']['show']):

            for ($this->_sections['cat_num']['index'] = $this->_sections['cat_num']['start'], $this->_sections['cat_num']['iteration'] = 1;
                 $this->_sections['cat_num']['iteration'] <= $this->_sections['cat_num']['total'];
                 $this->_sections['cat_num']['index'] += $this->_sections['cat_num']['step'], $this->_sections['cat_num']['iteration']++):
$this->_sections['cat_num']['rownum'] = $this->_sections['cat_num']['iteration'];
$this->_sections['cat_num']['index_prev'] = $this->_sections['cat_num']['index'] - $this->_sections['cat_num']['step'];
$this->_sections['cat_num']['index_next'] = $this->_sections['cat_num']['index'] + $this->_sections['cat_num']['step'];
$this->_sections['cat_num']['first']      = ($this->_sections['cat_num']['iteration'] == 1);
$this->_sections['cat_num']['last']       = ($this->_sections['cat_num']['iteration'] == $this->_sections['cat_num']['total']);
?>

<?php $this->assign('opt_comment', "opt_".($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'])); ?>
<?php $this->assign('opt_label_id', "opt_".($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'])); ?>
<?php $this->assign('opt_descr', "opt_descr_".($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'])); ?>

<?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'separator'): ?>

  <tr>
    <td colspan="3" class="TableSeparator">
      <?php if ($this->_tpl_vars['lng'][$this->_tpl_vars['opt_comment']] != ""): ?>
        <?php echo $this->_tpl_vars['lng'][$this->_tpl_vars['opt_comment']]; ?>

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['comment']): ?>
        <?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['comment']; ?>

      <?php else: ?>
        <hr />
      <?php endif; ?>
    </td>
  </tr>
  <?php $this->assign('cycle_name', $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']); ?>

<?php else: ?>

  <?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['pre_note']): ?>
    <tr>
      <td colspan="3"><?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['pre_note']; ?>
<br /><br /></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['cols_count'] == '1'): ?>
    <?php $this->assign('bgcolor', ""); ?>
    <?php $this->assign('cols_count', ""); ?>
  <?php else: ?>
    <?php $this->assign('bgcolor', "class=''"); ?>
    <?php $this->assign('cols_count', '1'); ?>
  <?php endif; ?>

  <?php echo smarty_function_cycle(array('name' => $this->_tpl_vars['cycle_name'],'values' => " class='TableSubHead', ",'assign' => 'row_style'), $this);?>


  <tr id="tr_<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
">
    <td width="30" <?php echo $this->_tpl_vars['row_style']; ?>
>&nbsp;<a name="anchor_<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
"></a></td>
    <td <?php echo $this->_tpl_vars['row_style']; ?>
 width="60%">
      <?php echo ''; ?><?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'checkbox'): ?><?php echo '<label for="'; ?><?php echo $this->_tpl_vars['opt_label_id']; ?><?php echo '">'; ?><?php endif; ?><?php echo ''; ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['lng'][$this->_tpl_vars['opt_comment']])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['comment']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['comment'])); ?><?php echo ''; ?><?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'checkbox'): ?><?php echo '</label>'; ?><?php endif; ?><?php echo ''; ?>

    </td>
    <td <?php echo $this->_tpl_vars['row_style']; ?>
 width="40%">

    <table cellpadding="0" cellspacing="0">
    <tr>
      <td>
      
      <?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'blowfish_enabled' && $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'] == 'Y' && $this->_tpl_vars['is_merchant_password'] != 'Y'): ?>

        <?php echo $this->_tpl_vars['lng']['lbl_enabled']; ?>

        <input type="hidden" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" value='<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value']; ?>
' />

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'periodic_logs'): ?>

        <input type="hidden" name="periodic_logs" value="" />
        <select name="periodic_logs[]" multiple="multiple" size="10">
          <?php $_from = $this->_tpl_vars['periodical_logs_names']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['log_label'] => $this->_tpl_vars['txt_label']):
?>
            <option value="<?php echo $this->_tpl_vars['log_label']; ?>
"<?php if ($this->_tpl_vars['periodical_log_labels'][$this->_tpl_vars['log_label']] != ""): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['txt_label']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>

      <?php elseif (( $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'use_https_login' || $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'leave_https' || $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'use_secure_login_page' ) && ! $this->_tpl_vars['https_check_success']): ?>
        <input type="checkbox" id="<?php echo $this->_tpl_vars['opt_label_id']; ?>
" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" disabled="disabled" />

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'numeric'): ?>

        <input id="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" type="text" size="10" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'])) ? $this->_run_mod_handler('formatnumeric', true, $_tmp) : smarty_modifier_formatnumeric($_tmp)); ?>
" />

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'text' || $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'trimmed_text'): ?>

        <input type="text" size="30" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'htmlall') : smarty_modifier_escape($_tmp, 'htmlall')); ?>
" />
      
      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'password'): ?>

        <input type="password" size="30" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" id="<?php echo $this->_tpl_vars['opt_label_id']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'htmlall') : smarty_modifier_escape($_tmp, 'htmlall')); ?>
" />

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'checkbox'): ?>

        <?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['disabled']): ?>
          <input type="hidden" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        <?php endif; ?>
        <input type="checkbox" id="<?php echo $this->_tpl_vars['opt_label_id']; ?>
" name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
"<?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'] == 'Y'): ?> checked="checked"<?php endif; ?><?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['disabled']): ?> disabled="disabled"<?php endif; ?> />

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'textarea'): ?>

        <textarea name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" cols="30" rows="5"><?php echo ((is_array($_tmp=$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea>

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'selector' && $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['variants'] != ''): ?>

        <select name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
"<?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['auto_submit']): ?> onchange="javascript: document.processform.submit()"<?php endif; ?><?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'gcheckout_package_type'): ?> onchange="javascript: if ($(this).val() == 'use_dimensions') {$('#tr_gcheckout_length, #tr_gcheckout_width, #tr_gcheckout_height').show();} else {$('#tr_gcheckout_length, #tr_gcheckout_width, #tr_gcheckout_height').hide();}"<?php endif; ?>>
          <?php $_from = $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['vkey'] => $this->_tpl_vars['vitem']):
?>
            <option value="<?php echo $this->_tpl_vars['vkey']; ?>
"<?php if ($this->_tpl_vars['vitem']['selected']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['vitem']['name']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'multiselector' && $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['variants'] != ''): ?>

        <select name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
[]" multiple="multiple" size="5">
          <?php $_from = $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['vkey'] => $this->_tpl_vars['vitem']):
?>
            <option value="<?php echo $this->_tpl_vars['vkey']; ?>
"<?php if ($this->_tpl_vars['vitem']['selected']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['vitem']['name']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>

      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'state'): ?>

        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/states.tpl", 'smarty_include_vars' => array('states' => $this->_tpl_vars['states'],'name' => $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'],'default' => $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value'],'default_country' => $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['country_value'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php echo smarty_function_assign_ext(array('var' => "state_values[".($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['prefix'])."]",'value' => $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value']), $this);?>


      <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['type'] == 'country'): ?>

        <select name="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
" id="<?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']; ?>
">
          <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
            <option value="<?php echo $this->_tpl_vars['c']['country_code']; ?>
"<?php if ($this->_tpl_vars['c']['country_code'] == $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['value']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['c']['country']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>

      <?php endif; ?>

      <?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['prefix']): ?>

        <?php $this->assign('prefix', $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['prefix']); ?>
        <?php if ($this->_tpl_vars['dynamic_states'][$this->_tpl_vars['prefix']] > 0): ?>
          <?php echo smarty_function_inc(array('assign' => 'next','value' => $this->_tpl_vars['dynamic_states'][$this->_tpl_vars['prefix']]), $this);?>

          <?php echo smarty_function_assign_ext(array('var' => "dynamic_states[".($this->_tpl_vars['prefix'])."]",'value' => $this->_tpl_vars['next']), $this);?>

        <?php else: ?>
          <?php echo smarty_function_assign_ext(array('var' => "dynamic_states[".($this->_tpl_vars['prefix'])."]",'value' => 1), $this);?>

        <?php endif; ?>

      <?php endif; ?>
      </td>
      <td valign="middle">
      <?php if ($this->_tpl_vars['lng'][$this->_tpl_vars['opt_descr']]): ?>
        
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/tooltip_js.tpl", 'smarty_include_vars' => array('title' => ((is_array($_tmp=@$this->_tpl_vars['lng'][$this->_tpl_vars['opt_comment']])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['comment']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['comment'])),'text' => $this->_tpl_vars['lng'][$this->_tpl_vars['opt_descr']],'id' => "help_".($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name']),'type' => 'img','sticky' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

      <?php else: ?>
        &nbsp;
      <?php endif; ?>
      </td>
    </tr>
    </table>
    </td>
  </tr>

  <?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['name'] == 'speedup_css'): ?>
  <tr>
  <td>&nbsp;</td>
  <td colspan="2">
  <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_speedup_description'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'speed_up_htaccess', $this->_tpl_vars['speed_up_htaccess']) : smarty_modifier_substitute($_tmp, 'speed_up_htaccess', $this->_tpl_vars['speed_up_htaccess'])))) ? $this->_run_mod_handler('substitute', true, $_tmp, 'htaccess_file', $this->_tpl_vars['htaccess_file']) : smarty_modifier_substitute($_tmp, 'htaccess_file', $this->_tpl_vars['htaccess_file'])); ?>

  </td>
  </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['error']): ?>

    <tr>
      <td width="30">&nbsp;</td>
      <td colspan="2" <?php echo $this->_tpl_vars['row_style']; ?>
>
        <font class="ErrorMessage"><?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['error']; ?>
</font>
      </td>
    </tr>

  <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['warning']): ?>

    <tr>
      <td width="30">&nbsp;</td>
      <td colspan="2" <?php echo $this->_tpl_vars['row_style']; ?>
>
        <strong><?php echo $this->_tpl_vars['lng']['lbl_warning']; ?>
:</strong> <?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['warning']; ?>

      </td>
    </tr>

  <?php elseif ($this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['note']): ?>

    <tr>
      <td width="30">&nbsp;</td>
      <td colspan="2" <?php echo $this->_tpl_vars['row_style']; ?>
>
        <strong><?php echo $this->_tpl_vars['lng']['lbl_note']; ?>
:</strong> <?php echo $this->_tpl_vars['configuration'][$this->_sections['cat_num']['index']]['note']; ?>

      </td>
    </tr>

  <?php endif; ?>

<?php endif; ?>

<?php $this->assign('first_row', 0); ?>

<?php endfor; endif; ?>

<?php if ($this->_tpl_vars['dynamic_states'] != ''): ?>
  <tr style="display: none;">
    <td>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "change_states_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      <?php $_from = $this->_tpl_vars['dynamic_states']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['name'] => $this->_tpl_vars['cnt']):
?>
        <?php if ($this->_tpl_vars['cnt'] == 2): ?>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_states.tpl", 'smarty_include_vars' => array('state_name' => ($this->_tpl_vars['name'])."_state",'country_name' => ($this->_tpl_vars['name'])."_country",'state_value' => $this->_tpl_vars['state_values'][$this->_tpl_vars['name']])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
<?php endif; ?>

<tr>
  <td colspan="3">
    <br /><br />
    <div id="sticky_content">
      <div class="main-button">
        <input type="submit" class="big-main-button" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_apply_changes'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </div>
    </div>
  </td>
</tr>

</table>

<?php if ($this->_tpl_vars['option'] == 'Company' && ( ! $this->_tpl_vars['single_mode'] )): ?>
  <br />
    <strong><?php echo $this->_tpl_vars['lng']['lbl_note']; ?>
:</strong> <?php echo $this->_tpl_vars['lng']['lbl_company_location_country_note']; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['option'] != 'User_Profiles' && $this->_tpl_vars['option'] != 'Contact_Us' && $this->_tpl_vars['option'] != 'Search_products'): ?>
  </form>
<?php endif; ?>

<?php if ($this->_tpl_vars['option'] == 'Shipping' && $this->_tpl_vars['is_realtime']): ?>

  <hr />

  <h3><?php echo $this->_tpl_vars['lng']['lbl_test_realtime_calculation']; ?>
</h3>

  <?php echo $this->_tpl_vars['lng']['txt_test_realtime_calculation_text']; ?>


  <br /><br />

  <form action="test_realtime_shipping.php" target="_blank">

    <label for="trs_weight"><?php echo $this->_tpl_vars['lng']['lbl_package_weight']; ?>
</label> <input type="text" id="trs_weight" name="weight" value="1" /> <input type="submit" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_test'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />

  </form>

<?php elseif ($this->_tpl_vars['option'] == 'Security'): ?>

  <hr />

  <h3><?php echo $this->_tpl_vars['lng']['lbl_test_data_encryption']; ?>
</h3>

  <a href="test_pgp.php"><?php echo $this->_tpl_vars['lng']['lbl_test_data_encryption_link']; ?>
</a>

<?php elseif ($this->_tpl_vars['option'] == 'XPayments_Connector'): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/XPayments_Connector/config_bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>

<br />

<?php endif; ?>

<?php if ($this->_tpl_vars['additional_config']): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['additional_config'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>