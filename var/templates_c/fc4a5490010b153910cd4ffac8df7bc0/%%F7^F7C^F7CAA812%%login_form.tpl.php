<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:39
         compiled from main/login_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/login_form.tpl', 19, false),array('modifier', 'escape', 'main/login_form.tpl', 19, false),)), $this); ?>
<?php func_load_lang($this, "main/login_form.tpl","lbl_password,lbl_remember_me_q,msg_err_antibot,lbl_submit,lbl_recover_password,lbl_register"); ?><form action="<?php echo $this->_tpl_vars['authform_url']; ?>
" method="post" name="loginform">
<input type="hidden" name="<?php echo $this->_tpl_vars['XCARTSESSNAME']; ?>
" value="<?php echo $this->_tpl_vars['XCARTSESSID']; ?>
" />
<input type="hidden" name="is_remember" value="<?php echo $this->_tpl_vars['is_remember']; ?>
" />
<input type="hidden" name="mode" value="login" />

<table class="login-table">
<tr>
  <td colspan="3" class="login-title"><?php echo $this->_tpl_vars['login_title']; ?>
</td>
</tr>

<tr> 
  <td class="data-name"><label for="username"><?php echo $this->_tpl_vars['login_field_name']; ?>
</label></td>
  <td class="data-required">*</td>
  <td>
    <input type="text" name="username"<?php if ($this->_tpl_vars['config']['email_as_login'] == 'Y'): ?> class="input-email"<?php endif; ?> id="username" size="30" value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_config[0]['vars']['default_login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['username']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['username'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
  </td>
</tr>

<tr>
  <td class="data-name"><label for="password"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</label></td>
  <td class="data-required">*</td>
  <td><input type="password" name="password" id="password" size="30" maxlength="64" value="<?php echo $this->_config[0]['vars']['default_password']; ?>
" /></td>
</tr>

<?php if ($this->_tpl_vars['active_modules']['Remember_Me'] != "" && $this->_tpl_vars['auth_cookie_allowed'] == 'Y'): ?>

  <tr>
    <td colspan="2">&nbsp;</td>
    <td>
      <input type="checkbox" name="autologin" id="autologin" value="Y" /> 
      <label for="autologin"><?php echo $this->_tpl_vars['lng']['lbl_remember_me_q']; ?>
</label>
    </td>
  </tr>

<?php endif; ?> 

<?php if ($this->_tpl_vars['active_modules']['Image_Verification'] && $this->_tpl_vars['show_antibot']['on_login'] == 'Y' && $this->_tpl_vars['login_antibot_on'] && $this->_tpl_vars['main'] != 'disabled'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Image_Verification/spambot_arrest.tpl", 'smarty_include_vars' => array('mode' => "data-table",'id' => $this->_tpl_vars['antibot_sections']['on_login'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<tr>
  <td colspan="3" class="ErrorMessage"><?php if ($this->_tpl_vars['antibot_err']): ?><?php echo $this->_tpl_vars['lng']['msg_err_antibot']; ?>
<?php endif; ?></td>
</tr>
<?php endif; ?>

<tr> 
  <td colspan="2">&nbsp;</td>
  <td>
    <table width="100%">
      <tr>
        <td class="main-button">
          <button class="big-main-button" type="submit"><?php echo $this->_tpl_vars['lng']['lbl_submit']; ?>
</button>
        </td>
        <td><a href="help.php?section=Password_Recovery"><?php echo $this->_tpl_vars['lng']['lbl_recover_password']; ?>
</a></td>
      </tr>
    </table>
  </td>
</tr>
<?php if ($this->_tpl_vars['is_register'] == 'Y'): ?>

<tr class="register-row">
  <td colspan="2">&nbsp;</td>
  <td>
    <a href="register.php"><?php echo $this->_tpl_vars['lng']['lbl_register']; ?>
</a>
  </td>
</tr>
<?php endif; ?>
</table>

</form>
