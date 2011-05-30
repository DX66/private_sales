/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Rating bar widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ajax.rating.js,v 1.2.2.1 2010/11/22 10:27:34 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

ajax.widgets.rating = function(elm) {
  if (!elm) {
    elm = $('.creviews-rating-box');

  } else {
    elm = $(elm);
  }

  elm.each(
    function() {
      if (!this.ratingWidget)
        new ajax.widgets.rating.obj(this);
    }
  );

  return true;
}

ajax.widgets.rating.obj = function(elm) {
  this.elm = elm;
  this.elm$ = $(elm);

  elm.ratingWidget = this;

  var s = this;

  this._rate = function() {
    return !s.rate(this);
  }

  this._callbackUB = function(responseText, textStatus, XMLHttpRequest) {
    return s._callbackUpdateBar(responseText, textStatus, XMLHttpRequest);
  }

  this._callbackR = function(state, a, b, c, d) {
    return s._callbackRate(state, a, b, c, d);
  }

  this._prepareWidget();

  this.state = 1;
}

// Options
ajax.widgets.rating.obj.prototype.ttl = 3000;

// Property
ajax.widgets.rating.obj.prototype.elm = false;

ajax.widgets.rating.obj.prototype.productid = false;

ajax.widgets.rating.obj.prototype.state = false;
ajax.widgets.rating.obj.prototype.isRated = false;

// Widget :: check widget status
ajax.widgets.rating.obj.prototype.isReady = function() {
  return this.productid;
}

// Widget :: do rate
ajax.widgets.rating.obj.prototype.rate = function(item) {
  if (!item || !this.isReady() || this.isRated)
    return false;

  this.changeState(2);

  var s = this;
  return ajax.query.add(
    {
      type: 'POST',
      data: '',
      url: $(item).attr('href'),
      success: function(a, b, c, d) {
        return s._callbackR(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s._callbackR(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: update bar
ajax.widgets.rating.obj.prototype.updateBar = function() {
  if (!this.isReady())
    return false;

  if (this.state == 1)
    this.changeState(2);

  if (typeof(window.creviews_hover_loaded) && creviews_hover_loaded)
    creviews_hover_loaded = false;

  var data = {
    productid: this.productid
  };
  if (this.pconf)
    data.pconf = this.pconf;

  return ajax.core.loadBlock(this.elm$, 'rating_bar', data, this._callbackUB);
}

// Widget :: changes widget state
//  1 - idle
//  2 - wait
//  3 - success rated
//  4 - error
//  5 - cancel rate: already rated
ajax.widgets.rating.obj.prototype.changeState = function(state, msg) {
  if (this.state == state)
    return true;

  var res = false;

  switch (this.state) {
    case 2:
      res = this._cleanWaitState();
      break;

    case 3:
      res = this._cleanRatedState();
      break;

    case 4:
      res = this._cleanErrorState();
      break;

    case 5:
      res = this._cleanCancelState();
      break;

    default:
      res = this._cleanIdleState();
  }

  if (!res)
    return false;

  this.state = state;
  var o = this;

  switch (state) {
    case 2:
      res = this._doWaitState();
      break;

    case 3:
      res = this._doRatedState();
      break;

    case 4:
      res = this._doErrorState();
      setTimeout(
        function() {
          return o.changeState(1);
        },
        this.ttl
      );
      break;

    case 5:
      res = this._doCancelState();
      break;

    default:
      res = this._doIdleState();
  }

  return res;
}

/* Private */

// Widget :: prepare widget
ajax.widgets.rating.obj.prototype._prepareWidget = function() {

  // Check stars
  var links = $('.creviews-vote-bar a', this.elm$);
  if (links.length == 0)
    return false;

  // Detect productid
  var m = links.get(0).href.match(/productid=(\d+)/)
  if (!m)
    return false;

  this.productid = parseInt(m[1]);
  if (isNaN(this.productid) || this.productid < 1) {
    this.productid = false;
    return false;
  }

  // Detect Product Configurator properties
  m = links.get(0).href.match(/pconf=(\d+)/)
  if (m) {
    this.pconf = parseInt(m[1]);
    if (isNaN(this.pconf) || this.pconf < 1)
      this.pconf = false;
  }

  links.click(this._rate);

  return true;
}

// Widget :: callback rate action
ajax.widgets.rating.obj.prototype._callbackRate = function(state, a, b, c, d) {
  var s = 0;

  if (state && c.messages) {
    for (var i = 0; i < c.messages.length; i++) {
      if (c.messages[i].name == 'addVote' && c.messages[i].params.productid == this.productid) {
        s = parseInt(c.messages[i].params.status);
      }
    }
  }

  var o = this;
  switch (s) {
    case 1:
      this.changeState(3);
      setTimeout(
        function() {
          return o.updateBar();
        },
        this.ttl
      );
      break;

    case 2:
      this.changeState(5);
      setTimeout(
        function() {
          return o.updateBar();
        },
        this.ttl
      );
      break;

    default:
      if (!state && a.status == 0) {
        this.changeState(1);
      } else
        this.changeState(4);
  }

  return true;
}

// Widget :: callback update bar
ajax.widgets.rating.obj.prototype._callbackUpdateBar = function(responseText, textStatus, XMLHttpRequest) {
  if (XMLHttpRequest.status == 200) {
    this.savedElm = false;
    this.changeState(1);

  } else {
    this.changeState(4);
  }

  return true;
}

ajax.widgets.rating.obj.prototype._doIdleState = function() {
  if (this.savedElm) {
    var s = this;
    this.savedElm.children().each(
      function() {
        s.elm$.append(this);
      }
    );

    this.savedElm = false;
  }

  this.elm$.width('auto');

  return true;
}

ajax.widgets.rating.obj.prototype._cleanIdleState = function() {
  this.savedWidth = $('li:last', this.elm$).offset().left - $('li:first', this.elm$).offset().left + $('li:last', this.elm$).width();
  var pl = parseInt($('li:last', this.elm$).css('padding-left'));
  if (!isNaN(pl))
    this.savedWidth += pl;

  var pr = parseInt($('li:last', this.elm$).css('padding-right'));
  if (!isNaN(pr))
    this.savedWidth += pr;

  this.savedElm = this.elm$.clone();

  this.elm$.empty();

  return true;
}

ajax.widgets.rating.obj.prototype._doWaitState = function() {
  var block = document.createElement('SPAN');
  block.className = 'progress';

  this.elm$
    .width(this.savedWidth)
    .empty()
    .addClass('wait')
    .append(block);

  return true;
}

ajax.widgets.rating.obj.prototype._cleanWaitState = function() {
  if (this.elm$.children().length == 1)
    this.elm$.empty();

  this.elm$.removeClass('wait');

  return true;
}

ajax.widgets.rating.obj.prototype._doRatedState = function() {
  var block = document.createElement('SPAN');
  block.innerHTML = lbl_rated;

  this.elm$
    .width(this.savedWidth)
    .empty()
    .addClass('message')
    .addClass('rated')
    .append(block);

  return true;
}

ajax.widgets.rating.obj.prototype._cleanRatedState = function() {
  if (this.elm$.children().length == 1)
    this.elm$.empty();

  this.elm$
    .removeClass('message')
    .removeClass('rated');

  return true;
}

ajax.widgets.rating.obj.prototype._doErrorState = function() {
  var block = document.createElement('SPAN');
  block.innerHTML = lbl_error;

  this.elm$
    .width(this.savedWidth)
    .empty()
    .addClass('message')
    .addClass('error')
    .append(block);

  return true;
}

ajax.widgets.rating.obj.prototype._cleanErrorState = function() {
  if (this.elm$.children().length == 1)
    this.elm$.empty();

  this.elm$
    .removeClass('message')
    .removeClass('error');

  return true;
}

ajax.widgets.rating.obj.prototype._doCancelState = function() {
  var block = document.createElement('SPAN');
  block.innerHTML = lbl_cancel_vote;

  this.elm$
    .width(this.savedWidth)
    .empty()
    .addClass('message')
    .addClass('cancel')
    .append(block);

  return true;
}

ajax.widgets.rating.obj.prototype._cleanCancelState = function() {
  if (this.elm$.children().length == 1)
    this.elm$.empty();

  this.elm$
    .removeClass('message')
    .removeClass('cancel');

  return true;
}

// onload handler
$(ajax).bind(
  'load',
  function() {
    return ajax.widgets.rating();
  }
);
