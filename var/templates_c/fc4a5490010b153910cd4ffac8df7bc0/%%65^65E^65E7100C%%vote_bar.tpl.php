<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:48
         compiled from modules/Customer_Reviews/vote_bar.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'modules/Customer_Reviews/vote_bar.tpl', 6, false),array('modifier', 'escape', 'modules/Customer_Reviews/vote_bar.tpl', 6, false),array('function', 'load_defer', 'modules/Customer_Reviews/vote_bar.tpl', 36, false),)), $this); ?>
<?php func_load_lang($this, "modules/Customer_Reviews/vote_bar.tpl","txt_rating_note,lbl_not_rated_yet,txt_you_have_rated_this_product,lbl_sign_in_to_rate"); ?><div class="creviews-rating-box">
  <div class="creviews-vote-bar<?php if ($this->_tpl_vars['rating']['allow_add_rate']): ?> allow-add-rate<?php endif; ?>" title="<?php if ($this->_tpl_vars['rating']['total'] > 0): ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_rating_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'avg', $this->_tpl_vars['rating']['rating_level'], 'rating', $this->_tpl_vars['rating']['total']) : smarty_modifier_substitute($_tmp, 'avg', $this->_tpl_vars['rating']['rating_level'], 'rating', $this->_tpl_vars['rating']['total'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_not_rated_yet'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>">

 <?php unset($this->_sections['vote_subbar']);
$this->_sections['vote_subbar']['loop'] = is_array($_loop=($this->_tpl_vars['stars']['length'])) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['vote_subbar']['name'] = 'vote_subbar';
$this->_sections['vote_subbar']['show'] = true;
$this->_sections['vote_subbar']['max'] = $this->_sections['vote_subbar']['loop'];
$this->_sections['vote_subbar']['step'] = 1;
$this->_sections['vote_subbar']['start'] = $this->_sections['vote_subbar']['step'] > 0 ? 0 : $this->_sections['vote_subbar']['loop']-1;
if ($this->_sections['vote_subbar']['show']) {
    $this->_sections['vote_subbar']['total'] = $this->_sections['vote_subbar']['loop'];
    if ($this->_sections['vote_subbar']['total'] == 0)
        $this->_sections['vote_subbar']['show'] = false;
} else
    $this->_sections['vote_subbar']['total'] = 0;
if ($this->_sections['vote_subbar']['show']):

            for ($this->_sections['vote_subbar']['index'] = $this->_sections['vote_subbar']['start'], $this->_sections['vote_subbar']['iteration'] = 1;
                 $this->_sections['vote_subbar']['iteration'] <= $this->_sections['vote_subbar']['total'];
                 $this->_sections['vote_subbar']['index'] += $this->_sections['vote_subbar']['step'], $this->_sections['vote_subbar']['iteration']++):
$this->_sections['vote_subbar']['rownum'] = $this->_sections['vote_subbar']['iteration'];
$this->_sections['vote_subbar']['index_prev'] = $this->_sections['vote_subbar']['index'] - $this->_sections['vote_subbar']['step'];
$this->_sections['vote_subbar']['index_next'] = $this->_sections['vote_subbar']['index'] + $this->_sections['vote_subbar']['step'];
$this->_sections['vote_subbar']['first']      = ($this->_sections['vote_subbar']['iteration'] == 1);
$this->_sections['vote_subbar']['last']       = ($this->_sections['vote_subbar']['iteration'] == $this->_sections['vote_subbar']['total']);
?>
  <ul class="star-<?php echo $this->_sections['vote_subbar']['index']; ?>
">
    <li class="star-<?php echo $this->_sections['vote_subbar']['index']; ?>
">
      <?php if ($this->_tpl_vars['rating']['allow_add_rate']): ?>
        <a href="product.php?mode=add_vote&amp;productid=<?php echo $this->_tpl_vars['productid']; ?>
&amp;vote=<?php echo $this->_tpl_vars['stars']['levels'][$this->_sections['vote_subbar']['index']]; ?>
<?php if ($this->_tpl_vars['is_pconf']): ?>&amp;pconf=<?php echo $this->_tpl_vars['current_product']['productid']; ?>
&amp;slot=<?php echo $this->_tpl_vars['slot']; ?>
<?php endif; ?>"<?php if ($this->_tpl_vars['rating']['full_stars'] > $this->_sections['vote_subbar']['index']): ?> class="full"<?php endif; ?><?php if ($this->_tpl_vars['stars']['titles'][$this->_sections['vote_subbar']['index']]): ?> title="<?php echo ((is_array($_tmp=$this->_tpl_vars['stars']['titles'][$this->_sections['vote_subbar']['index']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
          <?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE' && $this->_tpl_vars['config']['UA']['version'] < 7): ?>
            <span class="bg"></span>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['rating']['full_stars'] == $this->_sections['vote_subbar']['index'] && $this->_tpl_vars['rating']['percent'] > 0): ?>
            <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" style="width: <?php echo $this->_tpl_vars['rating']['percent']; ?>
%;" />
          <?php endif; ?>
        </a>
      <?php else: ?>
        <span<?php if ($this->_tpl_vars['rating']['full_stars'] > $this->_sections['vote_subbar']['index']): ?> class="full"<?php endif; ?>>
          <?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE' && $this->_tpl_vars['config']['UA']['version'] < 7): ?>
            <span class="bg"></span>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['rating']['full_stars'] == $this->_sections['vote_subbar']['index'] && $this->_tpl_vars['rating']['percent'] > 0): ?>
            <img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" style="width: <?php echo $this->_tpl_vars['rating']['percent']; ?>
%;" />
          <?php endif; ?>
        </span>
      <?php endif; ?>
  <?php endfor; endif; ?>
  <?php unset($this->_sections['vote_subbar']);
$this->_sections['vote_subbar']['loop'] = is_array($_loop=($this->_tpl_vars['stars']['length'])) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['vote_subbar']['name'] = 'vote_subbar';
$this->_sections['vote_subbar']['show'] = true;
$this->_sections['vote_subbar']['max'] = $this->_sections['vote_subbar']['loop'];
$this->_sections['vote_subbar']['step'] = 1;
$this->_sections['vote_subbar']['start'] = $this->_sections['vote_subbar']['step'] > 0 ? 0 : $this->_sections['vote_subbar']['loop']-1;
if ($this->_sections['vote_subbar']['show']) {
    $this->_sections['vote_subbar']['total'] = $this->_sections['vote_subbar']['loop'];
    if ($this->_sections['vote_subbar']['total'] == 0)
        $this->_sections['vote_subbar']['show'] = false;
} else
    $this->_sections['vote_subbar']['total'] = 0;
if ($this->_sections['vote_subbar']['show']):

            for ($this->_sections['vote_subbar']['index'] = $this->_sections['vote_subbar']['start'], $this->_sections['vote_subbar']['iteration'] = 1;
                 $this->_sections['vote_subbar']['iteration'] <= $this->_sections['vote_subbar']['total'];
                 $this->_sections['vote_subbar']['index'] += $this->_sections['vote_subbar']['step'], $this->_sections['vote_subbar']['iteration']++):
$this->_sections['vote_subbar']['rownum'] = $this->_sections['vote_subbar']['iteration'];
$this->_sections['vote_subbar']['index_prev'] = $this->_sections['vote_subbar']['index'] - $this->_sections['vote_subbar']['step'];
$this->_sections['vote_subbar']['index_next'] = $this->_sections['vote_subbar']['index'] + $this->_sections['vote_subbar']['step'];
$this->_sections['vote_subbar']['first']      = ($this->_sections['vote_subbar']['iteration'] == 1);
$this->_sections['vote_subbar']['last']       = ($this->_sections['vote_subbar']['iteration'] == $this->_sections['vote_subbar']['total']);
?>
    </li>   </ul>
  <?php endfor; endif; ?>

  </div>
  <?php echo smarty_function_load_defer(array('file' => "modules/Customer_Reviews/vote_bar.js",'type' => 'js'), $this);?>


  <?php if (! $this->_tpl_vars['rating']['allow_add_rate'] && $this->_tpl_vars['rating']['forbidd_reason']): ?>
    <div class="creviews-rating">

      <?php if ($this->_tpl_vars['rating']['forbidd_reason'] == 'already_added'): ?>
        <?php echo $this->_tpl_vars['lng']['txt_you_have_rated_this_product']; ?>

      <?php elseif ($this->_tpl_vars['rating']['forbidd_reason'] == 'unlogged'): ?>
        <?php echo $this->_tpl_vars['lng']['lbl_sign_in_to_rate']; ?>

      <?php endif; ?>

    </div>
  <?php endif; ?>

</div>