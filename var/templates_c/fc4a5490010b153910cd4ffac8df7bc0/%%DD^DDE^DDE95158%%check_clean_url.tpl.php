<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from check_clean_url.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'check_clean_url.tpl', 7, false),array('modifier', 'escape', 'check_clean_url.tpl', 7, false),array('modifier', 'replace', 'check_clean_url.tpl', 7, false),)), $this); ?>
<?php func_load_lang($this, "check_clean_url.tpl","err_clean_url_wrong_format"); ?><script type="text/javascript">
//<![CDATA[
var err_clean_url_wrong_format = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['err_clean_url_wrong_format'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\r", ' ') : smarty_modifier_replace($_tmp, "\r", ' ')); ?>
";
var clean_url_validation_regexp = new RegExp("<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['clean_url_validation_regexp'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", "g");
//]]>
</script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/check_clean_url.js"></script>