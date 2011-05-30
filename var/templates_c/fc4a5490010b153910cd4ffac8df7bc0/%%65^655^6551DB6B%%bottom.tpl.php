<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:11
         compiled from bottom.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'bottom.tpl', 20, false),)), $this); ?>
<?php func_load_lang($this, "bottom.tpl","lbl_language"); ?><table width="100%" cellpadding="0" cellspacing="0">

<?php if ($this->_tpl_vars['active_modules']['Users_online'] != "" || $this->_tpl_vars['login'] && $this->_tpl_vars['all_languages_cnt'] > 1): ?>
<tr>
  <td>
  <table width="100%">
    <tr>
<?php if ($this->_tpl_vars['active_modules']['Users_online'] != ""): ?>
      <td class="users-online-box">
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Users_online/menu_users_online.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </td>
<?php endif; ?>

<?php if ($this->_tpl_vars['login'] && $this->_tpl_vars['all_languages_cnt'] > 1): ?>
      <td class="admin-language">
        <form action="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" method="post" name="asl_form">
          <input type="hidden" name="redirect" value="<?php echo ((is_array($_tmp=$_SERVER['QUERY_STRING'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" />
          <?php echo $this->_tpl_vars['lng']['lbl_language']; ?>
:
          <select name="asl" onchange="javascript: document.asl_form.submit()">
          <?php $_from = $this->_tpl_vars['all_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['l']):
?>
          <option value="<?php echo $this->_tpl_vars['l']['code']; ?>
"<?php if ($this->_tpl_vars['current_language'] == $this->_tpl_vars['l']['code']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['l']['language']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
          </select>
        </form>
      </td>
<?php endif; ?>
      </tr>
    </table>
  </td>
</tr>
<?php endif; ?>

<tr>
  <td class="HeadThinLine">
    <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" />
  </td>
</tr>

<tr>
  <td class="BottomBox">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="Bottom" align="left">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/prnotice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </td>
        <td class="Bottom" align="right">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "copyright.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </td>
      </tr>
    </table>
  </td>
</tr>

</table>