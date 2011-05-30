<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/language_selector.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/language_selector.tpl', 19, false),array('modifier', 'amp', 'customer/language_selector.tpl', 30, false),array('modifier', 'escape', 'customer/language_selector.tpl', 30, false),)), $this); ?>
<?php func_load_lang($this, "customer/language_selector.tpl","lbl_select_language"); ?><?php if ($this->_tpl_vars['all_languages_cnt'] > 1): ?>
  <div class="languages <?php if ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'Y'): ?>languages-row<?php elseif ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'F'): ?>languages-flags<?php else: ?>languages-select<?php endif; ?>">

    <?php if ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'Y' || $this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'A' || $this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'L'): ?>

      <?php $_from = $this->_tpl_vars['all_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['languages'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['languages']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['l']):
        $this->_foreach['languages']['iteration']++;
?>
        <?php if ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'Y'): ?>
          <?php $this->assign('lng_dspl', $this->_tpl_vars['l']['code3']); ?>
        <?php elseif ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'A'): ?>
          <?php $this->assign('lng_dspl', $this->_tpl_vars['l']['code']); ?>
        <?php elseif ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'L'): ?>
          <?php $this->assign('lng_dspl', $this->_tpl_vars['l']['language']); ?>
        <?php endif; ?> 
        <?php if ($this->_tpl_vars['store_language'] == $this->_tpl_vars['l']['code']): ?>
          <strong class="language-code lng-<?php echo $this->_tpl_vars['l']['code']; ?>
"><?php echo ((is_array($_tmp=@$this->_tpl_vars['lng_dspl'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['l']['language']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['l']['language'])); ?>
</strong>
        <?php else: ?>
          <a href="home.php?sl=<?php echo $this->_tpl_vars['l']['code']; ?>
" class="language-code lng-<?php echo $this->_tpl_vars['l']['code']; ?>
"><?php echo ((is_array($_tmp=@$this->_tpl_vars['lng_dspl'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['l']['language']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['l']['language'])); ?>
</a>
        <?php endif; ?>
        <?php if (! ($this->_foreach['languages']['iteration'] == $this->_foreach['languages']['total'])): ?>|<?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>

    <?php elseif ($this->_tpl_vars['config']['Appearance']['line_language_selector'] == 'F'): ?>

      <?php $_from = $this->_tpl_vars['all_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['languages'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['languages']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['l']):
        $this->_foreach['languages']['iteration']++;
?>
        <?php if ($this->_tpl_vars['store_language'] == $this->_tpl_vars['l']['code']): ?>
          <strong class="language-code lng-<?php echo $this->_tpl_vars['l']['code']; ?>
<?php if (($this->_foreach['languages']['iteration'] == $this->_foreach['languages']['total'])): ?> language-last<?php endif; ?>"><img src="<?php if (! $this->_tpl_vars['l']['is_url']): ?><?php echo $this->_tpl_vars['current_location']; ?>
<?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['l']['tmbn_url'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['l']['language'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" width="<?php echo $this->_tpl_vars['l']['image_x']; ?>
" height="<?php echo $this->_tpl_vars['l']['image_y']; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['l']['language'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></strong>
        <?php else: ?>
          <a href="home.php?sl=<?php echo $this->_tpl_vars['l']['code']; ?>
" class="language-code lng-<?php echo $this->_tpl_vars['l']['code']; ?>
<?php if (($this->_foreach['languages']['iteration'] == $this->_foreach['languages']['total'])): ?> language-last<?php endif; ?>"><img class="language-code-out" src="<?php if (! $this->_tpl_vars['l']['is_url']): ?><?php echo $this->_tpl_vars['current_location']; ?>
<?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['l']['tmbn_url'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['l']['language'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['l']['language'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" width="<?php echo $this->_tpl_vars['l']['image_x']; ?>
" height="<?php echo $this->_tpl_vars['l']['image_y']; ?>
" onmouseover="javascript:$(this).removeClass('language-code-out').addClass('language-code-over');" onmouseout="javascript:$(this).removeClass('language-code-over').addClass('language-code-out');" /></a>
        <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>

    <?php else: ?>

      <form action="home.php" method="get" name="sl_form">
        <input type="hidden" name="redirect" value="<?php echo $_SERVER['PHP_SELF']; ?>
<?php if ($_SERVER['QUERY_STRING']): ?>?<?php echo ((is_array($_tmp=$_SERVER['QUERY_STRING'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
<?php endif; ?>" />

        <?php echo '<label>'; ?><?php echo $this->_tpl_vars['lng']['lbl_select_language']; ?><?php echo ':<select name="sl" onchange="javascript: this.form.submit();">'; ?><?php $_from = $this->_tpl_vars['all_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['l']):
?><?php echo '<option value="'; ?><?php echo $this->_tpl_vars['l']['code']; ?><?php echo '"'; ?><?php if ($this->_tpl_vars['store_language'] == $this->_tpl_vars['l']['code']): ?><?php echo ' selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo $this->_tpl_vars['l']['language']; ?><?php echo '</option>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</select></label>'; ?>


      </form>

    <?php endif; ?>

  </div>
<?php endif; ?>