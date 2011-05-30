<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:36
         compiled from customer/main/membership_signup.tpl */ ?>
<?php func_load_lang($this, "customer/main/membership_signup.tpl","lbl_signup_for_membership,lbl_not_member"); ?><tr>
  <td class="data-name"><?php echo $this->_tpl_vars['lng']['lbl_signup_for_membership']; ?>
</td>
  <td></td>
  <td>
    <select name="pending_membershipid">
      <option value=""><?php echo $this->_tpl_vars['lng']['lbl_not_member']; ?>
</option>
      <?php $_from = $this->_tpl_vars['membership_levels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
        <option value="<?php echo $this->_tpl_vars['v']['membershipid']; ?>
"<?php if ($this->_tpl_vars['userinfo']['pending_membershipid'] == $this->_tpl_vars['v']['membershipid']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['membership']; ?>
</option>
      <?php endforeach; endif; unset($_from); ?>
    </select>
  </td>
</tr>