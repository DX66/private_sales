<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/membership_selector.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'count', 'main/membership_selector.tpl', 11, false),array('function', 'inc', 'main/membership_selector.tpl', 12, false),)), $this); ?>
<?php func_load_lang($this, "main/membership_selector.tpl","lbl_all,lbl_hold_ctrl_key"); ?><?php if ($this->_tpl_vars['field'] == ''): ?>
  <?php $this->assign('field', "membershipids[]"); ?>
<?php endif; ?>
<?php $this->assign('size', 1); ?>

<?php if ($this->_tpl_vars['memberships']): ?>
  <?php echo smarty_function_count(array('assign' => 'size','value' => $this->_tpl_vars['memberships'],'print' => false), $this);?>

  <?php echo smarty_function_inc(array('assign' => 'size','value' => $this->_tpl_vars['size']), $this);?>


  <?php if ($this->_tpl_vars['size'] > 5): ?>
    <?php $this->assign('size', 5); ?>
  <?php endif; ?>

<?php endif; ?>

<select name="<?php echo $this->_tpl_vars['field']; ?>
" multiple="multiple" size="<?php echo $this->_tpl_vars['size']; ?>
">
  <option value="-1"<?php if ($this->_tpl_vars['data']['membershipids'] == ""): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_all']; ?>
</option>
  <?php if ($this->_tpl_vars['memberships']): ?>
    <?php $_from = $this->_tpl_vars['memberships']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
      <option value="<?php echo $this->_tpl_vars['v']['membershipid']; ?>
"<?php if ($this->_tpl_vars['data']['membershipids'] != "" && $this->_tpl_vars['data']['membershipids'][$this->_tpl_vars['v']['membershipid']] != ''): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['membership']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
  <?php endif; ?>
</select>
<?php if ($this->_tpl_vars['is_short'] != 'Y'): ?>
  <p><?php echo $this->_tpl_vars['lng']['lbl_hold_ctrl_key']; ?>
</p>
<?php endif; ?>