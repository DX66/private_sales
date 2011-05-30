/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Webmaster mode editor
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: editor.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/* $Id: editor.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $ */

function lmo(obj) {
  if (!obj || !obj.id)
    return false;

  if (!obj.onmouseout)
    obj.onmouseout = lmu;

  if (!obj.onclick)
    obj.onclick = lmc;

  // find template div or span
  var templates = grabTemplateNames(window.event.srcElement);
  setStatus(templates, obj.id);
  lastID = obj.id;
  window.event.srcElement.focus();
  var aElement = findAElement(window.event.srcElement);

  // check if there is Label dialog open
  if (aElement != null && (labelWindow == null || labelWindow.closed)) {
    // focus the link
    aElement.onkeypress = lkp;
    aElement.onmouseover = lamo;
    aElement.focus();
  }
  window.event.returnValue = true;
}

function grabTemplateNames(elem) {
  var templates = "";
  while (elem != null && elem.tagName.toUpperCase() != 'BODY') {
    if (elem.tagName.toUpperCase() == 'DIV' || elem.tagName.toUpperCase() == 'SPAN' || elem.tagName.toUpperCase() == 'TBODY') {
      if (elem.id != null && elem.id.indexOf('.tpl') > 0) {
        templates = elem.id.replace(/(\w)0(\w)/g, "$1/$2").replace(/\.tpl\d+/, ".tpl") + " > " + templates ;
      }
    }  
    elem = elem.parentElement;
  }
  return templates;
}

function lkp() {
  if (window.event.keyCode == 69 || window.event.keyCode == 101) { /* 'E', 'e' */
    if (lastID!='')
      showLabelForm(lastID);

    window.event.returnValue = true;

  } else if (window.event.keyCode == 67 || window.event.keyCode == 99 && window.clipboardData) { /* 'C', 'c' */
    clipboardData.setData('Text', lastID);
  }

}
function lmc() {
  if (findAElement(window.event.srcElement) != null) {
    return; /* inside <a> */
  }
  showLabelForm(this.id);
  window.event.returnValue = true;
}
function setLabel() {
  if (!labelWindow || !labelWindow.lf || !labelWindow.lf.val)
    return;

  // find all spans having onmousoOver=lom('labelWindow.lf.val.value');
  allElem = document.body.getElementsByTagName("SPAN");
  var v = labelWindow.lf.val.value ? labelWindow.lf.val.value : ("&lt;"+labelID+"&gt;");
  for (a = 0; a < allElem.length; a++) {
    if (allElem[a].onmouseover != null && allElem[a].id == labelID) {
      allElem[a].innerHTML = v;
      lng_labels[labelID] = labelWindow.lf.val.value ? xescape(v) : '';
    }
  }  
}

function showLabelForm(id) {
  if (labelWindow && !labelWindow.closed && labelWindow.close)
    labelWindow.close();
  if (defaultLabelWindowX == 0) {
    defaultLabelWindowX = window.screenLeft+100;
    defaultLabelWindowY = window.screenTop+100;
  }
  openLabelForm(id);
}

function copyName() {
  if (window.clipboardData)
    clipboardData.setData("Text", labelID);
  window.close();
}

function dmo(obj) {
  if (!obj)
    return false;

  if (!obj.onmouseout)
    obj.onmouseout = dmu;

  if (window.status == '') {
    window.status = grabTemplateNames(window.event.srcElement);
  }
}
