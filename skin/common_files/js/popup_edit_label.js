/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup edit label (webmaster mode)
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_edit_label.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var old_onclick;
var is_open = false;

function rememberXY() {
  if (!window.opener)
    return;

  if (localBFamily == 'MSIE') {
/*
    window.opener.defaultLabelWindowX = window.screenLeft;
    window.opener.defaultLabelWindowY = window.screenTop;
*/
  } else {
    window.opener.defaultLabelWindowX = window.screenX;
    window.opener.defaultLabelWindowY = window.screenY;
  } 
}

function restoreLabel() {
  if (!window.opener)
    return;

  if (document.lf && document.lf.val)
    document.lf.val.value = window.opener.xescape(window.opener.initialValue);

  if (window.opener.setLabel)
    set_label(window.opener.initialValue);
}

function getData() {
  if (!window.opener || !window.opener.xunescape || !document.lf) {
    window.close();
    return;
  }

  resizeWnd();

  if (document.lf && document.lf.val) {
    if (document.lf.val.focus)
      document.lf.val.focus();
    if (document.lf.val.select)
      document.lf.val.select();
  }

  if (document.getElementById('valEnb')) {
    old_onclick = document.getElementById('valEnb').onclick;
    document.getElementById('valEnb').onclick = function(e) {
      if (old_onclick)
        old_onclick();
      is_open = true;
      resizeWnd();
    }
  }

  if (document.getElementById('valDis')) {
    _old_onclick = document.getElementById('valDis').onclick;
    document.getElementById('valDis').onclick = function(e) {
      if (_old_onclick)
        _old_onclick();
      is_open = false;
      resizeWnd();
    }
  }

  window.focus();
}

function resizeWnd() {
  var tbl = document.getElementById("tbl");
  if (tbl) {
    var w = tbl.offsetWidth;
    var h = tbl.offsetHeight;
    window.innerWidth = w+23;
    window.innerHeight = h+20;
    window.resizeTo(w+33, h+160);
  }
}

function set_label(value) {
  if (localBrowser == "Chrome" || localBrowser == "Safari")
    window.opener.setLabel(value);
  else
    window.opener.setLabel();
}

function copyText() {
  var tmp = document.getElementById('val');
  if (is_open) {
    if (tmp && document.lf && document.lf.val)
      tmp.value = document.lf.val.value = editor_get_xhtml_body("val");
  }

  if (window.opener && window.opener.setLabel && isFunction(window.opener.setLabel))
    set_label(tmp.value);
}

