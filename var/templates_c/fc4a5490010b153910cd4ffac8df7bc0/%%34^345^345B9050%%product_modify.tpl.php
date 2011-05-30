<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from modules/Extra_Fields/product_modify.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'modules/Extra_Fields/product_modify.tpl', 17, false),)), $this); ?>
<?php $_from = $this->_tpl_vars['extra_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['exf'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['exf']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ef']):
        $this->_foreach['exf']['iteration']++;
?>

<?php if (($this->_foreach['exf']['iteration'] <= 1)): ?>
<tr>
  <?php if ($this->_tpl_vars['geid'] != ''): ?><td class="TableSubHead">&nbsp;</td><?php endif; ?>
  <td colspan="2"><hr /></td>
</tr>
<?php endif; ?>
<tr> 
<?php if ($this->_tpl_vars['geid'] != ''): ?><td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[efields][<?php echo $this->_tpl_vars['ef']['fieldid']; ?>
]" /></td><?php endif; ?>
  <td class="FormButton" nowrap="nowrap"><?php echo $this->_tpl_vars['ef']['field']; ?>
:</td>
  <td class="ProductDetails">
  <input type="text" name="efields[<?php echo $this->_tpl_vars['ef']['fieldid']; ?>
]" size="70" value="<?php if ($this->_tpl_vars['ef']['is_value'] == 'Y'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['ef']['field_value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['ef']['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
  </td>
</tr>
<?php endforeach; endif; unset($_from); ?>