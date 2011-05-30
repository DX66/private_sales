/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Multirow functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: multirow.js,v 1.3.2.1 2010/09/28 12:05:03 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Add new row (first row onclick event handler)
 */
function add_inputset(name, obj, isLined, handler) {

  if (!name)
    return false;

  var buttonTD = obj;
    while (buttonTD.tagName.toUpperCase() != 'TD' && buttonTD.parentNode)
        buttonTD = buttonTD.parentNode;

  if (buttonTD.tagName.toUpperCase() != 'TD')
    return false;

  if (!buttonTD.inheritedRows) {
    buttonTD.inheritedRows = [];
  }

  buttonTD.isLined = isLined;
  buttonTD.handler = handler;

  return add_inputset_row(name, buttonTD, buttonTD.parentNode);
}

/**
 * Add new row
 */
function add_inputset_row(name, buttonTD, lastTR) {
  var regexp = new RegExp('^'+name+'_box', 'ig');

  /* Get last cloned row */
  var maxI = -1;
  if (buttonTD.inheritedRows.length > 0) {
    for (var i in buttonTD.inheritedRows) {
      if (hasOwnProperty(buttonTD.inheritedRows, i))
        maxI = i;
    }
  }

  if (!lastTR)
    lastTR = (maxI >= 0) ? buttonTD.inheritedRows[maxI] : buttonTD.parentNode;
  var origTable = lastTR.parentNode.parentNode;
  var origTR = lastTR;

  /* Clone row */
  maxI++;
  lastTR = origTable.insertRow(lastTR.rowIndex+1);
  buttonTD.inheritedRows[maxI] = lastTR;

  /* Copy row attributes */
  for (var x = 0; x < origTR.attributes.length; x++) {
    if(!origTR.attributes[x].specified)
      continue;
    var newAttr = document.createAttribute(origTR.attributes[x].name);
    newAttr.value = origTR.attributes[x].value
    lastTR.attributes.setNamedItem(newAttr);
  }

  lastTR.buttonTD = buttonTD;
  lastTR.mark = name;
  lastTR.inheritedRowIndex = maxI;
  lastTR.cssText = origTR.cssText;

  for (var x = 0; x < origTR.cells.length; x++) {
    if (origTR.cells[x].id.search(regexp) == -1)
      continue;

    /* Clone cell */
    var curTD = lastTR.appendChild(origTR.cells[x].cloneNode(true));
    curTD.id = name+'_box_'+x+'_'+lastTR.inheritedRowIndex;

    /* Change clone element name (in clone cell) */
    for (var y = 0; y < curTD.childNodes.length; y++) {
      var elm = curTD.childNodes[y];
      if (!elm.tagName)
        continue;

      var tName = elm.tagName.toUpperCase();
      if (tName == 'INPUT' || tName == 'SELECT' || tName == 'TEXTAREA') {
        if (elm.name.search(/\[\]$/) != -1) {
          elm.name = elm.name.replace(/\[[0-9]+\]\[\]$/, '['+(lastTR.inheritedRowIndex+1)+'][]');
        } else {
          elm.name = elm.name.replace(/\[[0-9]+\]$/, '['+(lastTR.inheritedRowIndex+1)+']');
        }

        /* Clear cloned element content if noCloneContent option is enabled for this multirow inputset */
        if (
          typeof(multirowInputSets) != 'undefined' 
          && typeof(multirowInputSets[name]) != 'undefined'
          && multirowInputSets[name].noCloneContent) {
          if ((tName == 'INPUT' && elm.type == 'text') || tName == 'TEXTAREA')
            elm.value = '';
        }
      }
    }
  }

  /* Add service cell (with + / - buttons) */
  curTD = lastTR.insertCell(-1);
  curTD.noWrap = true;
  if (!window.lbl_remove_row)
    lbl_remove_row = "Remove row";
  if (!window.lbl_add_row)
    lbl_add_row = "Add row";

  if (window.inputset_plus_img && window.inputset_minus_img)
    curTD.innerHTML = '<img src="'+inputset_plus_img+'" alt="'+lbl_add_row+'" onclick="javascript: add_inputset_subrow(this);" style="cursor: pointer;" />&nbsp;&nbsp;<img src="'+inputset_minus_img+'" alt="'+lbl_remove_row+'" onclick="javascript: remove_inputset(this);" style="cursor: pointer;" />';
  else
    curTD.innerHTML = '<a href="javascript:void(0);" onclick="javascript: add_inputset_subrow(this);">'+lbl_add_row+'</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript: remove_inputset(this);">'+lbl_remove_row+'</a>';

  lined_inputset(buttonTD);

  if (buttonTD.handler)
    buttonTD.handler(1, lastTR);

  return lastTR;
}

/**
 * Add new row (onclick event handler)
 */
function add_inputset_subrow(tr) {
    while (tr.tagName.toUpperCase() != 'TR' && tr.parentNode)
        tr = tr.parentNode;

  if (tr.tagName.toUpperCase() != 'TR')
    return false;

  add_inputset_row(tr.mark, tr.buttonTD, tr);
}

/**
 * Remove row from rows set
 */
function remove_inputset(tr) {
  while (tr.tagName.toUpperCase() != 'TR' && tr.parentNode)
    tr = tr.parentNode;

  if (tr.tagName.toUpperCase() != 'TR')
    return false;

  if (!tr.buttonTD.inheritedRows[tr.inheritedRowIndex])
    return false;

  tr.parentNode.parentNode.deleteRow(tr.rowIndex);
  lined_inputset(tr.buttonTD);
  tr.buttonTD.inheritedRows[tr.inheritedRowIndex] = null;
  if (tr.buttonTD.handler)
    tr.buttonTD.handler(1);

  delete tr;

  return true;
}

/**
 * Display rows set as lined
 */
function lined_inputset(buttonTD) {
  if (!buttonTD.isLined || buttonTD.inheritedRows.length == 0)
    return false;

  var origTable = buttonTD;
  while (origTable.tagName.toUpperCase() != 'TABLE' && origTable.parentNode)
    origTable = origTable.parentNode;

  if (origTable.tagName.toUpperCase() != 'TABLE')
    return false;

  var maxRowIndex = buttonTD.parentNode.rowIndex;
  for (var i in buttonTD.inheritedRows) {
    if (hasOwnProperty(buttonTD.inheritedRows, i) && buttonTD.inheritedRows[i] && maxRowIndex < buttonTD.inheritedRows[i].rowIndex)
      maxRowIndex = buttonTD.inheritedRows[i].rowIndex;
  }

  var flag = true;
  for (var i = 0; i < origTable.rows.length; i++) {
    if (origTable.rows[i].rowIndex > buttonTD.parentNode.rowIndex && origTable.rows[i].rowIndex <= maxRowIndex) {
      origTable.rows[i].className = flag ? 'TableSubHead' : '';
      flag = !flag;
    }
  }
}

/**
 * Add row with preset data
 */
function add_inputset_preset(name, obj, isLined, preset) {
  var tr = add_inputset(name, obj, isLined);
  if (!tr)
    return false;

  for (var p = 0; p < preset.length; p++) {
    var elm = false;
    for (var i = 0; i < tr.cells.length && !elm; i++) {

      /* Get element */
      var elm = add_inputset_search_element(tr.cells[i], preset[p].regExp);
      if (!elm)
        continue;

      var tName = elm.tagName.toUpperCase();
      if (tName == 'INPUT' && (elm.type == 'text' || elm.type == 'hidden')) {
        elm.value = preset[p].value;

      } else if (tName == 'INPUT' && elm.type == 'checkbox') {
        elm.checked = preset[p].value;

      } else if (tName == 'INPUT' && elm.type == 'radio') {
        var elms = elm.form.elements[elm.name];
        if (elms) {
          for (var y = 0; y < elms.length; y++)
            elms[y].checked =  (elms[y].value == preset[p].value);
        }

      } else if (tName == 'SELECT') {
        for (var y = 0; y < elm.options.length; y++)
          if (elm.options[y].value == preset[p].value) {
            elm.options[y].selected = true;
            break;
          }

      } else if (tName == 'TEXTAREA') {
        elm.value = preset[p].value;
      }
    
    }
  }
}

/**
 * Search element by name (RegExp) recursive
 */
function add_inputset_search_element(parent, regExp) {
  if (parent.childNodes.length == 0)
    return false;

  for (var i = 0; i < parent.childNodes.length; i++) {
    if (!parent.childNodes[i].tagName || !parent.childNodes[i].name)
      continue;

    var tName = parent.childNodes[i].tagName.toUpperCase();
    if ((tName == 'INPUT' || tName == 'SELECT' || tName == 'TEXTAREA') && parent.childNodes[i].name.search(regExp) != -1) {
      return parent.childNodes[i];
    }

    if (parent.childNodes[i].parentChilds && parent.childNodes[i].parentChilds.length > 0) {
      var r = add_inputset_search_element(parent.childNodes[i], regExp);
      if (r)
        return r;
    }
  }

  return false;
}
