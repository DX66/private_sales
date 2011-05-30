<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:26
         compiled from main/states.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/states.tpl', 6, false),array('modifier', 'replace', 'main/states.tpl', 6, false),array('modifier', 'wm_remove', 'main/states.tpl', 8, false),array('modifier', 'escape', 'main/states.tpl', 8, false),array('modifier', 'amp', 'main/states.tpl', 13, false),)), $this); ?>
<?php func_load_lang($this, "main/states.tpl","lbl_please_select_one,lbl_other"); ?><?php if ($this->_tpl_vars['states'] != ""): ?>
<select name="<?php echo $this->_tpl_vars['name']; ?>
" id="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['name'])))) ? $this->_run_mod_handler('replace', true, $_tmp, "[", '_') : smarty_modifier_replace($_tmp, "[", '_')))) ? $this->_run_mod_handler('replace', true, $_tmp, "]", "") : smarty_modifier_replace($_tmp, "]", "")); ?>
" <?php echo $this->_tpl_vars['style']; ?>
>
<?php if ($this->_tpl_vars['required'] == 'N'): ?>
  <option value="">[<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_please_select_one'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
]</option>
<?php endif; ?>
  <option value="<?php if ($this->_tpl_vars['value_for_other'] != 'no'): ?>Other<?php endif; ?>"<?php if ($this->_tpl_vars['default'] == 'Other'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_other']; ?>
</option>
<?php unset($this->_sections['state_idx']);
$this->_sections['state_idx']['name'] = 'state_idx';
$this->_sections['state_idx']['loop'] = is_array($_loop=$this->_tpl_vars['states']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['state_idx']['show'] = true;
$this->_sections['state_idx']['max'] = $this->_sections['state_idx']['loop'];
$this->_sections['state_idx']['step'] = 1;
$this->_sections['state_idx']['start'] = $this->_sections['state_idx']['step'] > 0 ? 0 : $this->_sections['state_idx']['loop']-1;
if ($this->_sections['state_idx']['show']) {
    $this->_sections['state_idx']['total'] = $this->_sections['state_idx']['loop'];
    if ($this->_sections['state_idx']['total'] == 0)
        $this->_sections['state_idx']['show'] = false;
} else
    $this->_sections['state_idx']['total'] = 0;
if ($this->_sections['state_idx']['show']):

            for ($this->_sections['state_idx']['index'] = $this->_sections['state_idx']['start'], $this->_sections['state_idx']['iteration'] = 1;
                 $this->_sections['state_idx']['iteration'] <= $this->_sections['state_idx']['total'];
                 $this->_sections['state_idx']['index'] += $this->_sections['state_idx']['step'], $this->_sections['state_idx']['iteration']++):
$this->_sections['state_idx']['rownum'] = $this->_sections['state_idx']['iteration'];
$this->_sections['state_idx']['index_prev'] = $this->_sections['state_idx']['index'] - $this->_sections['state_idx']['step'];
$this->_sections['state_idx']['index_next'] = $this->_sections['state_idx']['index'] + $this->_sections['state_idx']['step'];
$this->_sections['state_idx']['first']      = ($this->_sections['state_idx']['iteration'] == 1);
$this->_sections['state_idx']['last']       = ($this->_sections['state_idx']['iteration'] == $this->_sections['state_idx']['total']);
?>
<?php if ($this->_tpl_vars['config']['General']['default_country'] == $this->_tpl_vars['states'][$this->_sections['state_idx']['index']]['country_code'] || $this->_tpl_vars['country_name'] == '' || $this->_tpl_vars['default_fields'][$this->_tpl_vars['country_name']]['avail'] == 'Y'): ?>
  <option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['states'][$this->_sections['state_idx']['index']]['state_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['default'] == $this->_tpl_vars['states'][$this->_sections['state_idx']['index']]['state_code'] && ( ! $this->_tpl_vars['default_country'] || $this->_tpl_vars['default_country'] == $this->_tpl_vars['states'][$this->_sections['state_idx']['index']]['country_code'] )): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['states'][$this->_sections['state_idx']['index']]['country_code']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['states'][$this->_sections['state_idx']['index']]['state'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</option>
<?php endif; ?>
<?php endfor; endif; ?>
</select>
<?php else: ?>
<input type="text"<?php if ($this->_tpl_vars['name'] != ''): ?> id="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['name'])))) ? $this->_run_mod_handler('replace', true, $_tmp, "[", '_') : smarty_modifier_replace($_tmp, "[", '_')))) ? $this->_run_mod_handler('replace', true, $_tmp, "]", "") : smarty_modifier_replace($_tmp, "]", "")); ?>
"<?php endif; ?> size="32" maxlength="65" name="<?php echo $this->_tpl_vars['name']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['default'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
<?php endif; ?>