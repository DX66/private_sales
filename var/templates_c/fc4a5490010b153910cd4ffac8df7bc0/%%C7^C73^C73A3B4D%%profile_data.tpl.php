<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/html/profile_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'mail/html/profile_data.tpl', 47, false),)), $this); ?>
<?php func_load_lang($this, "mail/html/profile_data.tpl","lbl_account_information,lbl_username,lbl_email,lbl_password,lbl_personal_information,lbl_title,lbl_first_name,lbl_last_name,lbl_company,lbl_ssn,lbl_tax_number,lbl_membership,lbl_signup_for_membership,lbl_additional_information"); ?><hr noshade="noshade" size="1" color="#CCCCCC" width="70%" align="left" />

<table cellpadding="0" cellspacing="0" width="100%">

  <tr>
    <td colspan="4"><b><?php echo $this->_tpl_vars['lng']['lbl_account_information']; ?>
</b></td>
  </tr>

  <?php if ($this->_tpl_vars['config']['email_as_login'] != 'Y'): ?>
    <tr>
      <td width="25">&nbsp;&nbsp;&nbsp;</td>
      <td width="20%"><tt><?php echo $this->_tpl_vars['lng']['lbl_username']; ?>
:</tt></td>
      <td width="10">&nbsp;</td>
      <td width="80%"><tt><?php echo $this->_tpl_vars['userinfo']['login']; ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <tr>
    <td>&nbsp;&nbsp;&nbsp;</td>
    <td><tt><?php echo $this->_tpl_vars['lng']['lbl_email']; ?>
:</tt></td>
    <td>&nbsp;</td>
    <td><tt><?php echo $this->_tpl_vars['userinfo']['email']; ?>
</tt></td>
  </tr>
 
  <?php if ($this->_tpl_vars['show_pwd']): ?>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
:</tt></td>
      <td>&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['userinfo']['password']; ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <tr>
    <td colspan="4"><b><?php echo $this->_tpl_vars['lng']['lbl_personal_information']; ?>
</b></td>
  </tr>

  <?php if ($this->_tpl_vars['userinfo']['default_fields']['title']): ?>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_title']; ?>
:</tt></td>
      <td>&nbsp;</td>
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['default_fields']['firstname']): ?>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_first_name']; ?>
:</tt></td>
      <td>&nbsp;</td>
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['firstname'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['default_fields']['lastname']): ?>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_last_name']; ?>
:</tt></td>
      <td>&nbsp;</td>
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['lastname'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['default_fields']['company']): ?>
    <tr> 
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_company']; ?>
:</tt></td>
      <td>&nbsp;</td> 
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['company'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['default_fields']['ssn']): ?>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_ssn']; ?>
:</tt></td>
      <td>&nbsp;</td>
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['ssn'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['default_fields']['tax_number']): ?>
    <tr> 
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_tax_number']; ?>
:</tt></td>
      <td>&nbsp;</td> 
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['tax_number'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['membership']): ?>
    <tr> 
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_membership']; ?>
:</tt></td>
      <td>&nbsp;</td> 
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['membership'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['userinfo']['pending_membership'] != $this->_tpl_vars['userinfo']['membership']): ?>
    <tr> 
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><tt><?php echo $this->_tpl_vars['lng']['lbl_signup_for_membership']; ?>
:</tt></td>
      <td>&nbsp;</td> 
      <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['pending_membership'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
    </tr>
  <?php endif; ?>
  
  <?php $_from = $this->_tpl_vars['userinfo']['additional_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
    <?php if ($this->_tpl_vars['v']['section'] == 'P'): ?>
      <tr>
        <td>&nbsp;&nbsp;&nbsp;</td>
        <td><tt><?php echo $this->_tpl_vars['v']['title']; ?>
:</tt></td>
        <td>&nbsp;</td>
        <td><tt><?php echo ((is_array($_tmp=@$this->_tpl_vars['v']['value'])) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</tt></td>
      </tr>
    <?php endif; ?>
  <?php endforeach; endif; unset($_from); ?>

  <?php if ($this->_tpl_vars['userinfo']['field_sections']['A']): ?>
    <tr>
      <td colspan="4"><b><?php echo $this->_tpl_vars['lng']['lbl_additional_information']; ?>
</b></td>
    </tr>

    <?php $_from = $this->_tpl_vars['userinfo']['additional_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
      <?php if ($this->_tpl_vars['v']['section'] == 'A' || $this->_tpl_vars['v']['section'] == 'C'): ?>
        <tr>
          <td>&nbsp;&nbsp;&nbsp;</td>
          <td><tt><?php echo $this->_tpl_vars['v']['title']; ?>
:</tt></td>
          <td>&nbsp;</td>
          <td><tt><?php echo $this->_tpl_vars['v']['value']; ?>
</tt></td>
        </tr>
      <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
  <?php endif; ?>

</table>