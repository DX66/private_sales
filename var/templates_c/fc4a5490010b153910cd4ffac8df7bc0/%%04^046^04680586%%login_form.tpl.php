<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:57
         compiled from customer/main/login_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/login_form.tpl', 10, false),array('modifier', 'default', 'customer/main/login_form.tpl', 15, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/login_form.tpl","lbl_authentication,lbl_password,lbl_remember_me_q,msg_err_antibot,lbl_recover_password"); ?><form action="<?php echo $this->_tpl_vars['authform_url']; ?>
" method="post" name="authform">
  <input type="hidden" name="<?php echo $this->_tpl_vars['XCARTSESSNAME']; ?>
" value="<?php echo $this->_tpl_vars['XCARTSESSID']; ?>
" />
  <input type="hidden" name="is_remember" value="<?php echo $this->_tpl_vars['is_remember']; ?>
" />
  <input type="hidden" name="mode" value="login" />

  <table cellspacing="0" class="data-table" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_authentication'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
    <tr> 
      <td class="data-name"><label for="username"><?php echo $this->_tpl_vars['login_field_name']; ?>
</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="text" id="username" name="username"<?php if ($this->_tpl_vars['config']['email_as_login'] == 'Y'): ?> class="input-email"<?php endif; ?> size="30" value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_config[0]['vars']['default_login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['username']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['username'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>

    <tr> 
      <td class="data-name"><label for="password"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</label></td>
      <td class="data-required">*</td>
      <td><input type="password" id="password" name="password" size="30" maxlength="64" value="<?php echo $this->_config[0]['vars']['default_password']; ?>
" /></td>
    </tr>

    <?php if ($this->_tpl_vars['active_modules']['Remember_Me'] != "" && $this->_tpl_vars['auth_cookie_allowed'] == 'Y'): ?>

      <tr>
        <td colspan="2">&nbsp;</td>
        <td>
          <input type="checkbox" id="autologin" name="autologin" value="Y" /> 
          <label for="autologin"><?php echo $this->_tpl_vars['lng']['lbl_remember_me_q']; ?>
</label>
        </td>
      </tr>

    <?php endif; ?>

    <?php ob_start();
$_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/submit.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->assign('submit_button', ob_get_contents()); ob_end_clean();
 ?>

    <?php if (! $this->_tpl_vars['is_modal_popup'] && $this->_tpl_vars['active_modules']['Image_Verification'] && $this->_tpl_vars['show_antibot']['on_login'] == 'Y' && $this->_tpl_vars['login_antibot_on'] && $this->_tpl_vars['main'] != 'disabled'): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Image_Verification/spambot_arrest.tpl", 'smarty_include_vars' => array('mode' => "data-table",'id' => $this->_tpl_vars['antibot_sections']['on_login'],'button_code' => $this->_tpl_vars['submit_button'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

      <?php if ($this->_tpl_vars['antibot_err']): ?>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td class="error-message"><?php echo $this->_tpl_vars['lng']['msg_err_antibot']; ?>
</td>
        </tr>
      <?php endif; ?>

   <?php else: ?>

    <tr> 
      <td colspan="2">&nbsp;</td>
      <td class="button-row"><?php echo $this->_tpl_vars['submit_button']; ?>
</td>
    </tr>
    
    <?php endif; ?>
    
    <?php if (! $this->_tpl_vars['is_modal_popup']): ?>
      <tr>
        <td colspan="2">&nbsp;</td>
        <td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('href' => "help.php?section=Password_Recovery",'button_title' => $this->_tpl_vars['lng']['lbl_recover_password'],'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
      </tr>
    <?php endif; ?>

  </table>

</form>