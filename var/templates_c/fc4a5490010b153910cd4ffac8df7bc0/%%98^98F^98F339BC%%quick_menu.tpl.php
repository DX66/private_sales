<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from main/quick_menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'wm_remove', 'main/quick_menu.tpl', 21, false),array('modifier', 'escape', 'main/quick_menu.tpl', 21, false),)), $this); ?>
<?php func_load_lang($this, "main/quick_menu.tpl","txt_quick_menu_text,lbl_select_target,lbl_quick_menu"); ?><?php if ($this->_tpl_vars['quick_menu']): ?>

<a name="menu"></a>
<?php ob_start(); ?>

<?php echo $this->_tpl_vars['lng']['txt_quick_menu_text']; ?>


<br /><br />

<table cellpadding="5" cellspacing="1">

<?php $_from = $this->_tpl_vars['quick_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group'] => $this->_tpl_vars['items']):
?>
<tr>
  <td class="FormButton"><?php echo $this->_tpl_vars['group']; ?>
:</td>
  <td>
  <select style='width: 200px;' onchange="javascript: if (this.selectedIndex != 0) self.location=this.value;">
    <option value="">[<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_select_target'])) ? $this->_run_mod_handler('wm_remove', true, $_tmp) : smarty_modifier_wm_remove($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
]</option>
<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['items']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['id']['show'] = true;
$this->_sections['id']['max'] = $this->_sections['id']['loop'];
$this->_sections['id']['step'] = 1;
$this->_sections['id']['start'] = $this->_sections['id']['step'] > 0 ? 0 : $this->_sections['id']['loop']-1;
if ($this->_sections['id']['show']) {
    $this->_sections['id']['total'] = $this->_sections['id']['loop'];
    if ($this->_sections['id']['total'] == 0)
        $this->_sections['id']['show'] = false;
} else
    $this->_sections['id']['total'] = 0;
if ($this->_sections['id']['show']):

            for ($this->_sections['id']['index'] = $this->_sections['id']['start'], $this->_sections['id']['iteration'] = 1;
                 $this->_sections['id']['iteration'] <= $this->_sections['id']['total'];
                 $this->_sections['id']['index'] += $this->_sections['id']['step'], $this->_sections['id']['iteration']++):
$this->_sections['id']['rownum'] = $this->_sections['id']['iteration'];
$this->_sections['id']['index_prev'] = $this->_sections['id']['index'] - $this->_sections['id']['step'];
$this->_sections['id']['index_next'] = $this->_sections['id']['index'] + $this->_sections['id']['step'];
$this->_sections['id']['first']      = ($this->_sections['id']['iteration'] == 1);
$this->_sections['id']['last']       = ($this->_sections['id']['iteration'] == $this->_sections['id']['total']);
?>
<?php if ($this->_tpl_vars['items'][$this->_sections['id']['index']]['title'] == ""): ?>
    <option value="">------------------------------------</option>
<?php else: ?>
    <option value="<?php echo $this->_tpl_vars['items'][$this->_sections['id']['index']]['link']; ?>
"><?php echo $this->_tpl_vars['items'][$this->_sections['id']['index']]['title']; ?>
</option>
<?php endif; ?>
<?php endfor; endif; ?>
  </select>
  </td>
</tr>
<?php endforeach; endif; unset($_from); ?>

</table>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_quick_menu'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br /><br />

<?php endif; ?>
