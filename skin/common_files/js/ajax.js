/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * X-Cart Ajax core library
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ajax.js,v 1.4 2010/07/26 12:21:16 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function errMsg(idx, label) {
  this.idx = idx;
  this.label = label;
}

errMsg.prototype.getLabelText = function() {
  if (typeof(window[this.label]) == 'undefined')
    return false;

  return window[this.label];
}

var ajax = {
  query: {
    defaultTTL: 30000,

    lastIdx: -1,
    query: [],

    _currentIdx: false
  },
  actions: {},
  widgets: {},
  core: {},
  messages: {},
  savedEvents: [],
  isReady: false
};

$(document).ready(
  function() {
    ajax.isReady = true;
    $(ajax).trigger('load');
    for (var i = 0; i < ajax.savedEvents.length; i++) {
        ajax.core.trigger(ajax.savedEvents[i].name, ajax.savedEvents[i].params);
    }
    ajax.savedEvents = [];
  }
);

/*
  Query
*/

// Add to query
ajax.query.add = function(options) {
  if (!options || !ajax.core.isReady())
    return false;

  options.status = 1;

  this.lastIdx++;
  this.query[this.lastIdx] = options;

  var o = this;
  setTimeout(
    function() {
      o._check();
    },
    100
  );

  return this.lastIdx;
}

// Remove from query
ajax.query.remove = function(i) {
  if (typeof(this.query[i]) == 'undefined' || !this.query[i])
    return false;

  this.query[i] = false;

  return true;
}

// Check query [private]
ajax.query._check = function() {
  if (this._currentIdx !== false)
    return false;

  var i = 0;
  while ((!this.query[i] || this.query[i].status != 1) && this.lastIdx >= i)
    i++;

  if (!this.query[i] || this.query[i].status !== 1)
    return false;

  this._currentIdx = i;

  this.query[i].status = 2;

  var s = this;
  var o = this.query[i];

  if (!o.timeout || o.timeout < 0)
    o.timeout = this.defaultTTL;

  if (o.complete) {
    var fc = o.complete;
    o.complete = function(obj, txt) {
      s._currentIdx = false;
      s.remove(i);
      fc(obj, txt, i);
      s._check();
    }
  }

  if (o.error) {
    var fe = o.error;
    o.error = function(obj, txt, err) {
      s._currentIdx = false;
      s.remove(i);
      fe(obj, txt, err, i);
      s._check();
    }
  }

  var fs = o.success;
  o.success = function(txt) {
    s._currentIdx = false;
    s.remove(i);
    var r = ajax.core.processMessages(txt);
    if (fs)
      fs(txt, i, r);
    s._check();
  }

  this.query[i].obj = $.ajax(o);

  return true; 
}

/*
  Core
*/

var __xhr_cache = false;
ajax.core.isReady = function() {
  try {
    __xhr_cache = $.ajaxSettings.xhr();
  } catch(e) {
    return false;
  }

  var ret = !!__xhr_cache;

  delete xhr;

  return ret;
}

// Replace service messages from response data
ajax.core.getMessages = function(data) {
  if (!data || data.constructor != String)
    return [data, false];

  var rg = /<div class="ajax-internal-message" style="display: none;">(.+)<\/div>/g;
  var str = data;
  var msgs = [];
  var pos;

  if ((pos = str.search(rg)) != -1) {

    var mm = data.match(rg);

    if (mm) {

      $.each(mm, function(k, v) {

        if (!v.match(rg)) {
          return;
        }

        var m = RegExp.$1;

        var tmp = m.split(/:/);
        var msg = {
          name: tmp.shift(),
          params: {}
        };

        tmp = tmp.join(':');

        if (tmp) {
          try {
            msg.params = eval("(" + tmp + ")");
          } catch (e) { }
        }

        msgs[msgs.length] = msg;

      });

    }
  }

  return {data: data.replace(rg, ''), messages: msgs};
}

// Process and throw service messages from response data
ajax.core.processMessages = function(data) {
  var r = ajax.core.getMessages(data);

  if (r.messages && r.messages.length > 0) {
    for (var i = 0; i < r.messages.length; i++) {
      ajax.core.trigger(r.messages[i].name, r.messages[i].params);
    }
  }

  return r;
}

// Trigger message
ajax.core.trigger = function(name, params) {
  if (!ajax.isReady) {
    ajax.savedEvents[ajax.savedEvents.length] = {
      name: name,
      params: params
    };

    return true;
  }

  return $(ajax.messages).trigger(name, [params]);
}

ajax.core.loadBlock = function(elm, name, params, callback) {
  if (!ajax.core.isReady())
    return false;

  elm.each(
    function() {
      if (this._xhrLoadBlock) {
        try {
          this._xhrLoadBlock.abort();
        } catch(e) { }
        this._xhrLoadBlock = false;
      }
    }
  );

  params = params || {};

  var d = new Date();
  params.t = d.getTime()

  var xhr = false;
  try {
    xhr = $.ajax(
      {
        url: xcart_web_dir + '/get_block.php?block=' + name + '&language=' + store_language,
        type: 'POST',
        data: params,
        dataType: 'html',
        complete: function(res, status) {
          elm.each(
            function() {
              this._xhrLoadBlock = xhr;
            }
          );

          if (status == "success" || status == "notmodified") {
            elm.html(res.responseText);
            change_width_iefix();
            ajax.core.normalizeElements(elm);
            $('form', elm).not('.skip-auto-validation').each( function() {
              applyCheckOnSubmit(this)
            });
          }

          if (callback) {
            elm.each(callback, [res.responseText, status, res]);
          }

        }
      }
    );

    elm.each(
      function() {
        this._xhrLoadBlock = xhr;
      }
    );

    return xhr;

  } catch(e) {
    return false;
  }
}

ajax.core.normalizeElements = function(elm) {
  if ($.browser.msie && parseFloat($.browser.version) < 7) {
    var events =      ['onsubmit', 'onchange', 'onclick', 'onmousemove', 'onmouseover', 'onmouseout'];
    var eventsShort = ['submit',   'change',   'click',   'mousemove',   'mouseover',   'mouseout'];
    var eventsLength = events.length;
    var rg = /^javascript:/;
    var i;

    $('*', elm).each(
      function() {
        for (i = 0; i < eventsLength; i++) {
          if (this[events[i]] && this[events[i]].constructor == String) {

            $(this).bind(
              eventsShort[i],
              new Function(
                '',
                this[events[i]].replace(rg, '')
              )
            );
            this.removeAttribute(events[i]);
          }
        }
      }
    );
  }

  return true;
}
