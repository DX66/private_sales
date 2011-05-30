<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from modules/Feature_Comparison/compare_checkbox.tpl */ ?>
<?php func_load_lang($this, "modules/Feature_Comparison/compare_checkbox.tpl","lbl_check_to_compare"); ?><?php if ($this->_tpl_vars['product']['fclassid'] > 0): ?>

<div class="fcomp-checkbox-box">

  <label for="fe_pid_<?php echo $this->_tpl_vars['product']['productid']; ?>
">
    <input type="checkbox" id="fe_pid_<?php echo $this->_tpl_vars['product']['productid']; ?>
" value="Y" />
    <?php echo $this->_tpl_vars['lng']['lbl_check_to_compare']; ?>

  </label>

</div>

<?php endif; ?>