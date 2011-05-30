<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/clean_url_field.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'main/clean_url_field.tpl', 19, false),array('modifier', 'substitute', 'main/clean_url_field.tpl', 39, false),)), $this); ?>
<?php func_load_lang($this, "main/clean_url_field.tpl","lbl_clean_url,lbl_clean_url_what_is,txt_clean_url_descr,lbl_clean_url_save_old,lbl_clean_url_manage_history,lbl_clean_url_format_warning,lbl_clean_url_format_warning_provider,lbl_clean_url_disabled_mode_warning,lbl_clean_url_disabled_mode_warning_provider"); ?><tr>

  <?php if ($this->_tpl_vars['geid'] != ''): ?>
    <td width="15" class="TableSubHead" valign="top"><input type="checkbox" disabled="disabled"/></td>
  <?php endif; ?>

  <td class="FormButton" nowrap="nowrap" valign="top"><?php echo $this->_tpl_vars['lng']['lbl_clean_url']; ?>
:</td>
  
  <?php if ($this->_tpl_vars['show_req_fields'] == 'Y'): ?>
    <td width="10" height="10" valign="top"><span class="Star"><?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>*<?php endif; ?></span></td>
  <?php endif; ?>
  
  <td class="ProductDetails" width="80%">
    <div>
      <input type="text" name="clean_url" id="clean_url" size="45" maxlength="250" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['clean_url'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?> onchange="javascript: checkCleanUrl(this, 'Y', 'Y');"<?php else: ?>class="ReadOnlyField" readonly="readonly"<?php endif; ?> />&nbsp;
      <?php if ($this->_tpl_vars['clean_url_fill_error']): ?><span class="Star">&lt;&lt;&nbsp;</span><?php endif; ?>

      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/tooltip_js.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_clean_url_what_is'],'text' => $this->_tpl_vars['lng']['txt_clean_url_descr'],'id' => $this->_tpl_vars['tooltip_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

    <br />

    <div class="SmallText">
    <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>

      <div id="clean_url_error" class="Star"></div>
      <input type="checkbox" name="clean_url_save_in_history" id="clean_url_save_in_history" value="Y" checked="checked" />
      <label for="clean_url_save_in_history"><?php echo $this->_tpl_vars['lng']['lbl_clean_url_save_old']; ?>
</label><br />

      <?php if ($this->_tpl_vars['clean_urls_history']): ?>
        [ <a href="#clean_url_history"><?php echo $this->_tpl_vars['lng']['lbl_clean_url_manage_history']; ?>
</a> ]<br />
      <?php endif; ?>

      <?php if ($this->_tpl_vars['is_admin_user']): ?>
        <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_clean_url_format_warning'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'seo_option_page', ($this->_tpl_vars['catalogs']['admin'])."/configuration.php?option=SEO") : smarty_modifier_substitute($_tmp, 'seo_option_page', ($this->_tpl_vars['catalogs']['admin'])."/configuration.php?option=SEO")); ?>

      <?php else: ?>
        <?php echo $this->_tpl_vars['lng']['lbl_clean_url_format_warning_provider']; ?>

      <?php endif; ?>

    <?php else: ?>

      <?php if ($this->_tpl_vars['usertype'] == 'A' || ( $this->_tpl_vars['usertype'] == 'P' && $this->_tpl_vars['active_modules']['Simple_Mode'] == 'Y' )): ?>
        <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_clean_url_disabled_mode_warning'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'seo_option_page', ($this->_tpl_vars['catalogs']['admin'])."/configuration.php?option=SEO") : smarty_modifier_substitute($_tmp, 'seo_option_page', ($this->_tpl_vars['catalogs']['admin'])."/configuration.php?option=SEO")); ?>

      <?php else: ?>
        <?php echo $this->_tpl_vars['lng']['lbl_clean_url_disabled_mode_warning_provider']; ?>

      <?php endif; ?>

    <?php endif; ?>

    </div>

    <br />

  </td>

</tr>