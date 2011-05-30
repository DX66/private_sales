<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/dialog_message.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'lower', 'customer/dialog_message.tpl', 8, false),array('modifier', 'default', 'customer/dialog_message.tpl', 8, false),array('modifier', 'escape', 'customer/dialog_message.tpl', 8, false),array('modifier', 'amp', 'customer/dialog_message.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "customer/dialog_message.tpl","lbl_close,lbl_go_to_last_edit_section"); ?><?php if ($this->_tpl_vars['top_message']['content'] != "" || $this->_tpl_vars['alt_content'] != ""): ?>

  <div id="dialog-message">
    <div class="box message-<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['top_message']['type'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 'i') : smarty_modifier_default($_tmp, 'i')); ?>
"<?php if ($this->_tpl_vars['top_message']['title']): ?> title="<?php echo ((is_array($_tmp=$this->_tpl_vars['top_message']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>

      <?php if ($this->_tpl_vars['top_message']['no_close'] == ""): ?>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>
?<?php echo ((is_array($_tmp=$_SERVER['QUERY_STRING'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" class="close-link" onclick="javascript: document.getElementById('dialog-message').style.display = 'none'; return false;"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_close'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="close-img" /></a>
      <?php endif; ?>

      <?php echo ((is_array($_tmp=@$this->_tpl_vars['top_message']['content'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['alt_content']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['alt_content'])); ?>


      <?php if ($this->_tpl_vars['top_message']['anchor'] != ""): ?>
        <div class="anchor">
          <a href="#<?php echo $this->_tpl_vars['top_message']['anchor']; ?>
"><?php echo $this->_tpl_vars['lng']['lbl_go_to_last_edit_section']; ?>
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" /></a>
        </div>
      <?php endif; ?>

    </div>
  </div>

<script type="text/javascript">
//<![CDATA[
document.getElementById('dialog-message').style.display = 'none';
//]]>
</script>

<?php endif; ?>