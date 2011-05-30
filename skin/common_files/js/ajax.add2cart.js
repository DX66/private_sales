/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Ajax add to cart widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ajax.add2cart.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Action
ajax.actions.add2cart = function(productid, quantity, options, callback) {
  if (!productid)
    return false;

  var data = {
    mode: 'add',
    productid: productid,
    amount: quantity
  };

  if (options) {
    for (var i in options) {
      if (hasOwnProperty(options, i)) {
        data['product_options[' + i + ']'] = options[i];
      }
    }
  }

  var request = {
    type: 'POST',
    url: xcart_web_dir + '/cart.php',
    data: data
  };

  if (callback) {
    request.success = function(html, i, r) {
      return callback(true, html, i, r);
    };
    request.error = function(obj, txt, err, i) {
      return callback(false, obj, txt, err, i);
    }
  }

  return ajax.query.add(request);
}

// Widget
ajax.widgets.add2cart = function(form) {
  if (!form || typeof(form.tagName) == 'undefined' || !form.tagName || form.tagName.toUpperCase() != 'FORM')
    return false;

  if (!form.add2cartWidget) {
    new ajax.widgets.add2cart.obj(form);
  }

  return form.add2cartWidget.add2cart();
}

ajax.widgets.add2cart.obj = function(form) {

  this.savedData = {};

  this.form = $(form);

  form.add2cartWidget = this;

  this._prepareWidget();

  var s = this;
  $(ajax.messages).bind(
    'updateBuyNowBlock',
    function(e, data) {
      return s._callbackUpdateBuyNowBlock(data);
    }
  );

  $(ajax.messages).bind(
    'updateProductDetailsBlock',
    function(e, data) {
      return s._callbackUpdateBuyNowBlock(data);
    }
  );

  return true;
}

// Options
ajax.widgets.add2cart.obj.prototype.ttl = 3000;

// Property
ajax.widgets.add2cart.obj.prototype.button = false;
ajax.widgets.add2cart.obj.prototype.form = false;

ajax.widgets.add2cart.obj.prototype.state = 1;
ajax.widgets.add2cart.obj.prototype.productid = false;

ajax.widgets.add2cart.obj.prototype.savedData = {};
ajax.widgets.add2cart.obj.prototype.isClicked = false;

// Widget :: check - ready widget or not
ajax.widgets.add2cart.obj.prototype.isReady = function() {
  return this.form.length > 0 && this.productid > 0 && this.box.length > 0;
}

// Widget :: add to cart
ajax.widgets.add2cart.obj.prototype.add2cart = function() {
  if (!this.isReady())
    return false;

  if (this.isClicked || this.state == 2 || this.state == 3 || this.state == 4)
    return true;

  this.isClicked = true;

  this.changeState(2);

  var s = this;

  setTimeout(
    function() {
      s.isClicked = false;
    },
    100
  );

  return ajax.query.add(
    {
      type: 'POST',
      url: xcart_web_dir + '/cart.php',
      data: this.form.serialize(),
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: ajax callback
ajax.widgets.add2cart.obj.prototype.callback = function(state, a, b, c, d) {
  if (!this.isReady())
    return false;

  var s = false;
  if (state && c.messages) {
    for (var i = 0; i < c.messages.length; i++) {
      if (c.messages[i].name == 'cartChanged' && c.messages[i].params.status == 1 && c.messages[i].params.changes) {
        for (var p in c.messages[i].params.changes) {
          if (hasOwnProperty(c.messages[i].params.changes, p) && c.messages[i].params.changes[p].productid == this.productid && c.messages[i].params.changes[p].changed != 0)
            s = true;
        }
      }
    }
  }

  this.changeState(s ? 3 : 4);

  return true; 
}

// Widget :: check button element
ajax.widgets.add2cart.obj.prototype.checkButton = function(button) {
  if (!button)
    button = this.button;

  if (!button || typeof(button.tagName) == 'undefined')
    return false;

  var tn = button.tagName.toUpperCase();

  if (tn == 'BUTTON' && $(button).children('span.button-right').children('span.button-left').length == 1) {
    return true;

  } else if (tn == 'DIV' && $(button).children('div').length == 1) {
    return true;
  }

  return false;
}

// Widget :: changes widget status
//  1 - idle
//  2 - background request
//  3 - success message
//  4 - error message
//  5 - submit form without background request
ajax.widgets.add2cart.obj.prototype.changeState = function(s) {
  if (this.state == s)
    return true;

  var res = false;

  if (this.button.length > 0) {

    switch (this.state) {
      case 2:
        res = this.cleanWaitState(s);
        break;

      case 3:
        res = this.cleanAddedState(s);
        break;

      case 4:
        res = this.cleanErrorState(s);
        break;

      default:
        res = this.cleanIdleState(s);
    }

    if (!res)
      return false;

  } else {
    res = true;
  }

  this.state = s;
  var o = this;

  if (this.button.length > 0) {
    switch (s) {
      case 2:
        res = this.doWaitState();
        break;

      case 3:
        res = this.doAddedState();
        setTimeout(
          function() {
            return o.changeState(1);
          },
          this.ttl
        );
        break;

      case 4:
        res = this.doErrorState();
        setTimeout(
          function() {
            o.changeState(5);
            o.submitForm(true);
          },
          this.ttl
        );
        break;

      default:
        res = this.doIdleState();
    }

  }

  return res;
}

// Widget :: change state to Idle
ajax.widgets.add2cart.obj.prototype.doIdleState = function() {
  if (this.savedData) {
    switch (this.savedData.type) {
      case 'b':
        $('.button-left', this.button).html(this.savedData.html);
        break;

      case 'd':
        $('div', this.button).html(this.savedData.html);
        break;

      default:
        return false;
    }
  }

  return true;
}

// Widget :: remove Idle state
ajax.widgets.add2cart.obj.prototype.cleanIdleState = function() {
  this.savedData = {
    type: false,
    box: false,
    html: false,
    width: false,
    height: false
  };

  switch (this.button.get(0).tagName.toUpperCase()) {
    case 'BUTTON':
      this.savedData.type = 'b';
      this.savedData.box = $('.button-left', this.button);
      break;

    case 'DIV':
      this.savedData.type = 'b';
      this.savedData.box = $('div', this.button);
      break;

    default:
      return false;
  }

  this.savedData.html = this.savedData.box.html();
  this.savedData.width = this.savedData.box.width();
  this.savedData.height = this.savedData.box.height();

  return true;
}

// Widget :: change state to Wait
ajax.widgets.add2cart.obj.prototype.doWaitState = function() {
  this.button.addClass('do-add2cart-wait');

  var span = document.createElement('SPAN');
  span.className = 'progress';
  span.style.width = this.savedData.width + 'px';
  span.style.height = this.savedData.height + 'px';

  this._freezeBox();

  this.savedData.box.empty().append(span);

  return true;
}

// Widget :: remove Wait state
ajax.widgets.add2cart.obj.prototype.cleanWaitState = function() {
  this.button.removeClass('do-add2cart-wait').remove('.progress');

  this._unFreezeBox();

  return true;
}

// Widget :: change state to Added
ajax.widgets.add2cart.obj.prototype.doAddedState = function() {
  this.button.addClass('do-add2cart-success');

  this._freezeBox();

  if (this.savedData.box)
    this.savedData.box.html(lbl_added);

  return true;
}

// Widget :: remove Added state
ajax.widgets.add2cart.obj.prototype.cleanAddedState = function() {
  this.button.removeClass('do-add2cart-success');

  this._unFreezeBox();

  return true;
}

// Widget :: change state to Error
ajax.widgets.add2cart.obj.prototype.doErrorState = function() {
  this.button.addClass('do-add2cart-error');

  this._freezeBox();

  if (this.savedData.box)
    this.savedData.box.html(lbl_error);

  return true;
}

// Widget :: remove Error state
ajax.widgets.add2cart.obj.prototype.cleanErrorState = function() {
  this.button.removeClass('do-add2cart-error');

  this._unFreezeBox();

  return true;
}

// Widget :: submit form withour background request
ajax.widgets.add2cart.obj.prototype.submitForm = function(isError) {
  if (!this.isReady())
    return false;

  if (isError && !this.form.get(0).elements.namedItem('ajax_error')) {
    var inp = document.createElement('INPUT');
    inp.type = 'hidden';
    inp.name = 'ajax_error';
    inp.value = 'Y';

    this.form.append(inp);
  }

  this.form.get(0).submit();

  return true;
}

/* Private methods */

// Widget :: prepare widget
ajax.widgets.add2cart.obj.prototype._prepareWidget = function() {

  if (this.form.length == 0)
    return false;

  // Get mode: do add to cart if mode == 'add', else cancel
  var m = this.form.get(0).elements.namedItem('mode');
  if (m && m.value != 'add') {
    return false;
  }

  // Get box
  this.box = this.form.parents().filter('.details,.product-cell');

  // Get button
  this.button = $('.add-to-cart-button', this.form).eq(0);

  // Get productid
  var p = this.form.get(0).elements.namedItem('productid');
  if (p) {
    this.productid = parseInt(p.value);
    if (isNaN(this.productid) || this.productid < 1)
      this.productid = false;
  }

  return true;
}

// Widget :: updateBuyNowBlock event listener
ajax.widgets.add2cart.obj.prototype._callbackUpdateBuyNowBlock = function(data) {
  this.savedData = {};

  return true;
}

// Widget :: freeze button width
ajax.widgets.add2cart.obj.prototype._freezeBox = function() {
  if (this.savedData.box)
    this.savedData.box.width(this.savedData.width);

  return true;
}

// Widget :: unfreeze button width
ajax.widgets.add2cart.obj.prototype._unFreezeBox = function() {
  if (this.savedData.box)
    this.savedData.box.width('auto');

  return true;
}
