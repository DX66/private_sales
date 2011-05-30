<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Gift_Registry/product_event_cart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'modules/Gift_Registry/product_event_cart.tpl', 12, false),)), $this); ?>
<?php func_load_lang($this, "modules/Gift_Registry/product_event_cart.tpl","lbl_giftreg_buy_as_present"); ?><?php if ($this->_tpl_vars['product']['event_data'] != ""): ?>
<?php $this->assign('creator', ($this->_tpl_vars['product']['event_data']['creator_title'])." ".($this->_tpl_vars['product']['event_data']['firstname'])." ".($this->_tpl_vars['product']['event_data']['lastname'])); ?>
<div class="event-info">
<input type="hidden" id="event_mark_<?php echo $this->_tpl_vars['product']['cartid']; ?>
" name="event_mark[<?php echo $this->_tpl_vars['product']['cartid']; ?>
]" value="Y" />
<table class="valign-middle">
<tr>
	<td><input type="checkbox" id="em_<?php echo $this->_tpl_vars['product']['cartid']; ?>
" name="em[<?php echo $this->_tpl_vars['product']['cartid']; ?>
]" checked="checked" onclick="javascript: _getById('event_mark_<?php echo $this->_tpl_vars['product']['cartid']; ?>
').value= (this.checked) ? 'Y' : 'N'" /></td>
  <td><label for="em_<?php echo $this->_tpl_vars['product']['cartid']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_giftreg_buy_as_present'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'event_name', $this->_tpl_vars['product']['event_data']['title'], 'eventid', $this->_tpl_vars['product']['event_data']['event_id'], 'creator', $this->_tpl_vars['creator']) : smarty_modifier_substitute($_tmp, 'event_name', $this->_tpl_vars['product']['event_data']['title'], 'eventid', $this->_tpl_vars['product']['event_data']['event_id'], 'creator', $this->_tpl_vars['creator'])); ?>
</label></td>
</tr>
</table>
</div>
<?php endif; ?>