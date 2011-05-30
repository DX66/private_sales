<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/service_css.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_defer', 'customer/service_css.tpl', 5, false),array('function', 'getvar', 'customer/service_css.tpl', 26, false),array('modifier', 'string_format', 'customer/service_css.tpl', 7, false),)), $this); ?>
<?php echo smarty_function_load_defer(array('file' => "css/".($this->_config[0]['vars']['CSSFilePrefix']).".css",'type' => 'css'), $this);?>

<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE'): ?>
  <?php $this->assign('ie_ver', ((is_array($_tmp=$this->_tpl_vars['config']['UA']['version'])) ? $this->_run_mod_handler('string_format', true, $_tmp, '%d') : smarty_modifier_string_format($_tmp, '%d'))); ?>
  <?php echo smarty_function_load_defer(array('file' => "css/".($this->_config[0]['vars']['CSSFilePrefix']).".IE".($this->_tpl_vars['ie_ver']).".css",'type' => 'css'), $this);?>

<?php endif; ?>

<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'Firefox' || $this->_tpl_vars['config']['UA']['browser'] == 'Mozilla'): ?>
  <?php echo smarty_function_load_defer(array('file' => "css/".($this->_config[0]['vars']['CSSFilePrefix']).".FF.css",'type' => 'css'), $this);?>

<?php endif; ?>

<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'Opera'): ?>
  <?php echo smarty_function_load_defer(array('file' => "css/".($this->_config[0]['vars']['CSSFilePrefix']).".Opera.css",'type' => 'css'), $this);?>

<?php endif; ?>

<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'Chrome'): ?>
  <?php echo smarty_function_load_defer(array('file' => "css/".($this->_config[0]['vars']['CSSFilePrefix']).".GC.css",'type' => 'css'), $this);?>

<?php endif; ?>

<?php echo smarty_function_load_defer(array('file' => "lib/cluetip/jquery.cluetip.css",'type' => 'css'), $this);?>


<?php if ($this->_tpl_vars['main'] == 'product'): ?>
  <?php echo smarty_function_getvar(array('var' => 'det_images_widget'), $this);?>

  <?php if ($this->_tpl_vars['det_images_widget'] == 'cloudzoom'): ?>
    <?php echo smarty_function_load_defer(array('file' => "lib/cloud_zoom/cloud-zoom.css",'type' => 'css'), $this);?>

  <?php elseif ($this->_tpl_vars['det_images_widget'] == 'colorbox'): ?>
    <?php echo smarty_function_load_defer(array('file' => "lib/colorbox/colorbox.css",'type' => 'css'), $this);?>

  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['ie_ver'] != ''): ?>
<style type="text/css">
<!--
<?php endif; ?>

<?php $_from = $this->_tpl_vars['css_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['mname'] => $this->_tpl_vars['files']):
?>
  <?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['f']):
?>
    <?php if (( $this->_tpl_vars['f']['browser'] == $this->_tpl_vars['config']['UA']['browser'] && $this->_tpl_vars['f']['version'] == $this->_tpl_vars['config']['UA']['version'] ) || ( $this->_tpl_vars['f']['browser'] == $this->_tpl_vars['config']['UA']['browser'] && ! $this->_tpl_vars['f']['version'] ) || ( ! $this->_tpl_vars['f']['browser'] && ! $this->_tpl_vars['f']['version'] ) || ( ! $this->_tpl_vars['f']['browser'] )): ?>
      <?php if ($this->_tpl_vars['f']['suffix']): ?>
        <?php echo smarty_function_load_defer(array('file' => "modules/".($this->_tpl_vars['mname'])."/".($this->_tpl_vars['f']['subpath']).($this->_config[0]['vars']['CSSFilePrefix']).".".($this->_tpl_vars['f']['suffix']).".css",'type' => 'css','css_inc_mode' => $this->_tpl_vars['ie_ver']), $this);?>

      <?php else: ?>
        <?php echo smarty_function_load_defer(array('file' => "modules/".($this->_tpl_vars['mname'])."/".($this->_tpl_vars['f']['subpath']).($this->_config[0]['vars']['CSSFilePrefix']).".css",'type' => 'css','css_inc_mode' => $this->_tpl_vars['ie_ver']), $this);?>

      <?php endif; ?>
    <?php endif; ?>
  <?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['ie_ver'] != ''): ?>
-->
</style>
<?php endif; ?>

<?php if ($this->_tpl_vars['AltSkinDir']): ?>
  <?php echo smarty_function_load_defer(array('file' => "css/altskin.css",'type' => 'css'), $this);?>

  <?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE'): ?>
    <?php echo smarty_function_load_defer(array('file' => "css/altskin.IE".($this->_tpl_vars['ie_ver']).".css",'type' => 'css'), $this);?>

  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['custom_styles']): ?>
<?php echo smarty_function_load_defer(array('file' => "css/custom_styles",'direct_info' => $this->_tpl_vars['custom_styles'],'type' => 'css'), $this);?>

<?php endif; ?>
