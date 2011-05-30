<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from jquery_ui.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_defer', 'jquery_ui.tpl', 5, false),)), $this); ?>
<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.core.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.widget.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.position.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.mouse.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.button.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.dialog.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.resizable.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.draggable.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.tabs.min.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.datepicker.min.js",'type' => 'js'), $this);?>


<?php if ($this->_tpl_vars['usertype'] == 'C'): ?>
  <?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.theme.css",'type' => 'css'), $this);?>

<?php else: ?>
  <?php echo smarty_function_load_defer(array('file' => "lib/jqueryui/jquery.ui.admin.css",'type' => 'css'), $this);?>

<?php endif; ?>