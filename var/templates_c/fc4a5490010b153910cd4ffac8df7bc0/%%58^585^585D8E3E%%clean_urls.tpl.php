<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/clean_urls.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'main/clean_urls.tpl', 33, false),array('modifier', 'escape', 'main/clean_urls.tpl', 35, false),)), $this); ?>
<?php func_load_lang($this, "main/clean_urls.tpl","lbl_clean_url_value,lbl_delete_selected,lbl_clean_url_history"); ?><?php if ($this->_tpl_vars['clean_urls_history']): ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_clean_url.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <a name="clean_url_history"></a>

  <?php ob_start(); ?>

  <script type="text/javascript" language="JavaScript 1.2">//<![CDATA[
  var clean_urls_history = new Array(<?php $_from = $this->_tpl_vars['clean_urls_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>'clean_urls_history[<?php echo $this->_tpl_vars['k']; ?>
]',<?php endforeach; endif; unset($_from); ?>'');
  //]]></script>

  <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
    <form action="<?php echo $this->_tpl_vars['clean_url_action']; ?>
" method="post" name="clean_urls_history_form">
    <input type="hidden" name="<?php echo $this->_tpl_vars['resource_name']; ?>
" value="<?php echo $this->_tpl_vars['resource_id']; ?>
" />
    <input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['clean_urls_history_mode']; ?>
" />
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/check_all_row.tpl", 'smarty_include_vars' => array('style' => "line-height: 170%;",'form' => 'clean_urls_history_form','prefix' => 'clean_urls_history')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endif; ?>

  <table cellpadding="2" cellspacing="1" border="0">
    <tr class="TableHead">
      <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
        <th width="15">&nbsp;</th>
      <?php endif; ?>
      <th><?php echo $this->_tpl_vars['lng']['lbl_clean_url_value']; ?>
</th>
    </tr>
    <?php $_from = $this->_tpl_vars['clean_urls_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['kurl'] => $this->_tpl_vars['url']):
?>
      <tr<?php echo smarty_function_cycle(array('values' => " , class='TableSubHead'"), $this);?>
>
        <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
          <td valign="top"><input type="checkbox" name="clean_urls_history[<?php echo $this->_tpl_vars['kurl']; ?>
]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kurl'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></td>
        <?php endif; ?>
        <td valign="top" width="300"><?php echo $this->_tpl_vars['url']; ?>
</td>
      </tr>
    <?php endforeach; endif; unset($_from); ?>
    <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
      <tr>
        <td colspan="2">
          <input type="button" value="<?php echo $this->_tpl_vars['lng']['lbl_delete_selected']; ?>
" onclick="javascript: if (checkMarks(this.form, new RegExp('clean_urls_history', 'ig'))) document.clean_urls_history_form.submit();" />
        </td>
      </tr>
    <?php endif; ?>
  </table>
  <?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
    </form>
  <?php endif; ?>
  <?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_clean_url_history'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>