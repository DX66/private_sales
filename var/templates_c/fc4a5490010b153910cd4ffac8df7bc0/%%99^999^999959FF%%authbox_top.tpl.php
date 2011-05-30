<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from authbox_top.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'authbox_top.tpl', 15, false),array('modifier', 'wm_remove', 'authbox_top.tpl', 17, false),array('modifier', 'escape', 'authbox_top.tpl', 17, false),array('modifier', 'amp', 'authbox_top.tpl', 17, false),)), $this); ?>
<?php func_load_lang($this, "authbox_top.tpl","lbl_your_partner_id,lbl_close_storefront,lbl_open,lbl_open_storefront,lbl_open_storefront_warning,lbl_close,lbl_logoff,lbl_keywords,lbl_search,txt_how_quick_search_works"); ?><table cellpadding="2" cellspacing="0" border="0">
<tr>
  <?php if ($this->_tpl_vars['login'] != '' && $this->_tpl_vars['usertype'] == 'B'): ?>
    <td nowrap="nowrap" height="20" valign="top" class="partnerid-info">
      <?php echo $this->_tpl_vars['lng']['lbl_your_partner_id']; ?>
: <strong><?php echo $this->_tpl_vars['logged_userid']; ?>
</strong>
    </td>
  <?php endif; ?>

  <td nowrap="nowrap" height="20" valign="top">
    <?php if ($this->_tpl_vars['config']['General']['shop_closed'] == 'Y'): ?>
      <div class="closed-store"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_close_storefront'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'STOREFRONT', $this->_tpl_vars['http_location'], 'SHOPKEY', $this->_tpl_vars['config']['General']['shop_closed_key']) : smarty_modifier_substitute($_tmp, 'STOREFRONT', $this->_tpl_vars['http_location'], 'SHOPKEY', $this->_tpl_vars['config']['General']['shop_closed_key'])); ?>
<?php if ($this->_tpl_vars['need_storefront_link']): ?> [ <a href="<?php echo $this->_tpl_vars['storefront_link']; ?>
"><?php echo $this->_tpl_vars['lng']['lbl_open']; ?>
</a> ]<?php endif; ?></div>
    <?php else: ?>
      <div class="open-store"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_open_storefront'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'STOREFRONT', $this->_tpl_vars['http_location']) : smarty_modifier_substitute($_tmp, 'STOREFRONT', $this->_tpl_vars['http_location'])); ?>
<?php if ($this->_tpl_vars['need_storefront_link']): ?> [ <a href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_open_storefront_warning'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
'))window.location='<?php echo ((is_array($_tmp=$this->_tpl_vars['storefront_link'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
';"><?php echo $this->_tpl_vars['lng']['lbl_close']; ?>
</a> ]<?php endif; ?></div>
    <?php endif; ?>
  </td>

  <td class="AuthText" height="20" valign="top">
    <a href="<?php echo $this->_tpl_vars['current_area']; ?>
/register.php?mode=update"><?php echo $this->_tpl_vars['fullname']; ?>
</a>
  </td>

  <td valign="top" class="auth-text-wrapper">
    [ <a href="login.php?mode=logout" class="AuthText"><?php echo $this->_tpl_vars['lng']['lbl_logoff']; ?>
</a> ]
  </td>

  <?php if ($this->_tpl_vars['need_quick_search'] == 'Y'): ?>

    <td width="50">&nbsp;</td>

    <td class="quick-search-form" valign="top">
      <form name="qsform" action="" onsubmit="javascript: quick_search($('#quick_search_query').val()); return false;">
        <input type="text" class="default-value" id="quick_search_query" onkeypress="javascript:$('#quick_search_panel').hide();" onclick="javascript:$('#quick_search_panel').hide();" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_keywords'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </form>
    </td>

    <td class="main-button">
      <button class="quick-search-button" onclick="javascript:quick_search($('#quick_search_query').val());return false;"><?php echo $this->_tpl_vars['lng']['lbl_search']; ?>
</button>

      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/tooltip_js.tpl", 'smarty_include_vars' => array('text' => $this->_tpl_vars['lng']['txt_how_quick_search_works'],'id' => 'qs_help','type' => 'img','sticky' => true,'alt_image' => "question_gray.png",'wrapper_tag' => 'div')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </td>
<?php endif; ?>

</tr>
</table>