<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from customer/main/navigation.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'customer/main/navigation.tpl', 6, false),array('modifier', 'escape', 'customer/main/navigation.tpl', 17, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/navigation.tpl","lbl_result_pages,lbl_prev_page,lbl_page,lbl_current_page,lbl_page,lbl_page,lbl_next_page"); ?><ul class="simple-list-left width-100">
<?php $this->assign('navigation_script', ((is_array($_tmp=$this->_tpl_vars['navigation_script'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp))); ?>
<?php if ($this->_tpl_vars['total_pages'] > 2): ?>
<li class="item-left">

  <div class="nav-pages">
    <!-- max_pages: <?php echo $this->_tpl_vars['navigation_max_pages']; ?>
 -->
    <span class="nav-pages-title"><?php echo $this->_tpl_vars['lng']['lbl_result_pages']; ?>
:</span>

    <?php echo ''; ?><?php if ($this->_tpl_vars['navigation_arrow_left']): ?><?php echo '<a class="left-arrow right-delimiter" href="'; ?><?php echo $this->_tpl_vars['navigation_script']; ?><?php echo '&amp;page='; ?><?php echo $this->_tpl_vars['navigation_arrow_left']; ?><?php echo '"><img src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_prev_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '" /></a>'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['start_page'] > 1): ?><?php echo '<a class="nav-page right-delimiter" href="'; ?><?php echo $this->_tpl_vars['navigation_script']; ?><?php echo '&amp;page=1" title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo ' #1">1</a>'; ?><?php if ($this->_tpl_vars['start_page'] > 2): ?><?php echo '<span class="nav-dots right-delimiter">...</span>'; ?><?php endif; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php unset($this->_sections['page']);
$this->_sections['page']['name'] = 'page';
$this->_sections['page']['loop'] = is_array($_loop=$this->_tpl_vars['total_pages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['page']['start'] = (int)$this->_tpl_vars['start_page'];
$this->_sections['page']['show'] = true;
$this->_sections['page']['max'] = $this->_sections['page']['loop'];
$this->_sections['page']['step'] = 1;
if ($this->_sections['page']['start'] < 0)
    $this->_sections['page']['start'] = max($this->_sections['page']['step'] > 0 ? 0 : -1, $this->_sections['page']['loop'] + $this->_sections['page']['start']);
else
    $this->_sections['page']['start'] = min($this->_sections['page']['start'], $this->_sections['page']['step'] > 0 ? $this->_sections['page']['loop'] : $this->_sections['page']['loop']-1);
if ($this->_sections['page']['show']) {
    $this->_sections['page']['total'] = min(ceil(($this->_sections['page']['step'] > 0 ? $this->_sections['page']['loop'] - $this->_sections['page']['start'] : $this->_sections['page']['start']+1)/abs($this->_sections['page']['step'])), $this->_sections['page']['max']);
    if ($this->_sections['page']['total'] == 0)
        $this->_sections['page']['show'] = false;
} else
    $this->_sections['page']['total'] = 0;
if ($this->_sections['page']['show']):

            for ($this->_sections['page']['index'] = $this->_sections['page']['start'], $this->_sections['page']['iteration'] = 1;
                 $this->_sections['page']['iteration'] <= $this->_sections['page']['total'];
                 $this->_sections['page']['index'] += $this->_sections['page']['step'], $this->_sections['page']['iteration']++):
$this->_sections['page']['rownum'] = $this->_sections['page']['iteration'];
$this->_sections['page']['index_prev'] = $this->_sections['page']['index'] - $this->_sections['page']['step'];
$this->_sections['page']['index_next'] = $this->_sections['page']['index'] + $this->_sections['page']['step'];
$this->_sections['page']['first']      = ($this->_sections['page']['iteration'] == 1);
$this->_sections['page']['last']       = ($this->_sections['page']['iteration'] == $this->_sections['page']['total']);
?><?php echo ''; ?><?php if ($this->_sections['page']['index'] == $this->_tpl_vars['navigation_page']): ?><?php echo '<span class="current-page'; ?><?php if (! $this->_sections['page']['last'] || ( $this->_tpl_vars['total_pages'] <= $this->_tpl_vars['total_super_pages'] || $this->_tpl_vars['navigation_arrow_right'] )): ?><?php echo ' right-delimiter'; ?><?php endif; ?><?php echo '" title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_current_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo ': #'; ?><?php echo $this->_sections['page']['index']; ?><?php echo '">'; ?><?php echo $this->_sections['page']['index']; ?><?php echo '</span>'; ?><?php else: ?><?php echo '<a class="nav-page'; ?><?php if (! $this->_sections['page']['last'] || ( $this->_tpl_vars['total_pages'] <= $this->_tpl_vars['total_super_pages'] || $this->_tpl_vars['navigation_arrow_right'] )): ?><?php echo ' right-delimiter'; ?><?php endif; ?><?php echo '" href="'; ?><?php echo $this->_tpl_vars['navigation_script']; ?><?php echo '&amp;page='; ?><?php echo $this->_sections['page']['index']; ?><?php echo '" title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo ' #'; ?><?php echo $this->_sections['page']['index']; ?><?php echo '">'; ?><?php echo $this->_sections['page']['index']; ?><?php echo '</a>'; ?><?php endif; ?><?php echo ''; ?><?php endfor; endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['total_pages'] <= $this->_tpl_vars['total_super_pages']): ?><?php echo ''; ?><?php if ($this->_tpl_vars['total_pages'] < $this->_tpl_vars['total_super_pages']): ?><?php echo '<span class="nav-dots right-delimiter">...</span>'; ?><?php endif; ?><?php echo '<a class="nav-page'; ?><?php if ($this->_tpl_vars['navigation_arrow_right']): ?><?php echo ' right-delimiter'; ?><?php endif; ?><?php echo '" href="'; ?><?php echo $this->_tpl_vars['navigation_script']; ?><?php echo '&amp;page='; ?><?php echo $this->_tpl_vars['total_super_pages']; ?><?php echo '" title="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo ' #'; ?><?php echo $this->_tpl_vars['total_super_pages']; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['total_super_pages']; ?><?php echo '</a>'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['navigation_arrow_right']): ?><?php echo '<a class="right-arrow" href="'; ?><?php echo $this->_tpl_vars['navigation_script']; ?><?php echo '&amp;page='; ?><?php echo $this->_tpl_vars['navigation_arrow_right']; ?><?php echo '"><img src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_next_page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '" /></a>'; ?><?php endif; ?><?php echo ''; ?>


  </div>
</li>
<?php endif; ?>
<li class="item-right">
<?php if ($this->_tpl_vars['per_page'] == 'Y' && $this->_tpl_vars['total_items'] >= $this->_tpl_vars['per_page_values']['0']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/per_page.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
</li>
</ul>
<div class="clearing"></div>