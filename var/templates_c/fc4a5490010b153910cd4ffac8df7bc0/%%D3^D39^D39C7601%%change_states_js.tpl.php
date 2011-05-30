<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from change_states_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'change_states_js.tpl', 7, false),array('modifier', 'escape', 'change_states_js.tpl', 7, false),array('modifier', 'replace', 'change_states_js.tpl', 28, false),)), $this); ?>
<?php func_load_lang($this, "change_states_js.tpl","lbl_country_doesnt_require_state,lbl_country_doesnt_require_county"); ?><script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var txt_no_states = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_country_doesnt_require_state'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var txt_no_counties = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_country_doesnt_require_county'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var use_counties = <?php if ($this->_tpl_vars['config']['General']['use_counties'] == 'Y'): ?>true<?php else: ?>false<?php endif; ?>;
var states_sort_override = <?php if (( $this->_tpl_vars['config']['UA']['browser'] == 'Opera' && $this->_tpl_vars['config']['UA']['version'] < 8 ) || $this->_tpl_vars['config']['UA']['browser'] == 'Safari' || $this->_tpl_vars['config']['UA']['browser'] == 'Chrome'): ?>true<?php else: ?>false<?php endif; ?>;

var config_default_country = "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['config']['General']['default_country'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";

var countries = {};
<?php $this->assign('cnt', 0); ?>
<?php if ($this->_tpl_vars['countries']): ?>
<?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
 = {states: <?php if ($this->_tpl_vars['v']['display_states'] == 'Y'): ?>[]<?php else: ?>false<?php endif; ?>, statesHash: <?php if ($this->_tpl_vars['v']['display_states'] == 'Y'): ?>[]<?php else: ?>false<?php endif; ?>};

<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

var i;
<?php if ($this->_tpl_vars['states'] != ''): ?>
i = 0;
<?php $_from = $this->_tpl_vars['states']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
.statesHash[i] = <?php echo $this->_tpl_vars['v']['stateid']; ?>
;
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
.states[<?php echo $this->_tpl_vars['v']['stateid']; ?>
] = {code: "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['state_code'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')); ?>
", name: "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['state'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')); ?>
", counties: [], order: i++ };

<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['config']['General']['use_counties'] == 'Y' && $this->_tpl_vars['counties'] != ''): ?>
i = 0;
<?php $_from = $this->_tpl_vars['counties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
.states[<?php echo $this->_tpl_vars['v']['stateid']; ?>
].counties[<?php echo $this->_tpl_vars['v']['countyid']; ?>
] = {name: "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['county'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')); ?>
", order: i++ };
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

var opera_ini_states_mem = <?php if ($this->_tpl_vars['ship2diff']): ?>1<?php else: ?>0<?php endif; ?>;

//]]>
</script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/change_states.js"></script>
