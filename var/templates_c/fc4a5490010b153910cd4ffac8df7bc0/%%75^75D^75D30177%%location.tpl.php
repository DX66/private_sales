<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from location.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'location.tpl', 11, false),array('modifier', 'default', 'location.tpl', 35, false),array('modifier', 'escape', 'location.tpl', 39, false),)), $this); ?>
<?php func_load_lang($this, "location.tpl","txt_noscript_warning,lbl_go_to_last_edit_section,lbl_close"); ?><?php if ($this->_tpl_vars['location'] != ""): ?>

<div id="location">
<?php echo ''; ?><?php unset($this->_sections['position']);
$this->_sections['position']['name'] = 'position';
$this->_sections['position']['loop'] = is_array($_loop=$this->_tpl_vars['location']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['position']['show'] = true;
$this->_sections['position']['max'] = $this->_sections['position']['loop'];
$this->_sections['position']['step'] = 1;
$this->_sections['position']['start'] = $this->_sections['position']['step'] > 0 ? 0 : $this->_sections['position']['loop']-1;
if ($this->_sections['position']['show']) {
    $this->_sections['position']['total'] = $this->_sections['position']['loop'];
    if ($this->_sections['position']['total'] == 0)
        $this->_sections['position']['show'] = false;
} else
    $this->_sections['position']['total'] = 0;
if ($this->_sections['position']['show']):

            for ($this->_sections['position']['index'] = $this->_sections['position']['start'], $this->_sections['position']['iteration'] = 1;
                 $this->_sections['position']['iteration'] <= $this->_sections['position']['total'];
                 $this->_sections['position']['index'] += $this->_sections['position']['step'], $this->_sections['position']['iteration']++):
$this->_sections['position']['rownum'] = $this->_sections['position']['iteration'];
$this->_sections['position']['index_prev'] = $this->_sections['position']['index'] - $this->_sections['position']['step'];
$this->_sections['position']['index_next'] = $this->_sections['position']['index'] + $this->_sections['position']['step'];
$this->_sections['position']['first']      = ($this->_sections['position']['iteration'] == 1);
$this->_sections['position']['last']       = ($this->_sections['position']['iteration'] == $this->_sections['position']['total']);
?><?php echo ''; ?><?php if ($this->_tpl_vars['location'][$this->_sections['position']['index']]['1'] != ""): ?><?php echo '<a href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['location'][$this->_sections['position']['index']]['1'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '">'; ?><?php endif; ?><?php echo ''; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['location'][$this->_sections['position']['index']]['0'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo ''; ?><?php if ($this->_tpl_vars['location'][$this->_sections['position']['index']]['1'] != ""): ?><?php echo '</a>'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_sections['position']['last']): ?><?php echo '&nbsp;'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['config']['Appearance']['breadcrumbs_separator'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '&nbsp;'; ?><?php endif; ?><?php echo ''; ?><?php endfor; endif; ?><?php echo ''; ?>

</div>

<?php endif; ?>

<!-- check javascript availability -->
<noscript>
  <table width="500" cellpadding="2" cellspacing="0" align="center">
  <tr>
    <td align="center" class="ErrorMessage"><?php echo $this->_tpl_vars['lng']['txt_noscript_warning']; ?>
</td>
  </tr>
  </table>
</noscript>

<table id="<?php echo ((is_array($_tmp=@$this->_tpl_vars['newid'])) ? $this->_run_mod_handler('default', true, $_tmp, "dialog-message") : smarty_modifier_default($_tmp, "dialog-message")); ?>
" width="100%" <?php if (( $this->_tpl_vars['alt_content'] == "" && $this->_tpl_vars['alt_type'] == "" ) && ( $this->_tpl_vars['top_message']['content'] == "" || ( $this->_tpl_vars['top_message']['type'] == 'I' || $this->_tpl_vars['top_message']['type'] == "" ) )): ?> style="display: none;"<?php endif; ?>>
<tr>
  <td>
    <div class="dialog-message">
      <div class="box message-<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['top_message']['type'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['alt_type']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['alt_type'])))) ? $this->_run_mod_handler('default', true, $_tmp, 'I') : smarty_modifier_default($_tmp, 'I')); ?>
"<?php if ($this->_tpl_vars['top_message']['title']): ?> title="<?php echo ((is_array($_tmp=$this->_tpl_vars['top_message']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>

        <table width="100%">
        <tr>
<?php if ($this->_tpl_vars['image_none'] != 'Y'): ?>
          <td width="50" valign="top">
            <img class="dialog-img" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" />
          </td>
<?php endif; ?>
          <td align="left" valign="top">
            <?php echo ((is_array($_tmp=@$this->_tpl_vars['top_message']['content'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['alt_content']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['alt_content'])); ?>

<?php if ($this->_tpl_vars['top_message']['anchor'] != ""): ?>
            <div class="anchor">
              <a href="#<?php echo $this->_tpl_vars['top_message']['anchor']; ?>
">
                <?php echo $this->_tpl_vars['lng']['lbl_go_to_last_edit_section']; ?>

                <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" />
              </a>
            </div>
<?php endif; ?>
          </td>
        </tr>
        </table>

<?php if ($this->_tpl_vars['top_message']['no_close'] == ""): ?>
        <a href="javascript:void(0);" class="close-link" onclick="javascript: $('#<?php echo ((is_array($_tmp=@$this->_tpl_vars['newid'])) ? $this->_run_mod_handler('default', true, $_tmp, "dialog-message") : smarty_modifier_default($_tmp, "dialog-message")); ?>
').hide(); return false;">
          <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_close'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="close-img" />
        </a>
<?php endif; ?>
      </div>
    </div>
  </td>
</tr>
</table>