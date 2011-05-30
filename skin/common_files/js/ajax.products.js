/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Ajax Products list widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ajax.products.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

ajax.widgets.products = function(elm) {
  if (!elm) {
    elm = $('.products');

  } else {
    elm = $(elm);
  }

  elm.each(
    function() {
      if (!this.productsWidget)
        new ajax.widgets.products.obj(this);
    }
  );

  return true;
}

ajax.widgets.products.obj = function(elm) {
  this.elm = elm;
  this.elm$ = $(elm);

  elm.productsWidget = this;

  this.type = false;

  if (this.elm$.hasClass('products-list')) {
    this.type = 'list';

  } else if (this.elm$.hasClass('products-table')) {
    this.type = 'matrix';

  }

  this._getProducts();
}

ajax.widgets.products.obj.prototype.elm = false;
ajax.widgets.products.obj.prototype.products = [];
ajax.widgets.products.obj.prototype.type = false;

ajax.widgets.products.obj.prototype.isReady = function() {
  return this.type && this.products.length > 0 && this.checkElement();
}

ajax.widgets.products.obj.prototype.checkElement = function(elm) {
  if (!elm)
    elm = this.elm;

  return typeof(elm) != 'undefiend' && elm.tagName && $(elm).hasClass('products');
}

/* Private */

// Widget :: get products
ajax.widgets.products.obj.prototype._getProducts = function() {
  if (!ajax.widgets.product)
    return false;

  this.products = [];

  var arr = [];

  if (this.type == 'list') {

    // Plain list
    arr = this.elm$.children('.item').get();

  } else if (this.type == 'matrix') {

    // Matrix
    var vSize = -1;
    for (var i = 1; i < this.elm.rows.length && vSize < 0; i++) {
      if ($(this.elm.rows[i]).hasClass('product-name-row'))
        vSize = i;
    }

    if (vSize < 0)
      vSize = this.elm.rows.length;

    var hSize = this.elm.rows[0].cells.length;
    var size = vSize * hSize;

    for (var r = 0; r < this.elm.rows.length; r++) {
      for (var c = 0; c < this.elm.rows[r].cells.length; c++) {
        var pn = Math.floor(r / vSize) * vSize + c;
        if (!arr[pn])
          arr[pn] = [];

        arr[pn][arr[pn].length] = this.elm.rows[r].cells[c];
      }
    } 

  }

  for (var i = 0; i < arr.length; i++) {
    var p = new ajax.widgets.product(arr[i]);
    this.products[this.products.length] = p;
  }

  return this.products.length > 0;
}


// onload handler
$(ajax).bind(
  'load',
  function() {
    return ajax.widgets.products();
  }
);
