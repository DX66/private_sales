<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:40
         compiled from main/multirow.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'main/multirow.tpl', 7, false),array('modifier', 'escape', 'main/multirow.tpl', 7, false),)), $this); ?>
<?php func_load_lang($this, "main/multirow.tpl","lbl_remove_row,lbl_add_row"); ?><script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var lbl_remove_row = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_remove_row'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var lbl_add_row = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_add_row'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
var inputset_plus_img = "<?php echo $this->_tpl_vars['ImagesDir']; ?>
/plus.gif";
var inputset_minus_img = "<?php echo $this->_tpl_vars['ImagesDir']; ?>
/minus.gif";
//]]>
</script>
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/plus.gif" width="0" height="0" alt="" style="display: none" />
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/minus.gif" width="0" height="0" alt="" style="display: none" />
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/multirow.js"></script>