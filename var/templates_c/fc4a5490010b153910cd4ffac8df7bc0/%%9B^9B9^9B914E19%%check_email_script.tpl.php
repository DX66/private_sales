<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from check_email_script.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'check_email_script.tpl', 7, false),array('modifier', 'escape', 'check_email_script.tpl', 7, false),array('modifier', 'replace', 'check_email_script.tpl', 7, false),)), $this); ?>
<?php func_load_lang($this, "check_email_script.tpl","txt_email_invalid"); ?><script type="text/javascript">
//<![CDATA[
var txt_email_invalid = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_email_invalid'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\r", ' ') : smarty_modifier_replace($_tmp, "\r", ' ')); ?>
";
var email_validation_regexp = new RegExp("<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['email_validation_regexp'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
", "gi");
//]]>
</script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/check_email_script.js"></script>