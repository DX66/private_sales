<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from meta.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'meta.tpl', 5, false),array('modifier', 'escape', 'meta.tpl', 9, false),array('function', 'load_defer', 'meta.tpl', 92, false),array('function', 'load_defer_code', 'meta.tpl', 99, false),)), $this); ?>
<?php func_load_lang($this, "meta.tpl","lbl_gmap_geocode_error,lbl_close"); ?><meta http-equiv="Content-Type" content="text/html; charset=<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Language" content="<?php if (( $this->_tpl_vars['usertype'] == 'P' || $this->_tpl_vars['usertype'] == 'A' ) && $this->_tpl_vars['current_language'] != ""): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['current_language'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['store_language'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>" />
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
<?php if ($this->_tpl_vars['__frame_not_allowed']): ?>
<script type="text/javascript">
//<![CDATA[
if (top != self)
  top.location = self.location;
//]]>
</script>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "presets_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/common.js"></script>
<?php if ($this->_tpl_vars['config']['Adaptives']['is_first_start'] == 'Y'): ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/browser_identificator.js"></script>
<?php endif; ?>
<?php if ($this->_tpl_vars['webmaster_mode'] == 'editor'): ?>
<script type="text/javascript">
//<![CDATA[
var store_language = "<?php if (( $this->_tpl_vars['usertype'] == 'P' || $this->_tpl_vars['usertype'] == 'A' ) && $this->_tpl_vars['current_language'] != ""): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['current_language'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['store_language'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
<?php endif; ?>";
var catalogs = new Object();
catalogs.admin = "<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
";
catalogs.provider = "<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
";
catalogs.customer = "<?php echo $this->_tpl_vars['catalogs']['customer']; ?>
";
catalogs.partner = "<?php echo $this->_tpl_vars['catalogs']['partner']; ?>
";
catalogs.images = "<?php echo $this->_tpl_vars['ImagesDir']; ?>
";
catalogs.skin = "<?php echo $this->_tpl_vars['SkinDir']; ?>
";
var lng_labels = [];
<?php $_from = $this->_tpl_vars['webmaster_lng']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lbl_name'] => $this->_tpl_vars['lbl_val']):
?>
lng_labels['<?php echo $this->_tpl_vars['lbl_name']; ?>
'] = '<?php echo $this->_tpl_vars['lbl_val']; ?>
';
<?php endforeach; endif; unset($_from); ?>
var page_charset = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
";
//]]>
</script>
<script type="text/javascript" language="JavaScript 1.2" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/editor_common.js"></script>
<?php if ($this->_tpl_vars['user_agent'] == 'ns'): ?>
<script type="text/javascript" language="JavaScript 1.2" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/editorns.js"></script>
<?php else: ?>
<script type="text/javascript" language="JavaScript 1.2" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/editor.js"></script>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Magnifier'] != ""): ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/lib/swfobject-min.js"></script>
<?php endif; ?>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/lib/jquery-min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/lib/cluetip/jquery.cluetip.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['SkinDir']; ?>
/lib/cluetip/jquery.cluetip.css" />

<!--[if lt IE 7]>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/iepngfix.js"></script>
<![endif]-->
<?php if ($this->_tpl_vars['gmap_enabled']): ?>
<script type="text/javascript">
//<![CDATA[
var gmapGeocodeError="<?php echo $this->_tpl_vars['lng']['lbl_gmap_geocode_error']; ?>
";
var lbl_close="<?php echo $this->_tpl_vars['lng']['lbl_close']; ?>
";
//]]>
</script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/gmap.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/js/modal.js"></script>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "jquery_ui.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<script type="text/javascript">
//<![CDATA[
var md = <?php echo ((is_array($_tmp=@$this->_tpl_vars['config']['Appearance']['delay_value'])) ? $this->_run_mod_handler('default', true, $_tmp, 10) : smarty_modifier_default($_tmp, 10)); ?>
*1000;
<?php echo '
$(document).ready( function() {
  $(\'form\').not(\'.skip-auto-validation\').each( function() {
    applyCheckOnSubmit(this);
  });

  $("input:submit, input:button, button, a.simple-button").button();

  $(".top-message-info").fadeIn(\'slow\').delay(md).fadeOut(\'slow\');
});

'; ?>

//]]>
</script>

<?php echo smarty_function_load_defer(array('file' => "js/ajax.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "js/popup_open.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jquery.blockUI.js",'type' => 'js'), $this);?>

<?php echo smarty_function_load_defer(array('file' => "lib/jquery.blockUI.defaults.js",'type' => 'js'), $this);?>


<?php echo smarty_function_load_defer(array('file' => "js/sticky.js",'type' => 'js'), $this);?>


<?php echo smarty_function_load_defer_code(array('type' => 'css'), $this);?>

<?php echo smarty_function_load_defer_code(array('type' => 'js'), $this);?>
