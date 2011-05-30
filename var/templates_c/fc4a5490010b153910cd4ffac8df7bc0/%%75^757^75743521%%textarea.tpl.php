<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/textarea.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'regex_replace', 'main/textarea.tpl', 8, false),array('modifier', 'default', 'main/textarea.tpl', 12, false),array('modifier', 'escape', 'main/textarea.tpl', 21, false),)), $this); ?>
<?php func_load_lang($this, "main/textarea.tpl","lbl_default_editor,lbl_default_editor,lbl_advanced_editor,lbl_advanced_editor"); ?><?php if ($this->_tpl_vars['active_modules']['HTML_Editor'] && ! $this->_tpl_vars['html_editor_disabled']): ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/start_textarea.tpl", 'smarty_include_vars' => array('_include_once' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->assign('id', ((is_array($_tmp=$this->_tpl_vars['name'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/[^\w\d_]/", "") : smarty_modifier_regex_replace($_tmp, "/[^\w\d_]/", ""))); ?>
<?php echo ''; ?><?php if ($this->_tpl_vars['no_links'] != 'Y'): ?><?php echo '<div class="AELinkBox" style="width: '; ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['width'])) ? $this->_run_mod_handler('default', true, $_tmp, "80%") : smarty_modifier_default($_tmp, "80%")); ?><?php echo ';"><a href="javascript:void(0);" style="display: none;" id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo 'Dis" onclick="javascript: disableEditor(\''; ?><?php echo $this->_tpl_vars['id']; ?><?php echo '\',\''; ?><?php echo $this->_tpl_vars['name']; ?><?php echo '\');">'; ?><?php echo $this->_tpl_vars['lng']['lbl_default_editor']; ?><?php echo '</a><b id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo 'DisB">'; ?><?php echo $this->_tpl_vars['lng']['lbl_default_editor']; ?><?php echo '</b>&nbsp;&nbsp;<a href="javascript:void(0);" id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo 'Enb" onclick="javascript: enableEditor(\''; ?><?php echo $this->_tpl_vars['id']; ?><?php echo '\',\''; ?><?php echo $this->_tpl_vars['name']; ?><?php echo '\');">'; ?><?php echo $this->_tpl_vars['lng']['lbl_advanced_editor']; ?><?php echo '</a><b id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo 'EnbB" style="display: none;">'; ?><?php echo $this->_tpl_vars['lng']['lbl_advanced_editor']; ?><?php echo '</b></div>'; ?><?php endif; ?><?php echo '<textarea id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo '" name="'; ?><?php echo $this->_tpl_vars['name']; ?><?php echo '"'; ?><?php if ($this->_tpl_vars['cols']): ?><?php echo ' cols="'; ?><?php echo $this->_tpl_vars['cols']; ?><?php echo '"'; ?><?php endif; ?><?php echo ' '; ?><?php if ($this->_tpl_vars['rows']): ?><?php echo ' rows="'; ?><?php echo $this->_tpl_vars['rows']; ?><?php echo '"'; ?><?php endif; ?><?php echo ' class="InputWidth '; ?><?php echo $this->_tpl_vars['class']; ?><?php echo '" style="width: '; ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['width'])) ? $this->_run_mod_handler('default', true, $_tmp, "80%") : smarty_modifier_default($_tmp, "80%")); ?><?php echo ';">'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['data'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '</textarea><div id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo 'Box" style="display:none;"><textarea id="'; ?><?php echo $this->_tpl_vars['id']; ?><?php echo 'Adv"'; ?><?php if ($this->_tpl_vars['cols']): ?><?php echo ' cols="'; ?><?php echo $this->_tpl_vars['cols']; ?><?php echo '"'; ?><?php endif; ?><?php echo ' '; ?><?php if ($this->_tpl_vars['rows']): ?><?php echo ' rows="'; ?><?php echo $this->_tpl_vars['rows']; ?><?php echo '"'; ?><?php endif; ?><?php echo ' class="InputWidth '; ?><?php echo $this->_tpl_vars['class']; ?><?php echo '" style="width: 576px;'; ?><?php if ($this->_tpl_vars['no_links'] == 'Y'): ?><?php echo 'display:none;'; ?><?php endif; ?><?php echo '">'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['data'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '</textarea>'; ?><?php if ($this->_tpl_vars['config']['HTML_Editor']['editor'] == 'ckeditor'): ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/HTML_Editor/editors/ckeditor/textarea.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['id'],'name' => $this->_tpl_vars['name'],'data' => $this->_tpl_vars['data'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['config']['HTML_Editor']['editor'] == 'innovaeditor'): ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/HTML_Editor/editors/innovaeditor/textarea.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['id'],'name' => $this->_tpl_vars['name'],'data' => $this->_tpl_vars['data'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['config']['HTML_Editor']['editor'] == 'tinymce'): ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/HTML_Editor/editors/tinymce/textarea.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['id'],'name' => $this->_tpl_vars['name'],'data' => $this->_tpl_vars['data'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ''; ?><?php endif; ?><?php echo '</div>'; ?>


<script type="text/javascript">
//<![CDATA[
var isOpen = getCookie('<?php echo $this->_tpl_vars['id']; ?>
EditorEnabled');
if (isOpen && isOpen == 'Y')
  $('#<?php echo $this->_tpl_vars['id']; ?>
Enb').click();
//]]>
</script>

<?php else: ?>
<textarea id="<?php echo $this->_tpl_vars['id']; ?>
" name="<?php echo $this->_tpl_vars['name']; ?>
"<?php if ($this->_tpl_vars['cols']): ?> cols="<?php echo $this->_tpl_vars['cols']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['rows']): ?> rows="<?php echo $this->_tpl_vars['rows']; ?>
"<?php endif; ?> class="InputWidth <?php echo $this->_tpl_vars['class']; ?>
" <?php if ($this->_tpl_vars['style']): ?> style="<?php echo $this->_tpl_vars['style']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['disabled']): ?> disabled="disabled"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['data'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>

<?php endif; ?>