<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/search_sort_by.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'customer/search_sort_by.tpl', 12, false),array('modifier', 'cat', 'customer/search_sort_by.tpl', 12, false),array('modifier', 'escape', 'customer/search_sort_by.tpl', 24, false),)), $this); ?>
<?php func_load_lang($this, "customer/search_sort_by.tpl","lbl_sort_by,lbl_sort_by,lbl_sort_by"); ?><?php if ($this->_tpl_vars['sort_fields'] && ( $this->_tpl_vars['url'] || $this->_tpl_vars['navigation_script'] )): ?>

  <?php if ($this->_tpl_vars['url'] == '' && $this->_tpl_vars['navigation_script'] != ''): ?>
    <?php $this->assign('url', $this->_tpl_vars['navigation_script']); ?>
  <?php endif; ?>

  <?php if ($this->_tpl_vars['navigation_page'] > 1): ?>
    <?php $this->assign('url', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)))) ? $this->_run_mod_handler('cat', true, $_tmp, "&amp;page=".($this->_tpl_vars['navigation_page'])) : smarty_modifier_cat($_tmp, "&amp;page=".($this->_tpl_vars['navigation_page'])))); ?>
  <?php else: ?>
    <?php $this->assign('url', ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp))); ?>
  <?php endif; ?>

  <div class="search-sort-bar no-print">
    <strong class="search-sort-title"><?php echo $this->_tpl_vars['lng']['lbl_sort_by']; ?>
:</strong>

    <?php $_from = $this->_tpl_vars['sort_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['name'] => $this->_tpl_vars['field']):
?>

      <span class="search-sort-cell">
        <?php if ($this->_tpl_vars['name'] == $this->_tpl_vars['selected']): ?>
          <a href="<?php echo $this->_tpl_vars['url']; ?>
&amp;sort=<?php echo ((is_array($_tmp=$this->_tpl_vars['name'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
&amp;sort_direction=<?php if ($this->_tpl_vars['direction'] == 1): ?>0<?php else: ?>1<?php endif; ?>" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sort_by'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="search-sort-link <?php if ($this->_tpl_vars['direction']): ?>down-direction<?php else: ?>up-direction<?php endif; ?>"><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        <?php else: ?>
          <a href="<?php echo $this->_tpl_vars['url']; ?>
&amp;sort=<?php echo ((is_array($_tmp=$this->_tpl_vars['name'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
&amp;sort_direction=<?php echo $this->_tpl_vars['direction']; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_sort_by'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" class="search-sort-link"><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        <?php endif; ?>
      </span>

    <?php endforeach; endif; unset($_from); ?>

  </div>

<?php endif; ?>