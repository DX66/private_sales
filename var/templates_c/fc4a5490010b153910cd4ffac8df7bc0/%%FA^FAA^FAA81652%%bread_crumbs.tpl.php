<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from customer/bread_crumbs.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'customer/bread_crumbs.tpl', 12, false),)), $this); ?>
<?php if ($this->_tpl_vars['location']): ?>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top" align="left">
  <div id="location">
      <?php $_from = $this->_tpl_vars['location']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['location'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['location']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['l']):
        $this->_foreach['location']['iteration']++;
?>
        <?php if ($this->_tpl_vars['l']['1'] && ! ($this->_foreach['location']['iteration'] == $this->_foreach['location']['total'])): ?>
          <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['l']['1'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" class="bread-crumb<?php if (($this->_foreach['location']['iteration'] == $this->_foreach['location']['total'])): ?> last-bread-crumb<?php endif; ?>"><?php if ($this->_tpl_vars['webmaster_mode'] == 'editor'): ?><?php echo $this->_tpl_vars['l']['0']; ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['l']['0'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php endif; ?></a>
        <?php else: ?>
          <font class="bread-crumb<?php if (($this->_foreach['location']['iteration'] == $this->_foreach['location']['total'])): ?> last-bread-crumb<?php endif; ?>"><?php if ($this->_tpl_vars['webmaster_mode'] == 'editor'): ?><?php echo $this->_tpl_vars['l']['0']; ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['l']['0'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php endif; ?></font>
        <?php endif; ?>
        <?php if (! ($this->_foreach['location']['iteration'] == $this->_foreach['location']['total']) && $this->_tpl_vars['config']['Appearance']['breadcrumbs_separator'] != ''): ?>
          <span><?php echo ((is_array($_tmp=$this->_tpl_vars['config']['Appearance']['breadcrumbs_separator'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</span>
        <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
  </div>
  </td>
  <td width="130" valign="top" align="right">
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/printable_link.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </td>
</tr>
</table>
<?php endif; ?>