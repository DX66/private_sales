<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from modules/Special_Offers/customer/cart_bonuses.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'modules/Special_Offers/customer/cart_bonuses.tpl', 11, false),)), $this); ?>
<?php func_load_lang($this, "modules/Special_Offers/customer/cart_bonuses.tpl","lbl_sp_cart_bonuses_title,lbl_sp_cart_bonuses_bp,lbl_sp_cart_bonuses_memberships,lbl_or"); ?><?php if ($this->_tpl_vars['cart']['bonuses'] != ""): ?>
  <div>
    <strong><?php echo $this->_tpl_vars['lng']['lbl_sp_cart_bonuses_title']; ?>
</strong>

    <ul>
      <?php if ($this->_tpl_vars['cart']['bonuses']['points'] != 0): ?>
        <li><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sp_cart_bonuses_bp'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'num', $this->_tpl_vars['cart']['bonuses']['points']) : smarty_modifier_substitute($_tmp, 'num', $this->_tpl_vars['cart']['bonuses']['points'])); ?>
</li>
      <?php endif; ?>
      <?php if ($this->_tpl_vars['cart']['bonuses']['memberships'] != ""): ?>
        <li><?php echo $this->_tpl_vars['lng']['lbl_sp_cart_bonuses_memberships']; ?>
<br />
          <?php $_from = $this->_tpl_vars['cart']['bonuses']['memberships']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['memberships'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['memberships']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['membership']):
        $this->_foreach['memberships']['iteration']++;
?>
            <?php echo $this->_tpl_vars['membership']; ?>

            <?php if (! ($this->_foreach['memberships']['iteration'] == $this->_foreach['memberships']['total'])): ?>
              <?php echo $this->_tpl_vars['lng']['lbl_or']; ?>

            <?php endif; ?>
          <?php endforeach; endif; unset($_from); ?>
        </li>
      <?php endif; ?>
    </ul>

  </div>

  <hr />

<?php endif; ?>