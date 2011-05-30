/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Webmaster mode editor
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: editorns.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function lmo(obj, event) {
  if (!obj || !obj.id)
    return false;

  if (!obj.onmouseout)
    obj.onmouseout = lmu;

  if (!obj.onclick)
    obj.onclick = lmc;

  // find template div or span
  var templates = grabTemplateNames(event.currentTarget);
  setTimeout("setStatus(\""+templates+"\", \""+obj.id+"\")", 20);
  lastID = obj.id;
  var aElement = findAElement(event.currentTarget);
  // check if there is Label dialog open
  if (aElement != null && (labelWindow == null || labelWindow.closed)) {
    aElement.addEventListener("keypress", lkp, true);
    // focus the link
    aElement.focus();
  }
}

function grabTemplateNames(elem) {
  var templates = "", tst=""
  while (elem != null) {
    if (elem.tagName != null && (elem.tagName.toUpperCase() == 'DIV' || elem.tagName.toUpperCase() == 'SPAN' || elem.tagName.toUpperCase() == 'TBODY')) {
      var attr = elem.attributes.getNamedItem("id");
      if (attr != null && attr.name != null && attr.value.indexOf('.tpl') > 0)
        templates = attr.value.replace(/(\w)0(\w)/g, "$1/$2").replace(/\.tpl\d+/, ".tpl") + " > " + templates;
    }  
    elem = elem.parentNode;
  }
  return templates;
}

function lkp(event){
  if (event.charCode == 69 || event.charCode == 101) { /* 'E', 'e' */
    if (lastID!='')
      showLabelForm(lastID);
  }
}

function lmc(event) {
  if (findAElement(event.currentTarget) == null)
    showLabelForm(this.id);
}

function setLabel(value) {
  if (!labelWindow || !labelWindow.document || !labelWindow.document.lf || !labelWindow.document.lf.val)
    return;

  // find all spans having onMouseOver=lom('labelWindow.lf.val.value')
  allElem = document.documentElement.getElementsByTagName("SPAN");

  var v;
  if (value != undefined)
    v = value;
  else
    v = labelWindow.document.lf.val.value ? labelWindow.document.lf.val.value : ("&lt;"+labelID+"&gt;");

  for (var a = 0; a < allElem.length; a++) {
    if (allElem[a].onmouseover && allElem[a].id == labelID) {
      allElem[a].innerHTML = v;
      lng_labels[labelID] = labelWindow.document.lf.val.value ? xescape(v) : '';
    }
  }  
}

function showLabelForm(id) {
  if (labelWindow != null && !labelWindow.closed)
    labelWindow.close();
  if (defaultLabelWindowX == 0) {
    defaultLabelWindowX = window.screenX+100;
    defaultLabelWindowY = window.screenY+100;
  }
  openLabelForm(id);
}

function dmo(obj, event) {
  if (!obj)
    return false;

  if (!obj.onmouseout)
    obj.onmouseout = dmu;

  if (window.status == '')
    window.status = grabTemplateNames(event.currentTarget);
}
