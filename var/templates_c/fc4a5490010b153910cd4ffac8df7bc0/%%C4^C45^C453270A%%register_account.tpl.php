<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:36
         compiled from customer/main/register_account.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/main/register_account.tpl', 5, false),array('modifier', 'escape', 'customer/main/register_account.tpl', 23, false),array('modifier', 'substitute', 'customer/main/register_account.tpl', 32, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/register_account.tpl","lbl_account_information,lbl_email,txt_email_note,txt_opc_create_account,txt_anonymous_account_msg,lbl_username,lbl_password,txt_password_strength,lbl_confirm_password,lbl_password,lbl_chpass,lbl_account_status,lbl_account_status_suspended,lbl_account_status_enabled,lbl_account_status_not_approved,lbl_account_status_declined,lbl_account_activity,lbl_account_activity_enabled,lbl_account_activity_disabled,lbl_reg_chpass,lbl_trusted_providers"); ?><?php if ($this->_tpl_vars['config']['Security']['use_complex_pwd'] == 'Y' && ((is_array($_tmp=@$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['uname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['uname'])) == ''): ?>
  <?php $this->assign('show_passwd_note', 'Y'); ?>
<?php endif; ?>

  <?php if ($this->_tpl_vars['hide_header'] == ""): ?>
    <tr>
      <td colspan="3" class="register-section-title">
        <div>
          <label><?php echo $this->_tpl_vars['lng']['lbl_account_information']; ?>
</label>
        </div>
      </td>
    </tr>
  <?php endif; ?>

  <tr>
    <td class="data-name"><label for="email"><?php echo $this->_tpl_vars['lng']['lbl_email']; ?>
</label></td>
    <td class="data-required">*</td>
    <td>
      <input type="text" id="email" name="email" class="input-required input-email" size="32" maxlength="128" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      <div id="email_note" class="note-box" style="display: none;"><?php echo $this->_tpl_vars['lng']['txt_email_note']; ?>
</div>
    </td>
  </tr>

  <?php if ($this->_tpl_vars['anonymous'] && $this->_tpl_vars['config']['General']['enable_anonymous_checkout'] == 'Y'): ?>
    <tr>
      <td class="register-section-title register-exp-section<?php if (! ( $this->_tpl_vars['reg_error'] && $this->_tpl_vars['userinfo']['create_account'] )): ?> register-sec-minimized<?php endif; ?>" colspan="3">
        <div>
          <label class="pointer" for="create_account"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_opc_create_account'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'login_field', $this->_tpl_vars['login_field_name']) : smarty_modifier_substitute($_tmp, 'login_field', $this->_tpl_vars['login_field_name'])); ?>
</label>
          <input type="checkbox" id="create_account" name="create_account" value="Y"<?php if ($this->_tpl_vars['reg_error'] && $this->_tpl_vars['userinfo']['create_account']): ?> checked="checked"<?php endif; ?> />
        </div>
      </td>
    </tr>

    </tbody>
    <tbody id="create_account_box">

    <tr>
      <td colspan="3"><?php echo $this->_tpl_vars['lng']['txt_anonymous_account_msg']; ?>
</td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['id'] == $this->_tpl_vars['logged_userid'] && $this->_tpl_vars['logged_userid'] > 0 && $this->_tpl_vars['userinfo']['usertype'] != 'C'): ?>

      <tr style="display: none;">
        <td>
          <input type="hidden" name="membershipid" value="<?php echo $this->_tpl_vars['userinfo']['membershipid']; ?>
" />
          <input type="hidden" name="pending_membershipid" value="<?php echo $this->_tpl_vars['userinfo']['pending_membershipid']; ?>
" />
        </td>
      </tr>

  <?php else: ?>

    <?php if ($this->_tpl_vars['config']['General']['membership_signup'] == 'Y' && ( $this->_tpl_vars['usertype'] == 'C' || $this->_tpl_vars['is_admin_user'] || $this->_tpl_vars['usertype'] == 'B' ) && $this->_tpl_vars['membership_levels']): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/membership_signup.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>

  <?php endif; ?>

  <?php if ($this->_tpl_vars['config']['email_as_login'] != 'Y'): ?>
    <tr>
      <td class="data-name"><label for="uname"><?php echo $this->_tpl_vars['lng']['lbl_username']; ?>
</label></td>
      <?php if ($this->_tpl_vars['login'] != '' && $this->_tpl_vars['config']['General']['allow_change_login'] != 'Y'): ?>
        <td></td>
        <td>
          <b><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['uname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['uname'])); ?>
</b>
          <input type="hidden" name="uname" value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['uname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['uname'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      <?php else: ?>
        <td class="data-required">*</td>
        <td>
          <input type="text" id="uname" name="uname" class="input-required" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['userinfo']['uname']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['uname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>" />
      <?php endif; ?>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['allow_pwd_modify'] == 'Y'): ?>
    <tr style="display:none;"><td><input type="hidden" name="password_is_modified" id="password_is_modified" value="N" /></td></tr>
    <tr>
      <td class="data-name"><label for="passwd1"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="password" id="passwd1" name="passwd1" class="input-required" size="32" maxlength="64" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['passwd1'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        <?php if ($this->_tpl_vars['show_passwd_note'] == 'Y'): ?><div id="passwd_note" class="note-box" style="display: none;"><?php echo $this->_tpl_vars['lng']['txt_password_strength']; ?>
</div><?php endif; ?>
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="passwd2"><?php echo $this->_tpl_vars['lng']['lbl_confirm_password']; ?>
</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="password" id="passwd2" name="passwd2" class="input-required" size="32" maxlength="64" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['passwd2'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        <span class="validate-mark"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="15" height="15" alt="" /></span>
      </td>
    </tr>
    <?php else: ?>
    <tr>
      <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</td>
      <td></td>
      <td><a href="change_password.php"><?php echo $this->_tpl_vars['lng']['lbl_chpass']; ?>
</a></td>
    </tr>
  <?php endif; ?>
  
  <?php if ($this->_tpl_vars['anonymous'] && $this->_tpl_vars['config']['General']['enable_anonymous_checkout'] == 'Y'): ?>
    </tbody>
    <tbody>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['is_admin_user'] && $this->_tpl_vars['userinfo']['id'] != $this->_tpl_vars['logged_userid']): ?>

      <tr>
        <td class="data-name"><label for="status"><?php echo $this->_tpl_vars['lng']['lbl_account_status']; ?>
:</label></td>
        <td>&nbsp;</td>
        <td>

          <select name="status">
            <option value="N"<?php if ($this->_tpl_vars['userinfo']['status'] == 'N'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_suspended']; ?>
</option>
            <option value="Y"<?php if ($this->_tpl_vars['userinfo']['status'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_enabled']; ?>
</option>
            <?php if ($this->_tpl_vars['active_modules']['XAffiliate'] != "" && ( $this->_tpl_vars['userinfo']['usertype'] == 'B' || $_GET['usertype'] == 'B' )): ?>
              <option value="Q"<?php if ($this->_tpl_vars['userinfo']['status'] == 'Q'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_not_approved']; ?>
</option>
              <option value="D"<?php if ($this->_tpl_vars['userinfo']['status'] == 'D'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_declined']; ?>
</option>
            <?php endif; ?>
          </select>
        </td>
      </tr>

    <?php if ($this->_tpl_vars['display_activity_box'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="activity"><?php echo $this->_tpl_vars['lng']['lbl_account_activity']; ?>
:</label></td>
        <td>&nbsp;</td>
        <td>

          <select name="activity">
            <option value="Y"<?php if ($this->_tpl_vars['userinfo']['activity'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_activity_enabled']; ?>
</option>
            <option value="N"<?php if ($this->_tpl_vars['userinfo']['activity'] == 'N'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_activity_disabled']; ?>
</option>
          </select>

        </td>
      </tr>
    <?php endif; ?>

      <tr>
        <td colspan="2">&nbsp;</td>
        <td>

          <label>
            <input type="checkbox" id="change_password" name="change_password" value="Y"<?php if ($this->_tpl_vars['userinfo']['change_password'] == 'Y'): ?> checked="checked"<?php endif; ?> />
            <?php echo $this->_tpl_vars['lng']['lbl_reg_chpass']; ?>

          </label>

        </td>
      </tr>

  <?php if (( $this->_tpl_vars['userinfo']['usertype'] == 'P' || $_GET['usertype'] == 'P' ) && $this->_tpl_vars['usertype'] == 'A' && $this->_tpl_vars['active_modules']['Simple_Mode'] == ""): ?>
      <tr>
        <td colspan="2">&nbsp;</td>
        <td>

          <label>
            <input type="checkbox" id="trusted_provider" name="trusted_provider" value="Y"<?php if ($this->_tpl_vars['userinfo']['trusted_provider'] == 'Y'): ?> checked="checked"<?php endif; ?> />
            <?php echo $this->_tpl_vars['lng']['lbl_trusted_providers']; ?>

          </label>

        </td>
      </tr>
  <?php endif; ?>

<?php endif; ?>