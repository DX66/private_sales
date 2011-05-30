<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from main/tooltip_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/tooltip_js.tpl', 7, false),array('modifier', 'escape', 'main/tooltip_js.tpl', 8, false),array('modifier', 'wm_remove', 'main/tooltip_js.tpl', 39, false),array('function', 'load_defer', 'main/tooltip_js.tpl', 45, false),)), $this); ?>
<?php func_load_lang($this, "main/tooltip_js.tpl","lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_need_help,lbl_close"); ?><?php if ($this->_tpl_vars['type'] == 'label'): ?>

  <label for="<?php echo ((is_array($_tmp=@$this->_tpl_vars['idfor'])) ? $this->_run_mod_handler('default', true, $_tmp, 'for_tooltip_link') : smarty_modifier_default($_tmp, 'for_tooltip_link')); ?>
">
    <a class="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['class'])) ? $this->_run_mod_handler('default', true, $_tmp, 'NeedHelpLink') : smarty_modifier_default($_tmp, 'NeedHelpLink')))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['show_title']): ?> title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_need_help']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_need_help'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?> id="<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
" rel="#<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
_tooltip"><?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_need_help']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_need_help'])); ?>
</a>
  </label>

<?php elseif ($this->_tpl_vars['type'] == 'img'): ?>

  <a href="javascript:void(0);" class="" id="<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
"<?php if ($this->_tpl_vars['show_title']): ?> title="<?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?> rel="#<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
_tooltip">
    <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/<?php echo ((is_array($_tmp=@$this->_tpl_vars['alt_image'])) ? $this->_run_mod_handler('default', true, $_tmp, "help_sign.gif") : smarty_modifier_default($_tmp, "help_sign.gif")); ?>
" width="15" height="15" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_need_help']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_need_help'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
  </a>

<?php else: ?>

  <a class="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['class'])) ? $this->_run_mod_handler('default', true, $_tmp, 'NeedHelpLink') : smarty_modifier_default($_tmp, 'NeedHelpLink')))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['show_title']): ?> title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_need_help']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_need_help'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?> id="<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
" href="#<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
_tooltip" rel="#<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
_tooltip"><?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_need_help']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_need_help'])); ?>
</a>

<?php endif; ?>

<<?php echo ((is_array($_tmp=@$this->_tpl_vars['wrapper_tag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'span') : smarty_modifier_default($_tmp, 'span')); ?>
 id="<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
_tooltip" style="display:none;">
  <?php echo $this->_tpl_vars['text']; ?>

</<?php echo ((is_array($_tmp=@$this->_tpl_vars['wrapper_tag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'span') : smarty_modifier_default($_tmp, 'span')); ?>
>

<?php ob_start(); ?>
$(document).ready(function(){
  $('#<?php echo ((is_array($_tmp=@$this->_tpl_vars['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 'tooltip_link') : smarty_modifier_default($_tmp, 'tooltip_link')); ?>
').cluetip({
    local:true, 
    hideLocal: false,
    showTitle: <?php if ($this->_tpl_vars['show_title']): ?>true<?php else: ?>false<?php endif; ?>,
    cluezIndex: <?php echo ((is_array($_tmp=@$this->_tpl_vars['cz_index'])) ? $this->_run_mod_handler('default', true, $_tmp, 1100) : smarty_modifier_default($_tmp, 1100)); ?>
,
    <?php if ($this->_tpl_vars['width'] > 0): ?>width: <?php echo $this->_tpl_vars['width']; ?>
, <?php endif; ?>
    <?php if ($this->_tpl_vars['sticky']): ?>
      sticky: true,
      mouseOutClose: true,
      closePosition: 'bottom',
      closeText: '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_close'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
',
    <?php endif; ?>
    clueTipClass: '<?php echo ((is_array($_tmp=@$this->_tpl_vars['extra_class'])) ? $this->_run_mod_handler('default', true, $_tmp, 'default') : smarty_modifier_default($_tmp, 'default')); ?>
'
  });
});
<?php $this->_smarty_vars['capture']['tooltip'] = ob_get_contents();  $this->assign('tt', ob_get_contents());ob_end_clean(); ?>
<?php echo smarty_function_load_defer(array('file' => 'tooltip','direct_info' => $this->_tpl_vars['tt'],'type' => 'js'), $this);?>
