<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/top_message_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'main/top_message_js.tpl', 14, false),array('modifier', 'escape', 'main/top_message_js.tpl', 14, false),)), $this); ?>
<?php func_load_lang($this, "main/top_message_js.tpl","lbl_error,lbl_warning,lbl_information"); ?><script type="text/javascript">
//<![CDATA[
var top_message_icon = {
  "E": "<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_error_small.gif",
  "W": "<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_warning_small.gif",
  "I": "<?php echo $this->_tpl_vars['ImagesDir']; ?>
/icon_info_small.gif"
};

var top_message_title = {
  "E": "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_error'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  "W": "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_warning'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
",
  "I": "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_information'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
"
};
//]]>
</script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/top_message.js"></script>