<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:36
         compiled from customer/main/register_personal_info.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/register_personal_info.tpl', 32, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/register_personal_info.tpl","lbl_personal_information,lbl_title,lbl_first_name,lbl_last_name,lbl_company,lbl_web_site,lbl_ssn,lbl_tax_number,lbl_tax_exemption,txt_tax_exemption_assigned,lbl_referred_by,lbl_unknown"); ?><?php if ($this->_tpl_vars['is_areas']['P'] == 'Y'): ?>

<?php if ($this->_tpl_vars['hide_header'] == ""): ?>
      <tr>
        <td colspan="3" class="register-section-title">
          <div>
            <label><?php echo $this->_tpl_vars['lng']['lbl_personal_information']; ?>
</label>
          </div>
        </td>
      </tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['title']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="title"><?php echo $this->_tpl_vars['lng']['lbl_title']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['title']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/title_selector.tpl", 'smarty_include_vars' => array('val' => $this->_tpl_vars['userinfo']['titleid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </td>
      </tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['firstname']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="firstname"><?php echo $this->_tpl_vars['lng']['lbl_first_name']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['firstname']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
          <input type="text" id="firstname" name="firstname" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['firstname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        </td>
      </tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['lastname']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="lastname"><?php echo $this->_tpl_vars['lng']['lbl_last_name']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['lastname']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
          <input type="text" id="lastname" name="lastname" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['lastname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        </td>
      </tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['company']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="company"><?php echo $this->_tpl_vars['lng']['lbl_company']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['company']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
          <input type="text" id="company" name="company" size="32" maxlength="255" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['company'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        </td>
      </tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['url']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="url"><?php echo $this->_tpl_vars['lng']['lbl_web_site']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['url']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
          <input type="text" id="url" name="url" size="32" maxlength="128" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['url'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        </td>
      </tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['ssn']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="ssn"><?php echo $this->_tpl_vars['lng']['lbl_ssn']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['ssn']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
          <input type="text" id="ssn" name="ssn" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['ssn'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
        </td>
      </tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['tax_number']['avail'] == 'Y'): ?>
      <tr>
        <td class="data-name"><label for="tax_number"><?php echo $this->_tpl_vars['lng']['lbl_tax_number']; ?>
</label></td>
        <td<?php if ($this->_tpl_vars['default_fields']['tax_number']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
        <td>
<?php if ($this->_tpl_vars['userinfo']['tax_exempt'] != 'Y' || $this->_tpl_vars['config']['Taxes']['allow_user_modify_tax_number'] == 'Y' || $this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P'): ?>
          <input type="text" id="tax_number" name="tax_number" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['userinfo']['tax_number'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
<?php else: ?>
<?php echo $this->_tpl_vars['userinfo']['tax_number']; ?>

<?php endif; ?>
        </td>
      </tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['config']['Taxes']['enable_user_tax_exemption'] == 'Y'): ?>
<?php if (( ( $this->_tpl_vars['userinfo']['usertype'] == 'C' || $_GET['usertype'] == 'C' ) && $this->_tpl_vars['userinfo']['tax_exempt'] == 'Y' ) || ( $this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P' )): ?>
      <tr>
        <td class="data-name"><label for="tax_exempt"><?php echo $this->_tpl_vars['lng']['lbl_tax_exemption']; ?>
</label></td>
        <td>&nbsp;</td>
        <td>
<?php if ($this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P'): ?> 
          <input type="checkbox" id="tax_exempt" name="tax_exempt" value="Y"<?php if ($this->_tpl_vars['userinfo']['tax_exempt'] == 'Y'): ?> checked="checked"<?php endif; ?> />
<?php elseif ($this->_tpl_vars['userinfo']['tax_exempt'] == 'Y'): ?>
<?php echo $this->_tpl_vars['lng']['txt_tax_exemption_assigned']; ?>

<?php endif; ?>
        </td>
      </tr>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P'): ?>
      <tr>
        <td class="data-name"><label for="referer"><?php echo $this->_tpl_vars['lng']['lbl_referred_by']; ?>
</label></td>
        <td>&nbsp;</td>
        <td>
<?php if ($this->_tpl_vars['userinfo']['referer']): ?>
          <a href="<?php echo $this->_tpl_vars['userinfo']['referer']; ?>
"><?php echo $this->_tpl_vars['userinfo']['referer']; ?>
</a>
<?php else: ?>
<?php echo $this->_tpl_vars['lng']['lbl_unknown']; ?>

<?php endif; ?>
        </td>
      </tr>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'P')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>