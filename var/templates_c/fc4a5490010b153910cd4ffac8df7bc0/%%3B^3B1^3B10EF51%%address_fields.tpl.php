<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:26
         compiled from customer/main/address_fields.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/main/address_fields.tpl', 20, false),array('modifier', 'default', 'customer/main/address_fields.tpl', 60, false),array('modifier', 'amp', 'customer/main/address_fields.tpl', 92, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/address_fields.tpl","lbl_title,lbl_first_name,lbl_last_name,lbl_address,lbl_address_2,lbl_city,lbl_county,lbl_state,lbl_country,lbl_zip_code,lbl_phone,lbl_fax"); ?>  <?php if ($this->_tpl_vars['default_fields']['title']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
title"><?php echo $this->_tpl_vars['lng']['lbl_title']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['title']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/title_selector.tpl", 'smarty_include_vars' => array('val' => $this->_tpl_vars['address']['titleid'],'id' => ($this->_tpl_vars['id_prefix'])."title",'name' => ($this->_tpl_vars['name_prefix'])."[title]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['firstname']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
firstname"><?php echo $this->_tpl_vars['lng']['lbl_first_name']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['firstname']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
firstname" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[firstname]" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address']['firstname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['lastname']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
lastname"><?php echo $this->_tpl_vars['lng']['lbl_last_name']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['lastname']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
lastname" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[lastname]" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address']['lastname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['address']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
address"><?php echo $this->_tpl_vars['lng']['lbl_address']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['address']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
address" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[address]" size="32" maxlength="64" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address']['address'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['address_2']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
address_2"><?php echo $this->_tpl_vars['lng']['lbl_address_2']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['address_2']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
address_2" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[address_2]" size="32" maxlength="64" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address']['address_2'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['city']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
city"><?php echo $this->_tpl_vars['lng']['lbl_city']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['city']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
city" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[city]" size="32" maxlength="64" value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['address']['city'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_city']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_city'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['county']['avail'] == 'Y' && $this->_tpl_vars['config']['General']['use_counties'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
county"><?php echo $this->_tpl_vars['lng']['lbl_county']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['county']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/counties.tpl", 'smarty_include_vars' => array('counties' => $this->_tpl_vars['counties'],'name' => ($this->_tpl_vars['name_prefix'])."[county]",'id' => ($this->_tpl_vars['id_prefix'])."county",'default' => $this->_tpl_vars['address']['county'],'country_name' => ($this->_tpl_vars['id_prefix'])."country")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['state']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
state"><?php echo $this->_tpl_vars['lng']['lbl_state']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['state']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/states.tpl", 'smarty_include_vars' => array('states' => $this->_tpl_vars['states'],'name' => ($this->_tpl_vars['name_prefix'])."[state]",'default' => ((is_array($_tmp=@$this->_tpl_vars['address']['state'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_state']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_state'])),'default_country' => ((is_array($_tmp=@$this->_tpl_vars['address']['country'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_country']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_country'])),'id' => ($this->_tpl_vars['id_prefix'])."state",'country_name' => ($this->_tpl_vars['id_prefix'])."country")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['country']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
country"><?php echo $this->_tpl_vars['lng']['lbl_country']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['country']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <select name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[country]" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
country" onchange="check_zip_code_field(this, $('#<?php echo $this->_tpl_vars['id_prefix']; ?>
zipcode').get(0))">
          <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
            <option value="<?php echo $this->_tpl_vars['c']['country_code']; ?>
"<?php if ($this->_tpl_vars['address']['country'] == $this->_tpl_vars['c']['country_code'] || ( $this->_tpl_vars['c']['country_code'] == $this->_tpl_vars['config']['General']['default_country'] && $this->_tpl_vars['address']['country'] == "" )): ?> selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['c']['country'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['state']['avail'] == 'Y' && $this->_tpl_vars['default_fields']['country']['avail'] == 'Y'): ?>
    <tr style="display: none;">
      <td<?php if ($this->_tpl_vars['default_fields']['state']['required']): ?> class="data-required"<?php endif; ?>>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_states.tpl", 'smarty_include_vars' => array('state_name' => ($this->_tpl_vars['name_prefix'])."[state]",'country_name' => ($this->_tpl_vars['id_prefix'])."country",'county_name' => ($this->_tpl_vars['name_prefix'])."[county]",'state_value' => ((is_array($_tmp=@$this->_tpl_vars['address']['state'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_state']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_state'])),'county_value' => $this->_tpl_vars['address']['county'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['zipcode']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
zipcode"><?php echo $this->_tpl_vars['lng']['lbl_zip_code']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['zipcode']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/zipcode.tpl", 'smarty_include_vars' => array('zip_section' => $this->_tpl_vars['zip_section'],'name' => ($this->_tpl_vars['name_prefix'])."[zipcode]",'id' => ($this->_tpl_vars['id_prefix'])."zipcode",'val' => ((is_array($_tmp=@$this->_tpl_vars['address']['zipcode'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_zipcode']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_zipcode'])),'zip4' => $this->_tpl_vars['address']['zip4'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['phone']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
phone"><?php echo $this->_tpl_vars['lng']['lbl_phone']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['phone']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
phone" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[phone]" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address']['phone'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['default_fields']['fax']['avail'] == 'Y'): ?>
    <tr>
      <td class="data-name"><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
fax"><?php echo $this->_tpl_vars['lng']['lbl_fax']; ?>
</label></td>
      <td<?php if ($this->_tpl_vars['default_fields']['fax']['required'] == 'Y'): ?> class="data-required">*<?php else: ?>>&nbsp;<?php endif; ?></td>
      <td>
        <input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
fax" name="<?php echo $this->_tpl_vars['name_prefix']; ?>
[fax]" size="32" maxlength="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address']['fax'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      </td>
    </tr>
  <?php endif; ?>