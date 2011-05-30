<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from dialog_tools.tpl */ ?>
<?php func_load_lang($this, "dialog_tools.tpl","lbl_go_to_last_edit_section,lbl_in_this_section,lbl_see_also"); ?><?php if ($this->_tpl_vars['dialog_tools_data']): ?>

<?php $this->assign('left', $this->_tpl_vars['dialog_tools_data']['left']); ?>
<?php $this->assign('right', $this->_tpl_vars['dialog_tools_data']['right']); ?>

<?php endif; ?>

<table cellpadding="0" cellspacing="0" width="100%" class="dialog-tools-table">

<tr>
  <td height="40" valign="top">

<?php if (( $this->_tpl_vars['top_message']['type'] == "" || $this->_tpl_vars['top_message']['type'] == 'I' ) && $this->_tpl_vars['newid'] == "" && $this->_tpl_vars['top_message']['content'] != ""): ?>
  <div class="top-message-info hidden ui-corner-all" onclick="javascript: $(this).hide();">

    <?php if ($this->_tpl_vars['top_message']['content']): ?>
      <?php echo $this->_tpl_vars['top_message']['content']; ?>

      <?php if ($this->_tpl_vars['top_message']['anchor'] != ""): ?>
        <div class="anchor">
          <a href="#<?php echo $this->_tpl_vars['top_message']['anchor']; ?>
"><?php echo $this->_tpl_vars['lng']['lbl_go_to_last_edit_section']; ?>
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" /></a>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php $this->assign('top_message', ""); ?>
  </div>
<?php endif; ?>

  </td>
</tr>

<?php if ($this->_tpl_vars['dialog_tools_data']): ?>
<tr>
  <td>

  <div class="dialog-tools">

      <ul class="dialog-tools-header">
<?php if ($this->_tpl_vars['left']): ?>
        <li class="dialog-header-left<?php if ($this->_tpl_vars['dialog_tools_data']['show'] == 'right'): ?> dialog-tools-nonactive<?php endif; ?>" onclick="javascript: dialog_tools_activate('left', 'right');">
        <?php if ($this->_tpl_vars['left']['title']): ?><?php echo $this->_tpl_vars['left']['title']; ?>
<?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_in_this_section']; ?>
<?php endif; ?>
        </li>
<?php endif; ?>
<?php if ($this->_tpl_vars['right']): ?>
        <li class="dialog-header-right<?php if ($this->_tpl_vars['left'] && $this->_tpl_vars['dialog_tools_data']['show'] != 'right'): ?> dialog-tools-nonactive<?php endif; ?>" onclick="javascript: dialog_tools_activate('right', 'left');">
        <?php if ($this->_tpl_vars['right']['title']): ?><?php echo $this->_tpl_vars['right']['title']; ?>
<?php else: ?><?php echo $this->_tpl_vars['lng']['lbl_see_also']; ?>
<?php endif; ?>
        </li>
<?php endif; ?>
      </ul>

    <div class="clearing">&nbsp;</div>

    <div class="dialog-tools-box">

<?php if ($this->_tpl_vars['left']): ?>
<?php if ($this->_tpl_vars['left']['data']): ?>
<?php $this->assign('left', $this->_tpl_vars['left']['data']); ?>
<?php endif; ?>
      <ul class="dialog-tools-content dialog-tools-left<?php if ($this->_tpl_vars['dialog_tools_data']['show'] == 'right'): ?> hidden<?php endif; ?>">
<?php $_from = $this->_tpl_vars['left']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cell']):
?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_tools_cell.tpl", 'smarty_include_vars' => array('cell' => $this->_tpl_vars['cell'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endforeach; endif; unset($_from); ?>
      </ul>
<?php endif; ?>

<?php if ($this->_tpl_vars['right']): ?>
<?php if ($this->_tpl_vars['right']['data']): ?>
<?php $this->assign('right', $this->_tpl_vars['right']['data']); ?>
<?php endif; ?>
      <ul class="dialog-tools-content dialog-tools-right<?php if ($this->_tpl_vars['left'] && $this->_tpl_vars['dialog_tools_data']['show'] != 'right'): ?> hidden<?php endif; ?>">
<?php $_from = $this->_tpl_vars['right']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cell']):
?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_tools_cell.tpl", 'smarty_include_vars' => array('cell' => $this->_tpl_vars['cell'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endforeach; endif; unset($_from); ?>
      </ul>
<?php endif; ?>

    </div>

  </div>

  </td>
</tr>
<?php endif; ?>

</table>