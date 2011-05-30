/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Webmaster mode, common functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: editor_common.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var lastID;
var defaultLabelWindowX = 0, defaultLabelWindowY = 0;
var labelWindow;
var labelID;
var initialValue;

function xescape(str) {
  if (str.constructor != String)
    return '';

  return str.replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

function xunescape(str) {
  if (str.constructor != String)
    return '';

  return str.replace(/&amp;/g, "&")
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">");
}

function setStatus(templates, id) {
  window.status = templates + id ;
}

function dmu() {
  window.status = '';
}

function openLabelForm(id) {
  labelID = id;
  if (typeof(lng_labels) == 'undefined')
    return;

  var labelText = lng_labels[labelID] = document.getElementById(id).innerHTML;
  initialValue = xunescape(labelText);

  var as_tarea = (labelText.length > 40 || labelText.match(/[\n\r]/)) ? "&tarea=Y" : "";
  labelWindow = window.open(
    xcart_web_dir + "/popup_edit_label.php?current_area=" + current_area + "&id=" + labelID + "&_l=" + store_language + as_tarea,
    "labelWnd",
    "width=450, height=60, resizable=yes, left=" + defaultLabelWindowX + ", top=" + defaultLabelWindowY
  );
}

function lamo(e) {
  if (!e)
    e = event;

  setStatus(grabTemplateNames(e.currentTarget), lastID);
}

function lmu(e) {
  lastID = window.status = '';
  if (!e)
    event.returnValue = true;
}

function findAElement(elem) {
  while ((elem.parentNode.tagName.toUpperCase() != 'A' || elem.parentNode.href == '') && elem.parentNode.tagName.toUpperCase() != 'BODY')
    elem = elem.parentNode;

  if (elem.parentNode.tagName.toUpperCase() != 'A')
    return null;

  return elem.parentNode;
}

function tmo(tmplt, obj) {
    if (!obj.onmouseout) {
      obj.onmouseout = function() {
        markTemplate(tmplt, true);
      }
    }
    
    markTemplate(tmplt, false);
}

/*
    make a border around the template
*/
function markTemplate(tmplt, remove) {

    /* find all spans and divs having name=tmplt */
    var list, j, i, a, allelem;
    var tags = ['SPAN', "DIV", "TBODY"];
    var reg = new RegExp("^" + tmplt.replace(/\./, "\."), "");
    var borderStyle = remove ? "0px none" : "1px solid black";

    var d = document.body ? document.body : document.documentElement;

    for (i = 0; i < tags.length; i++) {
        allelem = d.getElementsByTagName(tags[i]);

        for (a = 0; a < allelem.length; a++) {

            e = allelem[a];
            if (!e.id || e.id.search(reg) == -1)
                continue;

            if (tags[i] == 'TBODY') {

                list = e.getElementsByTagName('TD');
                for (j = 0; j < list.length; j++)
                  list.style.border = borderStyle;

                list = e.getElementsByTagName('TH');
                for (j = 0; j < list.length; j++)
                  list.style.border = borderStyle;

            } else {
                e.style.border = borderStyle;
            }
        }
    }
}

