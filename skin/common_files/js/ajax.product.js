/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Ajax product widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ajax.product.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Widget :: factory
ajax.widgets.product = function(elm) {
  if (!elm)
    return false;

  if (elm.constructor == Array) {
    if (!elm[0].productWidget)
      new ajax.widgets.product.obj(elm);

  } else if (elm.tagName) {
    if (!elm.productWidget)
      new ajax.widgets.product.obj(elm);

  } else {
    return false;
  }
    
  return true;
}

// Widget :: object
ajax.widgets.product.obj = function(elm) {
  this.elm = $(elm);

  var s = this;

  this.elm.each(
    function() {
      this.productWidget = s;
    }
  );

  this._prepareElement();

  if (isNaN(this.productid) || this.productid < 1)
    this.productid = false;

  $(ajax.messages).bind(
    'cartChanged',
    function(e, data) {
      return s._add2cartListener(data);
    }
  );

  this._callbackGPI = function(state, a, b, c) {
    return s.callbackGPI(state, a, b, c);
  }
  this._callbackBNB = function(responseText, textStatus, XMLHttpRequest) {
    return s.callbackBNB(responseText, textStatus, XMLHttpRequest);
  }
  this._callbackPDB = function(responseText, textStatus, XMLHttpRequest) {
    return s.callbackPDB(responseText, textStatus, XMLHttpRequest);
  }

}

ajax.widgets.product.obj.prototype.elm = false;

ajax.widgets.product.obj.prototype.type = false;
ajax.widgets.product.obj.prototype.productid = false;

// Widget :: check object status
ajax.widgets.product.obj.prototype.isReady = function() {
  return this.type && this.productid;
}

// Widget :: update product info
ajax.widgets.product.obj.prototype.updateBuyNowBlock = function() {
  if (!this.isReady())
    return false;

  var o = this;
  var f = function() {
    return ajax.core.loadBlock(
      $('.buy-now', o.elm),
      'buy_now',
      {
        productid: o.productid,
        is_featured_product: o.is_featured_product,
        type: o.type
      },
      o._callbackBNB
    );
  }

  return this._checkBlockButton(f);
}

// Widget :: update product details block
ajax.widgets.product.obj.prototype.updateProductDetailsBlock = function() {
  if (!this.isReady())
    return false;

  data = {
    productid: this.productid
  };
  var form = $('form', this.elm).get(0);
  if (form) {
    for (var i = 0; i < form.elements.length; i++) {
      if (form.elements[i].name) {
        var m = form.elements[i].name.match(/^product_options\[(\d+)\]$/);
        if (m) {
          data['options[' + m[1] + ']'] = form.elements[i].value;
        }
      }
    }
  }

  var m = (self.location + '').match(/&wishlistid=(\d+)/);
  if (m)
    data['wishlistid'] = m[1];

  var m = (self.location + '').match(/&pconf=(\d+)/);
  if (m)
    data['pconf'] = m[1];

  var m = (self.location + '').match(/&slot=(\d+)/);
  if (m)
    data['slot'] = m[1];

  var o = this;
  var f = function() {
    ajax.core.loadBlock(
      $('.details', o.elm).eq(0),
      'product_details',
      data,
      o._callbackPDB
    );
  }

  return this._checkBlockButton(f);
}

// Widget :: ajax callback (buy now block update)
ajax.widgets.product.obj.prototype.callbackBNB = function(responseText, textStatus, XMLHttpRequest) {
  if (XMLHttpRequest.status == 200) {
    ajax.core.trigger(
      'updateBuyNowBlock',
      {
        productid: this.productid,
        element: this.elm
      }
    );

    $('div.dropout-container div.drop-out-button').not('.activated-widget').each(initDropOutButton);
  }

  return true;
}

// Widget :: ajax callback (product details block update)
ajax.widgets.product.obj.prototype.callbackPDB = function(responseText, textStatus, XMLHttpRequest) {
  if (XMLHttpRequest.status == 200) {
    ajax.core.trigger(
      'updateProductDetailsBlock',
      {
        productid: this.productid,
        element: this.elm
      }
    );

    $('div.dropout-container div.drop-out-button').not('.activated-widget').each(initDropOutButton);
  }

  return true;
}

// Widget :: prepare element
ajax.widgets.product.obj.prototype._prepareElement = function() {
  this.productid = false;
  this.type = false;
  this.is_free_product = false;
  this.is_featured_product = false;

  var form_elements = $('form', this.elm);
  if (form_elements.length > 0) {
    tmp = form_elements.get(0).elements.namedItem('productid');
    if (tmp)
      this.productid = parseInt(tmp.value);

    if (isNaN(this.productid) || this.productid < 1)
      this.productid = false;

    tmp = form_elements.get(0).elements.namedItem('is_free_product');
    if (tmp)
      this.is_free_product = parseInt(tmp.value);

    if (isNaN(this.is_free_product))
      this.is_free_product = false;

    tmp = form_elements.get(0).elements.namedItem('is_featured_product');
    if (tmp)
      this.is_featured_product = tmp.value;
  }

  if (this.elm.is('div.item')) {
    this.type = 'list';

  } else if (this.elm.filter('td').length == this.elm.length) {
    this.type = 'matrix';

  } else if (this.elm.is('.product-details')) {
    this.type = 'details';
  }

  return true;
}

/* Private */

// Widget :: add2cart message listener
ajax.widgets.product.obj.prototype._add2cartListener = function(data) {
  if (data.status == 1 && data.changes) {
    for (var i in data.changes) {
      if (hasOwnProperty(data.changes, i) && data.changes[i].productid == this.productid && data.changes[i].changed != 0) {

        switch (this.type) {
          case 'list':
          case 'matrix':
            this.updateBuyNowBlock();
            break;

          case 'details':
            this.updateProductDetailsBlock();
            break;
        }
        break;
      }
    }
  }

  return true;
}

ajax.widgets.product.obj.prototype._checkBlockButton = function(f) {
  if ($('.do-add2cart-wait, .do-add2cart-success', this.elm).length > 0) {
    var o = this;
    return setTimeout(
      function() {
        return o._checkBlockButton(f);
      },
      1000
    );

  } else {
    return f();
  }
}
