<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:22
         compiled from customer/buttons/button.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'regex_replace', 'customer/buttons/button.tpl', 5, false),array('modifier', 'amp', 'customer/buttons/button.tpl', 13, false),array('modifier', 'default', 'customer/buttons/button.tpl', 34, false),array('modifier', 'escape', 'customer/buttons/button.tpl', 34, false),)), $this); ?>
<?php $this->assign('js_link', ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/^\s*javascript\s*:/Si", "") : smarty_modifier_regex_replace($_tmp, "/^\s*javascript\s*:/Si", ""))); ?>
<?php if ($this->_tpl_vars['js_link'] == $this->_tpl_vars['href']): ?>

  <?php if ($this->_tpl_vars['href']): ?>
    <?php $this->assign('is_link', true); ?>
  <?php endif; ?>

  <?php $this->assign('js_link', false); ?>
  <?php $this->assign('href', ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp))); ?>

<?php else: ?>

  <?php $this->assign('js_link', $this->_tpl_vars['href']); ?>
  <?php if ($this->_tpl_vars['js_to_href'] != 'Y'): ?>

    <?php $this->assign('onclick', $this->_tpl_vars['href']); ?>
    <?php if ($this->_tpl_vars['link_href']): ?>
      <?php $this->assign('href', $this->_tpl_vars['link_href']); ?>
    <?php else: ?>
      <?php $this->assign('href', "javascript:void(0);"); ?>
    <?php endif; ?>

  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['style'] == 'link'): ?>

  <?php if ($this->_tpl_vars['type'] == 'input'): ?>

    <button class="simple-button<?php if ($this->_tpl_vars['additional_button_class']): ?> <?php echo $this->_tpl_vars['additional_button_class']; ?>
<?php endif; ?>" type="submit" title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['js_link']): ?> onclick="<?php echo $this->_tpl_vars['js_link']; ?>
"<?php endif; ?>>
      <?php echo '<img class="left-simple-button" src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt="" /><span>'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['button_title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '</span><img class="right-simple-button" src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt="" />'; ?>

    </button>

  <?php else: ?>

    <?php echo '<a class="simple-button'; ?><?php if ($this->_tpl_vars['additional_button_class']): ?><?php echo ' '; ?><?php echo $this->_tpl_vars['additional_button_class']; ?><?php echo ''; ?><?php endif; ?><?php echo '" href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['onclick'] != ''): ?><?php echo ' onclick="'; ?><?php echo $this->_tpl_vars['onclick']; ?><?php echo '; return false;"'; ?><?php endif; ?><?php echo ' title="'; ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['target'] != ''): ?><?php echo ' target="'; ?><?php echo $this->_tpl_vars['target']; ?><?php echo '"'; ?><?php endif; ?><?php echo '><span>'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['button_title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '</span></a>'; ?>


  <?php endif; ?>

<?php elseif ($this->_tpl_vars['style'] == 'image'): ?>

  <?php if ($this->_tpl_vars['type'] == 'input'): ?>

    <input class="image-button<?php if ($this->_tpl_vars['additional_button_class']): ?> <?php echo $this->_tpl_vars['additional_button_class']; ?>
<?php endif; ?>" type="image" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['js_link']): ?> onclick="<?php echo $this->_tpl_vars['js_link']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['button_id']): ?> id="<?php echo ((is_array($_tmp=$this->_tpl_vars['button_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?> />

  <?php else: ?>

    <?php echo '<a class="image-button'; ?><?php if ($this->_tpl_vars['additional_button_class']): ?><?php echo ' '; ?><?php echo $this->_tpl_vars['additional_button_class']; ?><?php echo ''; ?><?php endif; ?><?php echo '" href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['onclick'] != ''): ?><?php echo ' onclick="'; ?><?php echo $this->_tpl_vars['onclick']; ?><?php echo '"'; ?><?php endif; ?><?php echo ' title="'; ?><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['target'] != ''): ?><?php echo ' target="'; ?><?php echo $this->_tpl_vars['target']; ?><?php echo '"'; ?><?php endif; ?><?php echo '><img src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt="" /></a>'; ?>


  <?php endif; ?>

<?php elseif ($this->_tpl_vars['is_link']): ?>

  <?php if ($this->_tpl_vars['js_link']): ?>
    <?php $this->assign('div_link', $this->_tpl_vars['js_link']); ?>
  <?php else: ?>
    <?php $this->assign('div_link', ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp))); ?>
    <?php $this->assign('div_link', "javascript: self.location = '".($this->_tpl_vars['div_link'])."'; if (event) event.cancelBubble = true;"); ?>
  <?php endif; ?>
  <div class="button<?php if ($this->_tpl_vars['additional_button_class']): ?> <?php echo $this->_tpl_vars['additional_button_class']; ?>
<?php endif; ?>" title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="<?php echo $this->_tpl_vars['div_link']; ?>
"<?php if ($this->_tpl_vars['button_id']): ?> id="<?php echo ((is_array($_tmp=$this->_tpl_vars['button_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
    <a href="<?php echo $this->_tpl_vars['href']; ?>
" onclick="<?php if ($this->_tpl_vars['js_link']): ?><?php echo $this->_tpl_vars['js_link']; ?>
;<?php else: ?>javascript:<?php endif; ?> if (event) event.cancelBubble = true;"<?php echo $this->_tpl_vars['reading_direction_tag']; ?>
><?php echo ((is_array($_tmp=$this->_tpl_vars['button_title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</a>

  </div>

<?php elseif ($this->_tpl_vars['style'] == 'dropout'): ?>

  <div class="dropout-wrapper">
    <div class="dropout-container">
      <div class="button<?php if ($this->_tpl_vars['additional_button_class']): ?> <?php echo $this->_tpl_vars['additional_button_class']; ?>
<?php endif; ?>" title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" id="dropout_btn_<?php echo ((is_array($_tmp=@$this->_tpl_vars['prefix'])) ? $this->_run_mod_handler('default', true, $_tmp, 'dropout') : smarty_modifier_default($_tmp, 'dropout')); ?>
_<?php echo $this->_tpl_vars['dropout_id']; ?>
">
        <div<?php echo $this->_tpl_vars['reading_direction_tag']; ?>
><?php echo ((is_array($_tmp=$this->_tpl_vars['button_title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</div>
      </div>
      <div class="dropout-box">
      <?php if ($this->_tpl_vars['dropout_tpl'] != ""): ?>
        <ul>
          <?php $this->assign('style', false); ?>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['dropout_tpl'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </ul>
      <?php endif; ?>
      </div>
    </div>
  </div>

<?php elseif ($this->_tpl_vars['style'] == 'div_button'): ?>

  <div class="button<?php if ($this->_tpl_vars['additional_button_class']): ?> <?php echo $this->_tpl_vars['additional_button_class']; ?>
<?php endif; ?>" title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['js_link']): ?> onclick="<?php echo $this->_tpl_vars['js_link']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['button_id']): ?> id="<?php echo ((is_array($_tmp=$this->_tpl_vars['button_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
    <div<?php echo $this->_tpl_vars['reading_direction_tag']; ?>
><?php echo ((is_array($_tmp=$this->_tpl_vars['button_title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</div>
  </div>

<?php else: ?>

  <button class="button<?php if ($this->_tpl_vars['additional_button_class']): ?> <?php echo $this->_tpl_vars['additional_button_class']; ?>
<?php endif; ?>" type="<?php if ($this->_tpl_vars['type'] == 'input'): ?>submit<?php else: ?>button<?php endif; ?>" title="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['button_title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['button_title'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['js_link']): ?> onclick="<?php echo $this->_tpl_vars['js_link']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['button_id']): ?> id="<?php echo ((is_array($_tmp=$this->_tpl_vars['button_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
  <?php echo '<span class="button-right"><span class="button-left"'; ?><?php echo $this->_tpl_vars['reading_direction_tag']; ?><?php echo '>'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['button_title'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?><?php echo '</span></span>'; ?>

  </button>

<?php endif; ?>