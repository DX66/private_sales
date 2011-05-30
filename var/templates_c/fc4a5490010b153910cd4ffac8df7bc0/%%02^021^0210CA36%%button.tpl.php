<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from buttons/button.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'regex_replace', 'buttons/button.tpl', 10, false),array('modifier', 'cat', 'buttons/button.tpl', 12, false),array('modifier', 'amp', 'buttons/button.tpl', 12, false),array('modifier', 'escape', 'buttons/button.tpl', 26, false),array('modifier', 'default', 'buttons/button.tpl', 76, false),)), $this); ?>
<?php if ($this->_tpl_vars['config']['Adaptives']['platform'] == 'MacPPC' && $this->_tpl_vars['config']['Adaptives']['browser'] == 'NN'): ?>
  <?php $this->assign('js_to_href', 'Y'); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['type'] == 'input'): ?>
  <?php $this->assign('img_type', 'input type="image"'); ?>
<?php else: ?>
  <?php $this->assign('img_type', 'img'); ?>
<?php endif; ?>
<?php $this->assign('js_link', ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/^\s*javascript\s*:/Si", "") : smarty_modifier_regex_replace($_tmp, "/^\s*javascript\s*:/Si", ""))); ?>
<?php if ($this->_tpl_vars['js_link'] == $this->_tpl_vars['href']): ?>
  <?php $this->assign('js_link', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp="javascript: self.location='")) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['href']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['href'])))) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)))) ? $this->_run_mod_handler('cat', true, $_tmp, "';") : smarty_modifier_cat($_tmp, "';"))); ?>
<?php else: ?>
  <?php $this->assign('js_link', $this->_tpl_vars['href']); ?>
  <?php if ($this->_tpl_vars['js_to_href'] != 'Y'): ?>
    <?php $this->assign('onclick', $this->_tpl_vars['href']); ?>
  <?php if ($this->_tpl_vars['style'] != 'button' && $this->_tpl_vars['submit'] == 'Y'): ?>
  <?php $this->assign('href', "#"); ?>
  <?php else: ?>
  <?php $this->assign('href', "javascript:void(0);"); ?>
  <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['style'] == 'button' && ( $this->_tpl_vars['config']['Adaptives']['platform'] != 'MacPPC' || $this->_tpl_vars['config']['Adaptives']['browser'] != 'NN' )): ?>
<table cellspacing="0" cellpadding="0" onclick="<?php echo $this->_tpl_vars['js_link']; ?>
" class="ButtonTable"<?php if ($this->_tpl_vars['title'] != ''): ?> title="<?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
<?php echo '<tr><td><'; ?><?php echo $this->_tpl_vars['img_type']; ?><?php echo ' src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/but1.gif" class="ButtonSide" alt="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '" /></td><td class="Button"'; ?><?php echo $this->_tpl_vars['reading_direction_tag']; ?><?php echo '><font class="Button">'; ?><?php echo $this->_tpl_vars['button_title']; ?><?php echo '</font></td><td><img src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/but2.gif" class="ButtonSide" alt="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '" /></td></tr>'; ?>

</table>
<?php elseif ($this->_tpl_vars['image_menu']): ?>
<?php echo '<table cellspacing="0" class="SimpleButton"><tr>'; ?><?php if ($this->_tpl_vars['button_title'] != ''): ?><?php echo '<td><a class="VertMenuItems" href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['onclick'] != ''): ?><?php echo ' onclick="'; ?><?php echo $this->_tpl_vars['onclick']; ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['title'] != ''): ?><?php echo ' title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['target'] != ''): ?><?php echo ' target="'; ?><?php echo $this->_tpl_vars['target']; ?><?php echo '"'; ?><?php endif; ?><?php echo '><font class="VertMenuItems">'; ?><?php echo $this->_tpl_vars['button_title']; ?><?php echo '</font></a>&nbsp;</td>'; ?><?php endif; ?><?php echo '<td>'; ?><?php if ($this->_tpl_vars['img_type'] == 'img'): ?><?php echo '<a class="VertMenuItems" href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['onclick'] != ''): ?><?php echo ' onclick="'; ?><?php echo $this->_tpl_vars['onclick']; ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['title'] != ''): ?><?php echo ' title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['target'] != ''): ?><?php echo ' target="'; ?><?php echo $this->_tpl_vars['target']; ?><?php echo '"'; ?><?php endif; ?><?php echo '>'; ?><?php endif; ?><?php echo '<'; ?><?php echo $this->_tpl_vars['img_type']; ?><?php echo ' '; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/go_image_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ' />'; ?><?php if ($this->_tpl_vars['img_type'] == 'img'): ?><?php echo '</a>'; ?><?php endif; ?><?php echo '</td></tr></table>'; ?>

<?php else: ?>
<?php echo '<table cellspacing="0" class="SimpleButton"><tr>'; ?><?php if ($this->_tpl_vars['button_title'] != ''): ?><?php echo '<td><a class="'; ?><?php if ($this->_tpl_vars['img_type'] == 'img'): ?><?php echo 'simple-button simple-'; ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['substyle'])) ? $this->_run_mod_handler('default', true, $_tmp, 'arrow') : smarty_modifier_default($_tmp, 'arrow')); ?><?php echo '-button'; ?><?php else: ?><?php echo 'Button'; ?><?php endif; ?><?php echo '" href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['onclick'] != ''): ?><?php echo ' onclick="'; ?><?php echo $this->_tpl_vars['onclick']; ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['title'] != ''): ?><?php echo ' title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['target'] != ''): ?><?php echo ' target="'; ?><?php echo $this->_tpl_vars['target']; ?><?php echo '"'; ?><?php endif; ?><?php echo '>'; ?><?php echo $this->_tpl_vars['button_title']; ?><?php echo '</a></td>'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['img_type'] != 'img'): ?><?php echo '<td>&nbsp;<'; ?><?php echo $this->_tpl_vars['img_type']; ?><?php echo ' '; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/go_image.tpl", 'smarty_include_vars' => array('full_url' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ' /></td>'; ?><?php endif; ?><?php echo '</tr></table>'; ?>

<?php endif; ?>