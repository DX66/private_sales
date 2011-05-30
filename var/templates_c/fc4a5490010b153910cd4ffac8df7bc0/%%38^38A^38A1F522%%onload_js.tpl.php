<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from onload_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'onload_js.tpl', 151, false),array('function', 'load_defer', 'onload_js.tpl', 182, false),)), $this); ?>
<?php ob_start(); ?>

<?php if ($_GET['is_install_preview']): ?>
<?php echo '
//  Fix problem with refreshing of the skin preview  
//  during skin installation
var _ts = new Date();
$(\'link\').each(function(){
$(this).attr(\'href\', this.href + \'?\' + _ts.valueOf());
});
'; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['config']['SEO']['clean_urls_enabled'] == 'Y'): ?>
<?php echo '
//  Fix a.href if base url is defined for page
function anchor_fix() {
var links = document.getElementsByTagName(\'A\');
var m;
var _rg = new RegExp("(^|" + self.location.host + xcart_web_dir + "/)#([\\\\w\\\\d_]+)$")
for (var i = 0; i < links.length; i++) {
  if (links[i].href && (m = links[i].href.match(_rg))) {
    links[i].href = \'javascript:void(self.location.hash = "\' + m[2] + \'");\';
  }
}
}

if (window.addEventListener)
window.addEventListener("load", anchor_fix, false);

else if (window.attachEvent)
window.attachEvent("onload", anchor_fix);
'; ?>

<?php endif; ?>

<?php echo '
function initDropOutButton() {

  if ($(this).hasClass(\'activated-widget\'))
    return;

  $(this).addClass(\'activated-widget\');

  var dropOutBoxObj = $(this).parent().find(\'.dropout-box\');

  // Process the onclick event on a dropout button 
  $(this).click(
    function(e) {
      e.stopPropagation();
      $(\'.dropout-box\').removeClass(\'current\');
      dropOutBoxObj
        .toggle()
        .addClass(\'current\');
      $(\'.dropout-box\').not(\'.current\').hide();
      if (dropOutBoxObj.offset().top + dropOutBoxObj.height() - $(\'#center-main\').offset().top - $(\'#center-main\').height() > 0) {
        dropOutBoxObj.css(\'bottom\', \'-2px\');
      }
    }
  );
 
  // Click on a dropout layer keeps the dropout content opened
  $(this).parent().click(
    function(e) { 
      e.stopPropagation(); 
    }
  );

  // shift the dropout layer from the right hand side 
  // if it\'s out of the main area
  var borderDistance = ($("#center-main").offset().left + $("#center-main").outerWidth()) - ($(this).offset().left + dropOutBoxObj.outerWidth());
  if (!isNaN(borderDistance) && borderDistance < 0) {
    dropOutBoxObj.css(\'left\', borderDistance+\'px\');
  }

  // Fix for IE6
  if ($.browser.msie && $.browser.version < 7) {
    dropOutBoxObj.bgiframe();
  }
}

$(document).ready( function() {
  $(\'body\').click(
    function() {
      $(\'.dropout-box\')
        .filter(function() { return $(this).css(\'display\') != \'none\'; } )
        .hide();
    }
  );
  $(\'div.dropout-container div.drop-out-button\').each(initDropOutButton);
}
);
'; ?>


<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE'): ?>
<?php echo '
// Fix with positioning (z-indexes)
var i = 100;
$(document).ready(function(){
$(".products div.item").each(function() {
  $(this).css({\'background-color\' : $(this).css(\'background-color\'), \'z-index\' : i--});
});
change_width_iefix();
});
'; ?>


<?php if ($this->_tpl_vars['config']['UA']['version'] == "6.0"): ?>
<?php echo '
// Fix for displaying PNG images in IE6
$.event.add( window, \'load\', function() {
$(\'img.png-image\').each( function() {
  if (this.src && this.src.search(/\\.png($|\\?)/) != -1) {
    if (this.currentStyle.width == \'auto\')
      this.style.width = this.offsetWidth + \'px\';

    if (this.currentStyle.height == \'auto\')
      this.style.height = this.offsetHeight + \'px\';

    pngFix(this);
  }
});
});
'; ?>

<?php endif; ?>
<?php endif; ?>

<?php echo '
// Position:absolute elements will not move when window is resized 
// if a sibling contains float elements and a clear:both element
// https://bugzilla.mozilla.org/show_bug.cgi?id=442542
// FireFox 3.0+
if (navigator.userAgent.toLowerCase().search(/firefox\\/(3\\.\\d+)/i) != -1 && typeof(window.$) != \'undefined\') {
$.event.add( window, \'resize\', function() {
  var h = document.getElementById(\'header\');
  if (!h || $(h).css(\'position\') != \'absolute\')
    return;

  document.getElementById(\'header\').style.position = \'static\';
  setTimeout(
    function() {
      document.getElementById(\'header\').style.position = \'absolute\';
    },
  20);
});
}
'; ?>


var md = <?php echo ((is_array($_tmp=@$this->_tpl_vars['config']['Appearance']['delay_value'])) ? $this->_run_mod_handler('default', true, $_tmp, 10) : smarty_modifier_default($_tmp, 10)); ?>
*1000;

<?php echo '
$(document).ready( function() {

$(\'form\').not(\'.skip-auto-validation\').each(function() {
  applyCheckOnSubmit(this);
});

$(\'a.toggle-link\').live(
  \'click\',
  function(e) {
    $(\'#\' + $(this).attr(\'id\').replace(\'link\', \'box\')).toggle();
  }
);

$("#dialog-message").fadeIn(\'slow\').delay(md).fadeOut(\'slow\');

});
'; ?>


<?php if ($this->_tpl_vars['products'] != "" || $this->_tpl_vars['free_products'] != "" || $this->_tpl_vars['product'] != ""): ?>
<?php echo '
if (products_data == undefined) {
var products_data = [];
}
'; ?>

<?php endif; ?>

<?php $this->_smarty_vars['capture']['onload_js'] = ob_get_contents(); ob_end_clean(); ?>

<?php echo smarty_function_load_defer(array('file' => 'onload_js','direct_info' => $this->_smarty_vars['capture']['onload_js'],'type' => 'js','queue' => '1'), $this);?>


<?php if ($this->_tpl_vars['active_modules']['Product_Options'] != ""): ?>
<?php echo smarty_function_load_defer(array('file' => "modules/Product_Options/func.js",'type' => 'js'), $this);?>

<?php endif; ?>

<?php if ($this->_tpl_vars['products'] || $this->_tpl_vars['free_products']): ?>
<?php echo smarty_function_load_defer(array('file' => "js/check_quantity.js",'type' => 'js'), $this);?>

<?php if ($this->_tpl_vars['active_modules']['Feature_Comparison'] && ! $this->_tpl_vars['printable'] && $this->_tpl_vars['products_has_fclasses']): ?>
<?php echo smarty_function_load_defer(array('file' => "modules/Feature_Comparison/products_check.js",'type' => 'js'), $this);?>

<?php endif; ?>
<?php endif; ?>
