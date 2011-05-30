<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/category_selector.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/category_selector.tpl', 66, false),array('modifier', 'amp', 'main/category_selector.tpl', 73, false),)), $this); ?>
<?php func_load_lang($this, "main/category_selector.tpl","lbl_please_select_category"); ?><script type="text/javascript">
//<![CDATA[
var isNN = document.layers ? true : false;
var isIE = document.all ? true : false;
var mouseX;
var mouseY;

init();

<?php echo '

function init() {
  if ( isNN )
    document.captureEvents(Event.MOUSEMOVE)
    document.onmousemove = handleMouseMove;
}

function handleMouseMove(evt) {
  mouseX = !isIE ? evt.pageX : window.event.clientX;
  mouseY = !isIE ? evt.pageY : window.event.clientY;

  return true;
}

function hideTitle(id) {
  var layer = document.getElementById(id);
  layer.style.display = "none"; 
}

function showTitle(value, position) {
  if (value.length < 40) {
    return;
  }
  if (!isIE) {
    var layer = document.getElementById(\'layer\');
    setTimeout("hideTitle(\'layer\');", 3000);
    layer.innerHTML = value;
  } else {
    var layer = document.getElementById(\'iframe\');
    setTimeout("hideTitle(\'iframe\');", 3000);
    layer.style.width = value.length * 6;
    layer.contentWindow.document.body.innerHTML = value;
    layer.contentWindow.document.body.style.fontSize = "12px";
    layer.contentWindow.document.body.style.marginLeft = "0px";
    layer.contentWindow.document.body.style.marginTop = "0px";
    layer.contentWindow.document.body.style.background = "#FFFBD3";
  }
    layer.style.display = "";
    if (position == \'left\') {
      var length = layer.style.width.substr(0, layer.style.width.length - 2);
      layer.style.left = (mouseX - length) + "px";
    } else if (position == \'right\') {
      layer.style.left = mouseX+"px";
    }
    layer.style.top = mouseY+"px";
}
'; ?>

//]]>
</script>
<div id='layer' style="display:none; position:absolute; background-color:#FFFBD3; border:1px solid #000000;left:0px;top:0px;z-index: 10;"></div>
<iframe  scrolling="no" frameborder="0" style="position:absolute;top:0px;left:0px;display:none;width:100px;height:14px;" id="iframe" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif"></iframe>
<select name="<?php echo ((is_array($_tmp=@$this->_tpl_vars['field'])) ? $this->_run_mod_handler('default', true, $_tmp, 'categoryid') : smarty_modifier_default($_tmp, 'categoryid')); ?>
"<?php echo $this->_tpl_vars['extra']; ?>
 onchange="javascript: showTitle(this.options[this.selectedIndex].text, 'right');"<?php if ($this->_tpl_vars['size']): ?> size="<?php echo $this->_tpl_vars['size']; ?>
"<?php endif; ?>>
<?php if ($this->_tpl_vars['display_empty'] == 'P'): ?>
  <option value=""><?php echo $this->_tpl_vars['lng']['lbl_please_select_category']; ?>
</option>
<?php elseif ($this->_tpl_vars['display_empty'] == 'E'): ?>
  <option value="">&nbsp;</option>
<?php endif; ?>
<?php $_from = $this->_tpl_vars['allcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['catid'] => $this->_tpl_vars['c']):
?>
  <option value="<?php echo $this->_tpl_vars['catid']; ?>
"<?php if ($this->_tpl_vars['categoryid'] == $this->_tpl_vars['catid']): ?> selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['c'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select>