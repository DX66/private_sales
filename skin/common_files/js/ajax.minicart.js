/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Ajax minicart widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ajax.minicart.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Facntory
ajax.widgets.minicart = function(elm) {
  if (!elm) {
    elm = $('.menu-minicart');

  } else {
    elm = $(elm);
  }

  elm.each(
    function() {
      if (!this.minicartWidget)
        new ajax.widgets.minicart.obj(this);
    }
  );

  return true;
}

// Class
ajax.widgets.minicart.obj = function(elm) {
  this.elm = $(elm);

  elm.minicartWidget = this;

  this.msie6 = $.browser.msie && parseInt($.browser.version) < 7;

  var s = this;

  $(ajax.messages).bind(
    'cartChanged',
    function(e, data) {
      return s._add2cartListener(data);
    }
  );

  this._minicartReposition = function(e) {
    return s.minicartReposition(e);
  }

  this._callbackMB = function(e) {
    s.click2Minicart = true;
    return s.minicartVisible ? s.hideMinicart() : s.showMinicart();
  }

  this._callbackUM = function(responseText, textStatus, XMLHttpRequest) {
    return s._callbackUpdateMinicart(responseText, textStatus, XMLHttpRequest);
  }

  this._deleteItem = function(e) {
    return !s.deleteItem(this, e);
  }

  this._updateCart = function(e) {
    return !s.updateCart(this, e);
  }

  this._clearCart = function() {
    return !s.clearCart();
  }

  this._showCheckoutPopup = function(e) {
    return s.checkoutPopupVisible ? !s.hideCheckoutPopup(this, e) : !s.showCheckoutPopup(this, e);
  }

  if (this.elm.hasClass('ajax-minicart')) {
    this._constructMinicartButton();
  }

  $('body').click(
    function() {
      if (!s.click2Minicart)
        s.hideMinicart();

      s.click2Minicart = false;
    }
  );

}

// Options
ajax.widgets.minicart.obj.prototype.errorTTL = 3000;
ajax.widgets.minicart.obj.prototype.minicartBorder = 0;

// Properties
ajax.widgets.minicart.obj.prototype.elm = false;
ajax.widgets.minicart.obj.prototype.minicart = false;
ajax.widgets.minicart.obj.prototype.minicartButton = false;

ajax.widgets.minicart.obj.prototype.minicartState = false;
ajax.widgets.minicart.obj.prototype.minicartVisible = false;
ajax.widgets.minicart.obj.prototype.minicartChanged = false;
ajax.widgets.minicart.obj.prototype.checkoutPopupVisible = false;

// Widget :: check widget status
ajax.widgets.minicart.obj.prototype.isReady = function() {
  return this.minicart.length > 0 && this.checkElement();
}

// Widget :: check element
ajax.widgets.minicart.obj.prototype.checkElement = function(elm) {
  if (!elm)
    elm = this.elm;

  return elm && elm.hasClass('menu-minicart');
}

// Widget :: update cart total block
ajax.widgets.minicart.obj.prototype.updateTotal = function() {
  return this.checkElement() && ajax.core.loadBlock($('div.minicart, span.minicart', this.elm), 'minicart_total');
}

// Widget :: update cart/checkout links block
ajax.widgets.minicart.obj.prototype.updateCartLinks = function() {
  return this.checkElement() && ajax.core.loadBlock($('div.cart-checkout-links', this.elm), 'minicart_links');
}

// Widget :: update minicart block
ajax.widgets.minicart.obj.prototype.updateMinicart = function() {
  if (!this.isReady())
    return false;

  this._markMinicartBoxAsLoaded();

  return ajax.core.loadBlock(this.minicart, 'minicart', {}, this._callbackUM);
}

// Widget :: show minicart
ajax.widgets.minicart.obj.prototype.showMinicart = function() {
  this._constructMinicartBox();

  if (this.minicartVisible)
    return false;

  this.minicartButton.addClass('minicart-button-show');

  if (this.minicartState == 1 || this.minicartChanged) {
    this._markMinicartBoxAsLoaded();
    this.updateMinicart();
  }

  if (this._iframe) {
    this._iframe.show();
  }

  this.minicart.show();

  this.minicartVisible = true;

  this.minicartReposition();

  return true;
}

// Widget :: hide minicart
ajax.widgets.minicart.obj.prototype.hideMinicart = function() {
  if (!this.minicart || !this.minicartVisible)
    return false;

  this.minicartButton.removeClass('minicart-button-show');

  if (this.checkoutPopupVisible)
    this.hideCheckoutPopup();

  this.minicart.hide();

  if (this._iframe) {
    this._iframe.hide();
  }

  this.minicartVisible = false;

  return true;
}

// Widget :: minicart reposition
ajax.widgets.minicart.obj.prototype.minicartReposition = function() {
  if (!this.isReady() || !this.minicartVisible)
    return false;

  if (this.elm.parents().filter('#left-bar').length > 0 ||  this.elm.hasClass('left-dir-minicart')) {
    var l = $('.ajax-minicart-icon', this.elm).position().left;
    var ml = $('.ajax-minicart-icon', this.elm).css('margin-left');
    if (ml) {
      ml = parseInt(ml);
      if (isNaN(ml))
        ml = 0;
    }
    l += ml;

    this.minicart.css('left', l - this.minicartBorder);

  } else if (this.elm.parents().filter('#right-bar').length > 0 || this.elm.hasClass('right-dir-minicart')) {
    var rb = $('.ajax-minicart-icon', this.elm).width() + $('.ajax-minicart-icon', this.elm).position().left;
    var ml = $('.ajax-minicart-icon', this.elm).css('margin-left');
    if (ml) {
      ml = parseInt(ml);
      if (isNaN(ml))
        ml = 0;
    }
    rb += ml;

    var pw = $('.ajax-minicart-icon', this.elm).parents().eq(0).width();

    this.minicart.css('right', pw - rb - this.minicartBorder);
  }

  this._iframeReposition();

  return true;
}

// Widget :: delete cart item
ajax.widgets.minicart.obj.prototype.deleteItem = function(item, e) {
  if (!this.isReady() || !item || !item.href)
    return false;

  this._markMinicartBoxAsLoaded();

  return ajax.query.add({ url: item.href }) !== false;
}

// Widget :: update cart
ajax.widgets.minicart.obj.prototype.updateCart = function(item, e) {
  if (!this.isReady() || !item || !item.form)
    return false;

  this._markMinicartBoxAsLoaded();

  return ajax.query.add(
    {
      type: 'POST',
      url: xcart_web_dir + '/cart.php',
      data: $(item.form).serialize()
    }
  ) !== false;
}

// Widget :: clear cart
ajax.widgets.minicart.obj.prototype.clearCart = function() {
  if (!this.isReady())
    return false;

  this._markMinicartBoxAsLoaded();

  return ajax.query.add({ url: xcart_web_dir + '/cart.php?mode=clear_cart' }) !== false;
}

// Widget :: show checkout popup
ajax.widgets.minicart.obj.prototype.showCheckoutPopup = function(item, e) {
  var p = $('.checkout-popup-link .buttons-box', this.minicart);
  if (this.checkoutPopupVisible || p.length == 0)
    return false;

  $('.checkout-popup-link', this.minicart).children('a').addClass('show');

  if (this._iframe_checkout)
    this._iframe_checkout.show();

  p.show();

  this.checkoutPopupVisible = true;

  return true;
}

// Widget :: hide checkout popup
ajax.widgets.minicart.obj.prototype.hideCheckoutPopup = function(item, e) {
  var p = $('.checkout-popup-link .buttons-box', this.minicart);
  if (!this.checkoutPopupVisible || p.length == 0)
    return false;

  $('.checkout-popup-link', this.minicart).children('a').removeClass('show');
  p.hide();

  if (this._iframe_checkout)
    this._iframe_checkout.hide();

  this.checkoutPopupVisible = false;

  return true;
}


/* Private */

// Widget :: add2cart message listener
ajax.widgets.minicart.obj.prototype._add2cartListener = function(data) {
  if (data.status == 1) {
    this._constructMinicartButton();
    this.updateTotal();
    this.updateCartLinks();

    if (data.isEmpty) {

      // Cart is empty
      this._cartIsEmpty();

    } else if (this.minicart && this.minicartVisible) {

      // Update minicart
      this._constructMinicartBox();
      this.updateMinicart();

    } else {

      // Save cart changed status
      this.minicartChanged = true;
    }
  }

  return true;
}

// Widget :: empty is cart
ajax.widgets.minicart.obj.prototype._cartIsEmpty = function() {
  this.hideMinicart();
  this._destructMinicartButton();

  $('.ajax-minicart-icon', this.elm).eq(0)
    .removeClass('full').addClass('empty')
    .parents('.full').removeClass('full').addClass('empty');

  ajax.core.trigger('cartCleaned');

  return true;
}

// Widget :: construct minicart box
ajax.widgets.minicart.obj.prototype._constructMinicartBox = function() {
  if (this.minicart)
    return false;

  var p = $('.ajax-minicart-icon', this.elm).get(0).parentNode;

  if (this.msie6) {
    this._iframe = document.createElement('IFRAME');
    this._iframe.className = 'minicart-bg';
    this._iframe = $(p.appendChild(this._iframe));

    this._iframe_checkout = document.createElement('IFRAME');
    this._iframe_checkout.className = 'minicart-checkout-bg';
    this._iframe_checkout = $(p.appendChild(this._iframe_checkout));
  }

  this.minicart = $(p.appendChild(document.createElement('DIV')));
  this.minicart.addClass('minicart-box');

  $(window).resize(this._minicartReposition);

  var s = this;
  this.minicart.click(
    function(e) {
      if (!s.click2CheckoutPopup)
        s.hideCheckoutPopup();

      s.click2CheckoutPopup = false;
      s.click2Minicart = true;
      s.showMinicart();
      return true;
    }
  );

  this.minicartState = 1;
  this.minicartVisible = false;

  return true;
}

// Widget :: mark minicart box as loaded
ajax.widgets.minicart.obj.prototype._markMinicartBoxAsLoaded = function() {
  if (this.minicart.hasClass('wait'))
    return false;

  var block = document.createElement('DIV');
  block.className = 'progress';

  this.minicart.empty().addClass('wait').append(block);

  this._iframeReposition();

  return true;
}

// Widget :: unmark minicart box as loaded
ajax.widgets.minicart.obj.prototype._unmarkMinicartBoxAsLoaded = function() {
  this.minicart.removeClass('wait').children('.progress').remove();

  this._iframeReposition();

  return true;
}

// Widget :: prepare minicart box
ajax.widgets.minicart.obj.prototype._prepareMinicart = function() {
  var s = this;

  $('.delete', this.minicart).click(this._deleteItem);

  $('.update-cart', this.minicart).click(this._updateCart);

  if ($('.clear-cart a', this.minicart).length > 0) {
    $('.clear-cart', this.minicart).click(
      function() {
        return false;
      }
    );
    $('.clear-cart a', this.minicart).click(this._clearCart);

  } else {
    $('.clear-cart', this.minicart).click(this._clearCart);
  }

  if ($('.checkout-popup-link .buttons-box', this.minicart).length > 0) {
    $('.checkout-popup-link a.link', this.minicart).click(this._showCheckoutPopup);
    $('.checkout-popup-link .buttons-box', this.minicart).click(
      function() {
        s.click2CheckoutPopup = true;
      }
    );
  }

  return true;
}

// Widget :: display error message
ajax.widgets.minicart.obj.prototype._displayMinicartError = function() {
  this.minicart.empty().html(lbl_error).addClass('error');

  return true;
}

// Widget :: construct minicart button
ajax.widgets.minicart.obj.prototype._constructMinicartButton = function() {
  if (this.minicartButton)
    return false;

  this.minicartButton = $('.ajax-minicart-icon', this.elm);
  if (this.minicartButton.length == 0)
    return false;

  this.elm.addClass('ajax-minicart');

  this.minicartButton
    .addClass('minicart-button')
    .click(this._callbackMB);

  return true;  
}

// Widget :: destruct minicart button
ajax.widgets.minicart.obj.prototype._destructMinicartButton = function() {
  if (!this.minicartButton)
    return false;

  this.elm.removeClass('ajax-minicart full-mini-cart');

  this.minicartButton
    .removeClass('minicart-button')
    .unbind('click', this._callbackMB);

  this.minicartButton = false;

  return true;
}

// Widget :: update minicart listener
ajax.widgets.minicart.obj.prototype._callbackUpdateMinicart = function(responseText, textStatus, XMLHttpRequest) {
  this._unmarkMinicartBoxAsLoaded();

  if (this.minicartState == 1) {

    // Minicart exists as empty box
    if (XMLHttpRequest.status == 200) {
      this.minicartState = 2;

    } else {
      this._displayMinicartError();
      var s = this;
      setTimeout(
        function() {
          s.hideMinicart();
          s._destructMinicartButton();
        },
        this.errorTTL
      );
    }
  }

  if (XMLHttpRequest.status == 200) {

    // Display new content
    this.minicartChanged = false;
    this._prepareMinicart();

  } else if (XMLHttpRequest.getResponseHeader('X-Request-Error-Code') == 1) {

    // Cart is empty
    this._cartIsEmpty();

  } else {

    // Error
    this._displayMinicartError();
    var s = this;
    setTimeout(
      function() {
        s.hideMinicart();
        s._destructMinicartButton();
      },
      this.errorTTL
    );
  }

  return true;
}

// Widget :: iframe reposition
ajax.widgets.minicart.obj.prototype._iframeReposition = function() {
  if (!this._iframe)
    return false;

  var pos = this.minicart.position();
  this._iframe
    .css({ top: pos.top + 'px', left: pos.left + 'px' })
    .width(this.minicart.width())
    .height(this.minicart.height());

  var box = $('.checkout-popup-link .buttons-box', this.minicart);
  if (box.length > 0) {
    pos = box.position();
    this._iframe_checkout
      .css({ top: pos.top + 'px', left: pos.left + 'px' })
      .width(box.width())
      .height(box.height());
  }

  return true;
}

// onload handler
$(ajax).bind(
  'load',
  function() {
    return ajax.widgets.minicart();
  }
);
