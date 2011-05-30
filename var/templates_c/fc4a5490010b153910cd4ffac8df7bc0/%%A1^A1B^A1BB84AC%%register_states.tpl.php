<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:26
         compiled from main/register_states.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip_tags', 'main/register_states.tpl', 9, false),array('modifier', 'escape', 'main/register_states.tpl', 9, false),)), $this); ?>
<?php if ($this->_tpl_vars['country_id'] == ''): ?>
  <?php $this->assign('country_id', $this->_tpl_vars['country_name']); ?>
<?php endif; ?>
<span style="display:none;">
<input id="<?php echo $this->_tpl_vars['country_id']; ?>
_state_value" type="text" value='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['state_value'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
' />
<input id="<?php echo $this->_tpl_vars['country_id']; ?>
_county_value" type="text" value='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['county_value'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
' />
</span>
<script type="text/javascript">
//<![CDATA[
init_js_states(document.getElementById('<?php echo $this->_tpl_vars['country_id']; ?>
'), '<?php echo $this->_tpl_vars['state_name']; ?>
', '<?php echo $this->_tpl_vars['county_name']; ?>
', '<?php echo $this->_tpl_vars['country_id']; ?>
'<?php if ($this->_tpl_vars['is_ajax_request'] || $this->_tpl_vars['is_modal_popup']): ?>, true<?php endif; ?>);
//]]>
</script>
