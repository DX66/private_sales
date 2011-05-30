/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Common JavaScript variables and functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage ____sub_package____
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: common.js,v 1.13.2.3 2011/01/10 13:12:10 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Enviroment identificator
 */
var localIsDOM = document.getElementById ? true: false;
var localIsJava = navigator.javaEnabled();
var localIsStrict = document.compatMode == 'CSS1Compat';
var localPlatform = navigator.platform;
var localVersion = "0";
var localBrowser = "";
var localBFamily = "";
var isHttps = false;

if (window.opera && localIsDOM) {
  localBFamily = localBrowser = "Opera";
  if (navigator.userAgent.search(/^.*Opera.([\d.]+).*$/) != - 1) localVersion = navigator.userAgent.replace(/^.*Opera.([\d.]+).*$/, "$1");
  else if (window.print) localVersion = "6";
  else localVersion = "5";

} else { 

  if (document.all && document.all.item)
    localBFamily = localBrowser = 'MSIE';
}

if (navigator.appName == "Netscape") {

  localBFamily = "NC";

  if (!localIsDOM) {
    localBrowser = 'Netscape';
    localVersion = navigator.userAgent.replace(/^.*Mozilla.([\d.]+).*$/, "$1");

    if (localVersion != '') localVersion = "4";

  }
  else if (navigator.userAgent.indexOf("Chrome") >= 0) localBrowser = 'Chrome';
  else if (navigator.userAgent.indexOf("Safari") >= 0) localBrowser = 'Safari';
  else if (navigator.userAgent.indexOf("Netscape") >= 0) localBrowser = 'Netscape';
  else if (navigator.userAgent.indexOf("Firefox") >= 0) localBrowser = 'Firefox';
  else localBrowser = 'Mozilla';

}

if (navigator.userAgent.indexOf("MSMSGS") >= 0)
  localBrowser = "WMessenger";
else if (navigator.userAgent.indexOf("e2dk") >= 0)
  localBrowser = "Edonkey";
else if (navigator.userAgent.indexOf("Gnutella") + navigator.userAgent.indexOf("Gnucleus") >= 0)
  localBrowser = "Gnutella";
else if (navigator.userAgent.indexOf("KazaaClient") >= 0)
  localBrowser = "Kazaa";

if (localVersion == '0' && localBrowser != '') {
  var rg = new RegExp("^.*" + localBrowser + ".([\\d.]+).*$");
  localVersion = navigator.userAgent.replace(rg, "$1");
}

var localIsCookie = ((localBrowser == 'Netscape' && localVersion == '4') 
  ? (document.cookie != '') 
  : navigator.cookieEnabled);

var isHttps = document.location.protocol == "https:";

function change_antibot_image(id) {
  var image = document.getElementById(id);
  if (image) {
    var src = xcart_web_dir + "/antibot_image.php?tmp=" + Math.random() + "&section=" + id + "&regenerate=Y";
    setTimeout(
    function() {
      image.src = src;
    },
    200);
  }
  $('#antibot_input_str', $(image).parents('form')[0]).val('');
}

/**
 * Get real inner width (jsel- JQuery selector)
 */
function getRealWidth(jsel) {
  var sw = $(jsel).attr('scrollWidth');

  if ($.browser.opera) 
    return sw;

  var pl = parseInt($(jsel).css('padding-left'));

  if (!isNaN(pl)) sw -= pl;

  var pr = parseInt($(jsel).css('padding-right'));

  if (!isNaN(pr)) 
    sw -= pr;

  return sw;
}

/**
 * Show note next to element
 */
function showNote(id, next_to) {
  if ( typeof showNote.isReadyToShow == 'undefined' ) {
      showNote.isReadyToShow = true;
  }

  if (
    showNote.isReadyToShow 
    && $('#' + id).css('display') == 'none'
  ) {
    showNote.isReadyToShow = false;

    var div = $('#' + id).get();
    $('#' + id).remove();
    $('body').append(div);

    $('#' + id).show();

    var sw = getRealWidth('#' + id);

    $('#' + id).css('left', $(next_to).offset().left + $(next_to).width() + 'px');
    $('#' + id).css('top', $(next_to).offset().top + 'px');

    if (sw > $('#' + id).width()) { 
      $('#' + id).css('width', sw + 'px');
    }
    showNote.isReadyToShow = true;
  }
}

/**
 * Find element by classname
 */
function getElementsByClassName(clsName) {
  var elem, cls;
  var arr = [];
  var elems = document.getElementsByTagName("*");

  for (var i = 0; (elem = elems[i]); i++) {
    if (elem.className == clsName) {
      arr[arr.length] = elem;
    }
  }

  return arr;
}

function getProperDimensions(old_x, old_y, new_x, new_y, crop) {

  if (old_x <= 0 || old_y <= 0 || (new_x <= 0 && new_y <= 0) || (crop && old_x <= new_x && old_y <= new_y)) 
    return [old_x, old_y];

  var k = 1;

  if (new_x <= 0) {
    k = (crop && old_y <= new_y) ? 1: new_y / old_y;

  } else if (new_y <= 0) {
    k = (crop && old_x <= new_x) ? 1: new_x / old_x;

  } else {

    var _kx = new_x / old_x;
    var _ky = new_y / old_y;

    k = crop ? Math.min(_kx, _ky, 1) : Math.min(_kx, _ky);
  }

  return [round(k * old_x), round(k * old_y)];

}
/**
 * Opener/Closer HTML block
 */
function visibleBox(id, skipOpenClose) {
  elm1 = document.getElementById("open" + id);
  elm2 = document.getElementById("close" + id);
  elm3 = document.getElementById("box" + id);

  if (!elm3) return false;

  if (skipOpenClose) {
    elm3.style.display = (elm3.style.display == "") ? "none": "";

  } else if (elm1) {
    if (elm1.style.display == "") {
      elm1.style.display = "none";

      if (elm2) elm2.style.display = "";

      elm3.style.display = "none";
      var class_objs = getElementsByClassName('DialogBox');
      for (var i = 0; i < class_objs.length; i++) {
        class_objs[i].style.height = "1%";
      }

    } else {
      elm1.style.display = "";
      if (elm2) elm2.style.display = "none";

      elm3.style.display = "";
    }
  }

  return true;
}

function switchVisibleBox(id) {
  var box = document.getElementById(id);
  var plus = document.getElementById(id + '_plus');
  var minus = document.getElementById(id + '_minus');
  if (!box || ! plus || ! minus) return false;

  if (box.style.display == 'none') {
    box.style.display = '';
    plus.style.display = 'none';
    minus.style.display = '';

  } else {
    box.style.display = 'none';
    minus.style.display = 'none';
    plus.style.display = '';
  }

  return true;
}

/**
 * URL encode
 */
function urlEncode(url) {
  return url.replace(/\s/g, "+").replace(/&/, "&amp;").replace(/"/, "&quot;")
}

/**
 * Math.round() wrapper
 */
function round(n, p) {
  if (isNaN(n)) n = parseFloat(n);

  if (!p || isNaN(p)) return Math.round(n);

  p = Math.pow(10, p);
  return Math.round(n * p) / p;
}

/**
 * Price format
 */
function price_format(price, thousand_delim, decimal_delim, precision, currency) {

  thousand_delim = (arguments.length > 1 && thousand_delim !== false) 
    ? thousand_delim 
    : number_format_th;

  decimal_delim = (arguments.length > 2 && decimal_delim !== false) 
    ? decimal_delim 
    : number_format_dec;

  precision = (arguments.length > 3 && precision !== false) 
    ? precision 
    : number_format_point;

  currency = (arguments.length > 4 && currency !== false) 
    ? currency_format 
    : "x";

  if (precision > 0) {
    precision = Math.pow(10, precision);
    price = Math.round(price * precision) / precision;
    var top = Math.floor(price);
    var bottom = Math.round((price - top) * precision) + precision;
  } else {
    var top = Math.round(price);
    var bottom = 0;
  }

  top = top + "";
  bottom = bottom + "";
  var cnt = 0;
  for (var x = top.length; x >= 0; x--) {
    if (cnt % 3 == 0 && cnt > 0 && x > 0) top = top.substr(0, x) + thousand_delim + top.substr(x, top.length);
    cnt++;
  }

  return currency.replace("x", (bottom > 0) ? (top + decimal_delim + bottom.substr(1, bottom.length)) : top);
}

/**
 * Substitute
 */
function substitute(lbl) {
  var rg;
  for (var x = 1; x < arguments.length; x += 2) {
    if (arguments[x] && arguments[x + 1]) {
      lbl = lbl.replace(new RegExp("\\{\\{" + arguments[x] + "\\}\\}", "gi"), arguments[x + 1])
               .replace(new RegExp('~~' + arguments[x] + '~~', "gi"), arguments[x + 1]);
    }
  }
  return lbl;
}

function getWindowOutWidth(w) {
  if (!w) 
    w = window;

  return localBFamily == "MSIE" ? w.document.body.clientWidth: w.outerWidth;
}

function getWindowOutHeight(w) {
  if (!w) 
    w = window;

  return localBFamily == "MSIE" ? w.document.body.clientHeight: w.outerHeight;
}

function getWindowWidth(w) {
  if (!w) 
    w = window;

  return localBFamily == "MSIE" ? w.document.body.clientWidth: w.innerWidth;
}

function getWindowHeight(w) {
  if (!w) 
    w = window;

  return localBFamily == "MSIE" ? w.document.body.clientHeight: w.innerHeight;
}

function getDocumentHeight(w) {
  if (!w) 
    w = window;

  return Math.max(w.document.documentElement.scrollHeight, w.document.body.scrollHeight);
}

function getDocumentWidth(w) {
  if (!w) 
    w = window;

  return Math.max(w.document.documentElement.scrollWidth, w.document.body.scrollWidth);
}

/**
 * Check list of checkboxes
 */
function checkMarks(form, reg, lbl) {
  var is_exist = false;

  if (!form || form.elements.length == 0) 
    return true;

  for (var x = 0; x < form.elements.length; x++) {
    if (form.elements[x].name.search(reg) == 0 && form.elements[x].type == 'checkbox' && ! form.elements[x].disabled) {
      is_exist = true;

      if (form.elements[x].checked) 
        return true;
    }
  }

  if (!is_exist) 
    return true;

  if (lbl) {
    alert(lbl);

  } else if (lbl_no_items_have_been_selected) {
    alert(lbl_no_items_have_been_selected);

  }

  return false;
}

/**
 * Submit form with specified value of 'mode' parmaeters
 */
function submitForm(formObj, formMode, e) {
  if (!e && typeof(window.event) != 'undefined') e = event;

  if (e) {
    if (e.stopPropagation) e.stopPropagation();
    else e.cancelBubble = true;
  }

  if (!formObj) 
    return false;

  if (formObj.tagName != "FORM") {
    if (!formObj.form) 
      return false;

    formObj = formObj.form;
  }

  if (formObj.mode) formObj.mode.value = formMode;

  if (typeof(window.$) != 'undefined') {
    var r = $(formObj).triggerHandler('submit');
    if (r === false) 
      return false;
  }

  return formObj.submit();
}

/**
 * Convert number from current format
 * (according to 'Input and display format for floating comma numbers' option)
 * to float number
 */
function convert_number(num) {
  var regDec = new RegExp(reg_quote(number_format_dec), "gi");
  var regTh = new RegExp(reg_quote(number_format_th), "gi");
  var pow = Math.pow(10, parseInt(number_format_point));

  num = parseFloat(num.replace(" ", "").replace(regTh, "").replace(regDec, "."));
  return Math.round(num * pow) / pow;
}

/**
 * Check string as number
 * (according to 'Input and display format for floating comma numbers' option)
 */
function check_is_number(num) {
  var regDec = new RegExp(reg_quote(number_format_dec), "gi");
  var regTh = new RegExp(reg_quote(number_format_th), "gi");

  num = num.replace(" ", "").replace(regTh, "").replace(regDec, ".");

  return (num.search(/^[+-]?[0-9]+(\.[0-9]+)?$/) != - 1);
}

/**
 * Qutation for RegExp class
 */
function reg_quote(s) {
  return s.replace(/\./g, "\\.").replace(/\//g, "\\/").replace(/\*/g, "\\*").replace(/\+/g, "\\+").replace(/\[/g, "\\[").replace(/\]/g, "\\]");
}

function setCookie(name, value, path, expires, domain) {
  if (typeof(expires) == 'object') {
    try {
      var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      if (days[expires.getDay()] && months[expires.getMonth()]) expires = days[expires.getDay()] + " " + expires.getDate() + "-" + months[expires.getMonth()] + "-" + expires.getFullYear() + " " + expires.getHours() + ":" + expires.getMinutes() + ":" + expires.getSeconds() + " GMT";
    } catch(e) {}
  }

  if (typeof(expires) != 'string') expires = false;
  if (typeof(domain) != 'string') domain = false;

  document.cookie = name + "=" + escape(value) + (expires ? "; expires=" + expires: "") + (path ? "; path=" + path: "") + (domain ? "; domain=" + domain: "");
}

function getCookie(name) {
  if (document.cookie.length > 0) {
    start = document.cookie.indexOf(name + "=");
    if (start != - 1) {
      start = start + name.length + 1;
      end = document.cookie.indexOf(";", start);
      if (end == - 1) end = document.cookie.length;

      return unescape(document.cookie.substring(start, end));
    }
  }

  return false;
}

function deleteCookie(name) {
  document.cookie = name + "=0; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

/**
 * Clone object
 */
function cloneObject(orig) {
  var r = {};
  for (var i in orig) {
    if (hasOwnProperty(orig, i)) r[i] = orig[i];
  }

  return r;
}

/**
 * getElementById() wrapper
 */
function _getById(id) {
  if (typeof(id) != 'string' || ! id) return false;

  var obj = document.getElementById(id);
  if (obj && obj.id != id) {
    obj = false;
    for (var i = 0; i < document.all.length && obj === false; i++) {
      if (document.all[i].id == id) obj = document.all[i];
    }
  }

  return obj;
}

// undefined or not
function isset(obj) {
  return typeof(obj) != 'undefined' && obj !== null;
}

// Check - variable is function or not
function isFunction(f) {
  return (typeof(f) == 'function' || (typeof(f) == 'object' && (f + "").search(/\s*function /) === 0));
}

// Get text length without \r
function getPureLength(text) {
  return (text && text.replace) ? text.replace(new RegExp("\r", "g"), '').length: - 1;
}

// Ge text area selection limits
function getTASelection(t) {
  if (document.selection) {
    t.focus();
    var sel1 = document.selection.createRange();
    var sel2 = sel1.duplicate();
    sel2.moveToElementText(t);
    var selText = sel1.text;
    var c = String.fromCharCode(1);
    sel1.text = c;
    var index = sel2.text.indexOf(c);
    t.selectionStart = getPureLength((index == - 1) ? sel2.text: sel2.text.substring(0, index));
    t.selectionEnd = getPureLength(selText) + t.selectionStart;
    sel1.moveStart('character', - 1);
    sel1.text = selText;
  }

  return [t.selectionStart, t.selectionEnd];
}

// Insert string to text area to current position
function insert2TA(t, str) {
  if (!t) return false;

  var pos = getTASelection(t);
  var p;
  if (!isNaN(pos[0])) {
    t.value = t.value.substr(0, pos[0]) + str + t.value.substr(pos[0]);
    p = pos[0];

  } else {
    p = getPureLength(t.value);
    t.value += str;
  }

  setTACursorPos(t, p);

  return p;
}

// Set cursor pointer to specified postion for text area 
function setTACursorPos(t, begin, end) {
  if (!t || ! t.tagName || t.tagName.toUpperCase() != 'TEXTAREA') 
    return false;

  if (isNaN(begin)) {
    begin = 0;

  } else if (getPureLength(t.value) < begin) {
    begin = getPureLength(t.value);
    end = begin;
  }

  if (isNaN(end)) end = begin;

  if (document.selection) {
    var sel = t.createTextRange();
    sel.collapse(true);
    sel.moveStart('character', begin);
    sel.moveEnd('character', end - begin);
    sel.select();

  } else if (!isNaN(t.selectionStart)) {
    t.selectionStart = begin;
    t.selectionEnd = end;
  }

  if (t.focus) t.focus();

  return true;
}

/**
 * Position functions
 */
function posGetPageOffset(o) {
  var l = 0;
  var t = 0;
  do {
    l += o.offsetLeft;
    t += o.offsetTop;
  } while ((o = o.offsetParent));
  return {
    left: l,
    top: t
  };
}

function getMethod(method, obj) {
  var args = [];
  for (var i = 2; i < arguments.length; i++)
  args[args.length] = arguments[i];

  if (!obj) obj = window;

  return function() {
    if (!isFunction(method)) method = obj[method];

    return method.apply ? method.apply(obj, args) : method();
  }
}

function lockForm(form) {
  if (form.locked) 
    return false;

  form.locked = true;

  setTimeout(
  function() {
    form.locked = false;
  },
  1000);

  return true;
}

function getPopupControl(elm) {
  var e = elm;
  while (e && e.tagName && ! e._popupControl)
  e = e.parentNode;

  return (e && e._popupControl) ? e._popupControl: false;
}

function parse_url(url) {
  if (!url || url.constructor != String) 
    return false;

  var m = url.match(/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/);
  if (!m) 
    return false;

  var res = {
    scheme: m[2],
    host: m[4],
    path: m[5],
    query: m[7],
    fragment: m[9]
  };

  if (res.host) {
    m = res.host.match(/^(?:([^:]+):)?([^@]+)@(.+)$/);
    if (m) {
      res.host = m[3];
      res.user = m[1] ? m[1] : m[2];
      res.password = m[1] ? m[2] : false;
    }
  }

  return res;
}

var xxx = 0;
function pngFix(elm) {
  if (!elm || ! elm.tagName || ! $.browser.msie || parseFloat($.browser.version) >= 7 || elm.tagName.toUpperCase() != 'IMG') 
    return false;

  var src = elm.src.replace(/\(/g, '%28').replace(/\)/g, '%29');
  elm.src = images_dir + '/spacer.gif';
  elm.style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + src.replace(/"/, '\"') + '",sizingMethod="scale")';

  return true;
}

function getImgSrc(elm) {

  if (!elm || ! elm.tagName || elm.tagName.toUpperCase() != 'IMG' || ! elm.src) 
    return false;

  if ($.browser.msie && elm.src.search(/\/spacer\.gif$/) != - 1 && elm.filters['DXImageTransform.Microsoft.AlphaImageLoader']) 
    return elm.filters['DXImageTransform.Microsoft.AlphaImageLoader'].src;

  return elm.src;
}

function isPngFix(elm) {
  return $.browser.msie && elm && elm.tagName && elm.tagName.toUpperCase() == 'IMG' && elm.src && elm.src.search(/\/spacer\.gif$/) != - 1 && elm.filters['DXImageTransform.Microsoft.AlphaImageLoader'];
}

function extend(c, p) {
  var f = function() {}
  f.prototype = p.prototype;
  c.prototype = new f();
  c.prototype.constructor = c;
  c.superclass = p.prototype;
}

function hasOwnProperty(obj, prop) {
  if (typeof(obj) != 'undefined' && Object.prototype.hasOwnProperty) 
    return obj.hasOwnProperty(prop);

  return typeof(obj[prop]) != 'undefined' && obj.constructor.prototype[prop] !== obj[prop];
}

var hint_timer = new Array();

function skipDefaultValue(form) {
  $('input.default-value', form).each(function() {
    this.value = '';
  })
  return true;
}

function initResetDefault() {
  $('input.default-value').bind('focus', function() {
    if (!this.isReseted) {
      this.defaultValue = this.value;
      this.value = '';
      $(this).removeClass('default-value');
      this.isReseted = true;
    }
    return true;
  }).bind('change', function() {
    this.isContentIsChanged = true;
    return true;
  }).bind('blur', function() {
    if (this.isReseted && ! this.isContentIsChanged && this.defaultValue) {
      this.value = this.defaultValue;
      $(this).addClass('default-value');
      this.isReseted = false;
    }
    return true;
  }).each(
  function() {
    if (!this.form.isSetReset) {
      $(this.form).bind('submit', function() {
        $('input.default-value', this).each(
        function() {
          this.value = '';
        });
        return true;
      });
      this.form.isSetReset = true;
    }
  });
}

if (window.addEventListener) 
  window.addEventListener('load', initResetDefault, false);
else if (window.attachEvent) 
  window.attachEvent('onload', initResetDefault);

var popup_html_editor_text;

/*
  Debug window (require jQuery)
  Usage:
    debug().html('example');
    debug().html('example', 10);
    debug().add('second string')
    debug().clean();
    debug().hide();
    debug().show();
    debug().row(0).html('example');
    debug().row(0).add('second part');
    debug().row(0).remove();
    debug().opacity(0.1);
*/
var debug = function() {
  var debug_panel = false;

  return function() {

    if (typeof(window.$) == 'undefined') 
      return false;

    if (!debug_panel) {
      debug_panel = $(document.createElement('DIV')).
      css({
        position: 'absolute',
        border: '1px solid black',
        backgroundColor: 'white',
        display: 'none',
        top: '0px',
        left: '0px',
        width: '200px',
        height: '200px',
        overflow: 'auto',
        padding: '5px',
        margin: '0px'
      }).get(0);

      document.body.appendChild(debug_panel);

      debug_panel.defaultOpacity = 0.9;
      debug_panel.ttl = 0;
      debug_panel._extend_create = false;
      debug_panel._ttlTO = false;
      debug_panel._rowsLength = 0;

      /* Replace window content */
      debug_panel.html = function(str, ttl) {
        this._getBox().innerHTML = str;
        this.show();
        this.startTTL(arguments.length > 1 ? ttl: this.ttl);
      }

      /* Add new string */
      debug_panel.add = function(str, ttl) {
        this._getBox().innerHTML += str + "<br />\n";
        this.show();
        this.startTTL(arguments.length > 1 ? ttl: this.ttl);
      }

      /* Get row (old or new) */
      debug_panel.row = function(i) {
        var row = $('div:eq(' + i + ')', this._getBox()).get(0);
        if (!row) {
          for (var x = this._rowsLength; x < i + 1; x++) {
            row = this._getBox().appendChild(document.createElement('DIV'));
            row.remove = this._removeRow;
            row.html = this._htmlRow;
            row.add = this._addRow;
            row.box = this;
          }

          this._rowsLength = i + 1;
        }

        return row;
      }

      /* Remove row */
      debug_panel._removeRow = function() {
        if (this.parentNode) {
          this.box._rowsLength--;
          this.parentNode.removeChild(this);
        }
      }

      /* Replace row content */
      debug_panel._htmlRow = function(str, ttl) {
        this.innerHTML = str;
        this.box.show();
        this.box.startTTL(arguments.length > 1 ? ttl: this.parentNode.ttl);
      }

      /* Add content ot row */
      debug_panel._addRow = function(str, ttl) {
        this.innerHTML += str;
        this.box.show();
        this.box.startTTL(arguments.length > 1 ? ttl: this.parentNode.ttl);
      }

      /* Clean window content */
      debug_panel.clean = function() {
        this._rowsLength = 0;
        this._getBox().innerHTML = '';
      }

      /* Hide window */
      debug_panel.hide = function() {
        this.style.display = 'none';
      }

      /* Show window */
      debug_panel.show = function() {
        this.style.display = '';
      }

      /* Set window opacity */
      debug_panel.opacity = function(level) {
        level = parseFloat(level);
        if (isNaN(level) || level < 0 || level > 1) return false;

        level = Math.round(level * 100) / 100;
        if ($.browser.msie) {
          this.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity = ' + (level * 100) + ')';
        } else {
          this.style.opacity = level;
        }

        return true;
      }

      /* Start window auto-hide timer */
      debug_panel.startTTL = function(ttl) {
        if (this._ttlTO) clearTimeout(this._ttlTO);

        if (ttl <= 0) return false;

        var o = this;
        this._ttlTO = setTimeout(function() {
          o.hide();
        },
        ttl * 1000);

        return true;
      }

      /* Extend debug panel */
      debug_panel.extend = function() {
        if (this._extend_create) return true;

        var scripts = document.getElementsByTagName('SCRIPT');
        var m;
        var path = false;
        for (var i = 0; i < scripts.length && ! path; i++) {
          if (scripts[i].src && (m = scripts[i].src.match(/^(.+\/)common.js/))) path = m[1];
        }

        if (!path) return false;

        var s = document.createElement('SCRIPT');
        s.src = path + 'debug.js';
        document.body.appendChild(s);

        this._extend_create = true;

        return true;
      }

      /* Check - debug extended or not */
      debug_panel.is_extended = function() {
        return this._extend_create && typeof(window._debug_is_extended) != 'undefined' && _debug_is_extended;
      }

      debug_panel._getBox = function() {
        return this;
      }

      if (debug_panel.defaultOpacity > 0 && debug_panel.defaultOpacity <= 1) {
        debug_panel.opacity(debug_panel.defaultOpacity);
      }

    }

    /* Extend debug panel methods */
    if (typeof(window.debug_panel_ext_methods) != 'undefined' && debug_panel_ext_methods) {
      for (var i = 0; i < debug_panel_ext_methods.length; i++) {
        debug_panel[debug_panel_ext_methods[i]] = debug_panel_ext[debug_panel_ext_methods[i]];
      }

      if (typeof(debug_panel_ext.init) != 'undefined') debug_panel_ext.init.call(debug_panel);

      debug_panel_ext_methods = false;
      debug_panel_ext = false;
    }

    return debug_panel;
  }
} ();

/**
 * Changing button width on the fly (IE bugs)
 */
function change_width_iefix() {
  $("td div button").each(function() {
    $(this).width("auto").width($(this).width());
  });
}

/**
 * Popup wrapper
 */
function popup(url, width, height) {
  window.open(
  url, 'popup', 'width=' + width + ',height=' + height + ',toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');
}

/**
 * Dialog tools specific toggle function.
 * 
 * @param string active_panel    left or right panel that should be activated
 * @param string nonactive_panel left or right panel that should be deactivated
 * 
 * @return void
 * @see    ____func_see____
 * @since  4.4.0
 */
function dialog_tools_activate(active_panel, nonactive_panel) {
  $('.dialog-header-' + active_panel).removeClass('dialog-tools-nonactive');
  $('.dialog-header-' + nonactive_panel).addClass('dialog-tools-nonactive');

  $('.dialog-tools-' + active_panel).removeClass('hidden');
  $('.dialog-tools-' + nonactive_panel).addClass('hidden');
}

/**
 * Check form fields (CSS class-based) 
 * 
 * @return void
 * @see    ____func_see____
 */
function checkFormFields() {

  var errFields = [];

  if (!this.tagName || this.tagName.toUpperCase() != 'FORM') {

    if (
      arguments.length > 0
      && arguments[0]
      && arguments[0].tagName
      && arguments[0].tagName.toUpperCase() == 'FORM'
    ) {
      return checkFormFields.call(arguments[0]);
    }

    return true;
  }

  var err = empty = false;
  var frm = this;
    
  $('label', frm).each(function() {

    if (!this.htmlFor) {
      return;
    }

    var f = $('#' + this.htmlFor, frm).get(0);

    if (!f || f.disabled) {
      return;
    }

    if ($(f).parents(':hidden').length > 0) {
      return;
    }

    var errMsg = lbl_required_field_is_empty;

    var r = (
      $(f).hasClass('input-required')
      || $(this).hasClass('data-required')
      || (
        $(this).parent('td').hasClass('data-name')
        && $(this).closest('tr').find('td.data-required').length > 0
      )
    );

    var fType = false;

    if (f.className) {
      var m = $(f).attr('class').replace(/input-required/, '').match(/input-([a-z]+)/);
      if (m) {
        fType = m[1];
      }
    }

    if (!fType && !r) { 
      return;
    }

    if (r && $.trim($(f).val()) == '') {
      err = true;
      empty = true;

    } else if (fType) {

      var val = $(f).val().replace(/^\s+/g, '').replace(/\s+$/g, '');

      errMsg = lbl_field_format_is_invalid;

      switch (fType) {

      case 'email':
        if (val.search(email_validation_regexp) == - 1) {
          err = true;
          errMsg = txt_email_invalid;
        }
        break;

      case 'int':
        err = val.search(/^[-+]?\d+$/) == - 1;
        break;

      case 'uint':
        err = val.search(/^\+?\d+$/) == - 1;
        break;

      case 'intz':
        err = val.search(/^[-+]?\d+$/) == - 1 || val == '0';
        break;

      case 'uintz':
        err = val.search(/^\+?\d+$/) == - 1 || val == '0';
        break;

      case 'double':
        err = val.search(/^[-+]?(?:\d+|\.\d+|\d+\.|\d+\.\d+)$/) == - 1;
        break;

      case 'udouble':
        err = val.search(/^\+?(?:\d+|\.\d+|\d+\.|\d+\.\d+)$/) == - 1;
        break;

      case 'doublez':
        err = val.search(/^[-+]?(?:\d+|\.\d+|\d+\.|\d+\.\d+)$/) == - 1 || val.search(/^[-+]?[0\.]+$/) != - 1;
        break;

      case 'udoublez':
        err = val.search(/^\+?(?:\d+|\.\d+|\d+\.|\d+\.\d+)$/) == - 1 || val.search(/^\+?[0\.]+$/) != - 1;
        break;

      case 'ip':
        err = val.search(/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/) == - 1;
        break;
      }
    }

    if (err) {

      if (is_admin_editor) {
        errFields[errFields.length] = $(this).html();
      }
      else {
        markErrorField(f, empty ? lbl_field_required: '');

        if ($(f).not(':hidden')) {
          $(f).focus();
        }

        xAlert(substitute(errMsg, 'field', $(this).html()), lbl_warning);
        return false;
      }
    }

  });

  if (err && !is_admin_editor) {

    return false;

  } else if (errFields.length > 0) {

    return confirm(substitute(txt_required_fields_not_completed, 'fields', "\n\t" + errFields.join(",\n\t") + "\n\n"));

  }

  return true;
}

/**
 * Highlight error field (CSS-based)
 * 
 * @param object  $f        Field object
 * @param string  $errLabel Error label
 * 
 * @return void
 * @see    ____func_see____
 */
function markErrorField(f, errLabel) {

  if (!f || f == 'undefined') {
    return true;
  }

  var container = $(f).parents('tr, .field-container')[0];

  if (container && container != 'undefined' && ! $(container).hasClass('fill-error')) {
    $(container).addClass('fill-error');

    if (errLabel && errLabel != '') {
      $(document.createElement('div')).attr('class', 'error-label').appendTo($(f).parent()).html(lbl_field_required);
    }

    $(f).bind('keydown', function(event) {
      if (event.keyCode == '13') {
        event.preventDefault();
      }
      if ($.trim($(this).val() + String.fromCharCode(event.keyCode)) != '') {
        $(container).removeClass('fill-error').find('div.error-label').remove();
        $(this).unbind('keydown');
      }
    });
  }

}

/**
 * Mark empty required form fields
 * 
 * @param form $form Form object
 * 
 * @return void
 * @see    ____func_see____
 */
function markEmptyFields(form) {

  if (!form) {
    return;
  }

  $(form).find('.data-required').each(function() {
    var parentObj = $(this).parents('tr, .field-container')[0];

    if (!parentObj || parentObj == 'undefined') {
      return;
    }

    $(parentObj).find('input, textarea, select').each(function() {
      if (this.value == '') {
        markErrorField(this);
      }
    });
  });
}

/**
 * Apply checking of the required fields of the form
 * on submit automatically
 * 
 * @param form $form Form DOM object
 * 
 * @return bool
 * @see    ____func_see____
 */
function applyCheckOnSubmit(form) {

  if (!form) {
    return true;
  }

  var defaultAction = false;

  if (undefined !== form.onsubmit && form.onsubmit && form.onsubmit.constructor != String) {
    var defaultAction = form.onsubmit;
    form.onsubmit = null;
  }

  $(form).submit(function() {
    if (checkFormFields(form)) {
      if (defaultAction != false) {
        return defaultAction.call(form);
      }
      return true;
    }
    return false;
  });
}

/**
 * Custom alert using jQuery UI
 * 
 * @param string $msg    Message
 * @param string $header Alert header
 * 
 * @return void
 * @see    ____func_see____
 */
function xAlert(msg, header) {

  var buttons = {};
  buttons[lbl_ok] = function() {
    $(this).dialog('destroy').remove();
  }

  $(document.createElement('div')).attr('class', 'xalertbox').html(msg).dialog({
    modal: $('.ui-widget-overlay').length <= 0,
    title: undefined === header ? '': header,
    buttons: buttons
  });
}

/**
 * Custom confirm using jQuery UI
 * 
 * @param string   $msg      Message
 * @param string   $callback Callback on confirm
 * @param string   $header   Confirmation header text
 * 
 * @return void
 * @see    ____func_see____
 */
function xConfirm(msg, callback, header) {

  var buttons = {};
  buttons[lbl_no] = function() {
    $(this).dialog('destroy').remove();
  }
  buttons[lbl_yes] = function() {
    if (undefined !== callback && callback != '') {
      eval(callback);
    }
    $(this).dialog('destroy').remove();
  }

  $(document.createElement('div')).attr('class', 'xalertbox').html(msg).dialog({
    modal: $('.ui-widget-overlay').length <= 0,
    title: undefined === header ? '': header,
    buttons: buttons
  });

}

