<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:26
         compiled from customer/help/popup_info.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'customer/help/popup_info.tpl', 6, false),array('function', 'config_load', 'customer/help/popup_info.tpl', 9, false),array('function', 'load_defer_code', 'customer/help/popup_info.tpl', 71, false),)), $this); ?>
<?php func_load_lang($this, "customer/help/popup_info.tpl","lbl_close_window"); ?><?php echo '<?xml'; ?>
 version="1.0" encoding="<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
"<?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['skin_config'])), $this);?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/service_head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['SkinDir']; ?>
/css/<?php echo $this->_config[0]['vars']['CSSFilePrefix']; ?>
.popup.css" />
  <!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['SkinDir']; ?>
/css/<?php echo $this->_config[0]['vars']['CSSFilePrefix']; ?>
.popup.IE6.css" />
  <![endif]-->
</head>
<body<?php echo $this->_tpl_vars['reading_direction_tag']; ?>
<?php if ($this->_tpl_vars['body_onload'] != ''): ?> onload="javascript: <?php echo $this->_tpl_vars['body_onload']; ?>
"<?php endif; ?> class="<?php if ($_GET['open_in_layer']): ?> popup-in-layer<?php endif; ?><?php $_from = $this->_tpl_vars['container_classes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?><?php echo $this->_tpl_vars['c']; ?>
 <?php endforeach; endif; unset($_from); ?>">
<div id="page-container">
  <div id="page-container2">
    <div id="content-container">
      <div id="content-container2">
        <div id="center">
          <div id="center-main">

<!-- MAIN -->

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog_message.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['template_name'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['template_name'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php elseif ($this->_tpl_vars['pre'] != ""): ?>
<?php echo $this->_tpl_vars['pre']; ?>


<?php else: ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/error_page_not_found.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<!-- /MAIN -->
          </div>
        </div>
      </div>
    </div>

    <div class="clearing">&nbsp;</div>

    <div id="header">
      <div>
        <?php echo ((is_array($_tmp=@$this->_tpl_vars['popup_title'])) ? $this->_run_mod_handler('default', true, $_tmp, "&nbsp;") : smarty_modifier_default($_tmp, "&nbsp;")); ?>

      </div>
    </div>

    <div id="footer">
      <div>
        <a href="javascript:void(0);" onclick="javascript: window.close();"><?php echo $this->_tpl_vars['lng']['lbl_close_window']; ?>
</a>
      </div>
    </div>

<?php if ($this->_tpl_vars['active_modules']['SnS_connector']): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/SnS_connector/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['Google_Analytics'] != "" && $this->_tpl_vars['config']['Google_Analytics']['ganalytics_code'] != ""): ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Google_Analytics/ga_code.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

  </div>
</div>

<?php echo smarty_function_load_defer_code(array('type' => 'css'), $this);?>

<?php echo smarty_function_load_defer_code(array('type' => 'js'), $this);?>

</body>
</html>