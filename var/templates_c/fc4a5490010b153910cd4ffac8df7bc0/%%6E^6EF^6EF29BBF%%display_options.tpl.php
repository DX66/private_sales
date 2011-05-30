<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Product_Options/display_options.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'modules/Product_Options/display_options.tpl', 25, false),array('modifier', 'replace', 'modules/Product_Options/display_options.tpl', 36, false),)), $this); ?>
<?php func_load_lang($this, "modules/Product_Options/display_options.tpl","lbl_product_options,lbl_product_options_expired"); ?><?php if ($this->_tpl_vars['options'] && $this->_tpl_vars['force_product_options_txt'] == ''): ?>

<?php if ($this->_tpl_vars['is_plain'] == 'Y'): ?>

<?php if ($this->_tpl_vars['options'] != $this->_tpl_vars['options_txt']): ?>

<?php $_from = $this->_tpl_vars['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
   <?php echo $this->_tpl_vars['v']['class']; ?>
: <?php echo $this->_tpl_vars['v']['option_name']; ?>

<?php endforeach; endif; unset($_from); ?>

<?php else: ?>

<?php echo $this->_tpl_vars['options_txt']; ?>


<?php endif; ?>

<?php else: ?>

<?php if ($this->_tpl_vars['options'] != $this->_tpl_vars['options_txt']): ?>

<table cellspacing="0" class="poptions-options-list" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_product_options'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php $_from = $this->_tpl_vars['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
  <tr>
    <td><?php echo ((is_array($_tmp=$this->_tpl_vars['v']['class'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
:</td>
    <td><?php echo ((is_array($_tmp=$this->_tpl_vars['v']['option_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
  </tr>
<?php endforeach; endif; unset($_from); ?>
</table>

<?php else: ?>

<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['options_txt'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", "<br />") : smarty_modifier_replace($_tmp, "\n", "<br />")); ?>


<?php endif; ?>

<?php endif; ?>

<?php elseif ($this->_tpl_vars['force_product_options_txt']): ?>

<?php if ($this->_tpl_vars['is_plain'] == 'Y'): ?>

<?php echo ((is_array($_tmp=$this->_tpl_vars['options_txt'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>


<?php else: ?>

<?php echo ((is_array($_tmp=$this->_tpl_vars['options_txt'])) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", "<br />") : smarty_modifier_replace($_tmp, "\n", "<br />")); ?>


<?php endif; ?>

<?php endif; ?>

<?php if (( $this->_tpl_vars['options'] || $this->_tpl_vars['force_product_options_txt'] ) && $this->_tpl_vars['product']['options_expired']): ?>
<div id="cart_message_<?php echo $this->_tpl_vars['product']['cartid']; ?>
" class="cart-message cart-message-W">
<div class="close-link" onclick="javascript: return close_opts_expire_msg('<?php echo $this->_tpl_vars['product']['cartid']; ?>
');">&nbsp;</div>
<?php echo $this->_tpl_vars['lng']['lbl_product_options_expired']; ?>

</div>
<?php endif; ?>
