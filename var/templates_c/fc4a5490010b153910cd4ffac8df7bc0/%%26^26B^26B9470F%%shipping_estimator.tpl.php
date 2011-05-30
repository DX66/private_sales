<?php /* Smarty version 2.6.26, created on 2011-05-26 16:24:49
         compiled from customer/main/shipping_estimator.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'customer/main/shipping_estimator.tpl', 15, false),array('modifier', 'default', 'customer/main/shipping_estimator.tpl', 18, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/shipping_estimator.tpl","lbl_destination,lbl_change,lbl_estimate_shipping_cost,lbl_estimate_shipping_cost"); ?><?php if ($this->_tpl_vars['login'] == ''): ?>

  <div class="estimator-container">

    <?php if ($this->_tpl_vars['userinfo'] != ''): ?>

      <strong><?php echo $this->_tpl_vars['lng']['lbl_destination']; ?>
:</strong>

      <?php $_from = $this->_tpl_vars['shipping_estimate_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['estimate'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['estimate']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['f']):
        $this->_foreach['estimate']['iteration']++;
?>
        <?php if ($this->_tpl_vars['userinfo']['address']['S'] == ''): ?>
          <?php $this->assign('k', ((is_array($_tmp='s_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['k']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['k']))); ?>
        <?php endif; ?>  
        <?php $this->assign('_fieldname', ((is_array($_tmp=$this->_tpl_vars['k'])) ? $this->_run_mod_handler('cat', true, $_tmp, 'name') : smarty_modifier_cat($_tmp, 'name'))); ?>
        <?php $this->assign('_field', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['userinfo']['address']['S'][$this->_tpl_vars['_fieldname']])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['address']['S'][$this->_tpl_vars['k']]) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['address']['S'][$this->_tpl_vars['k']])))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo'][$this->_tpl_vars['_fieldname']]) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo'][$this->_tpl_vars['_fieldname']])))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo'][$this->_tpl_vars['k']]) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo'][$this->_tpl_vars['k']]))); ?>
        <?php if ($this->_tpl_vars['f']['avail'] == 'Y' && $this->_tpl_vars['_field'] != ''): ?>
          <?php echo $this->_tpl_vars['_field']; ?>

          <?php if (! ($this->_foreach['estimate']['iteration'] == $this->_foreach['estimate']['total'])): ?>, <?php endif; ?><?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
    
      <?php $this->assign('btitle', $this->_tpl_vars['lng']['lbl_change']); ?>

    <?php endif; ?>

    <div class="button-row">
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => ((is_array($_tmp=@$this->_tpl_vars['btitle'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['lng']['lbl_estimate_shipping_cost']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['lng']['lbl_estimate_shipping_cost'])),'href' => "javascript:popupOpen('popup_estimate_shipping.php');",'style' => 'link')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

    <div class="smethods">
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/checkout_shipping_methods.tpl", 'smarty_include_vars' => array('simple_list' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

  </div>

  <hr />
<?php endif; ?>