/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Debug panel
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: debug.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var _debug_is_extended = true;
var debug_panel_ext_methods = [
  '_getBox',
  'width', 'height', 'position'
];

var debug_panel_ext = {
  init: function() {
    var d = this.appendChild(document.createElement('DIV'));
    d.style.height = ($(this).height() - 22) + 'px';

    d = this.appendChild(document.createElement('DIV'));
    d.style.height = '22px';
    var inp = d.appendChild(document.createElement('INPUT'));
    inp.style.margin = '0px';
    inp.style.padding = '0px';
    inp.style.border = '1px solid black';
    inp.style.height = '18px';
    inp.box = this;
    inp.history = [];
    inp.historyIdx = 0;
    inp.currentValue = false;
    inp.onkeypress = function(e) {
      if (!e)
        e = event;

      switch (e.keyCode) {
        case 13:
          // Enter
          if (this.history.length == 0 || this.history[this.history.length - 1] != this.value)
            this.history[this.history.length] = this.value;

          this.historyIdx = this.history.length;

          try {
            this.box.row(0).html('eval: ' + eval(this.value));
          } catch(e) {
            this.box.row(0).html('eval error: ' + (e.message ? e.message : e));
          }
          break;

        case 38:
          // History up
          if (this.historyIdx > 0) {
            if (this.historyIdx == this.history.length)
              this.currentValue = this.value;

            this.historyIdx--;
            this.value = this.history[this.historyIdx];
          }
          break;

        case 40:
          // History down
          if (this.historyIdx < this.history.length) {
            this.historyIdx++;
            this.value = (this.historyIdx == this.history.length) ? this.currentValue : this.history[this.historyIdx];
          }
          break;
      }

      return true;
    }
  },

  _getBox: function() {
    return this.childNodes[0];
  },

  width: function(w) {
    if (w > 0)
      this.style.width = w + 'px';

    return $(this).width();
  },

  height: function(h) {
    if (h > 0) {
      this.style.height = h + 'px';
      this._getBox().style.height = (h - 22) + 'px';
    }

    return $(this).height();
  },

  position: function(x, y) {
    var pos = posGetPageOffset(this);
    if (x >= 0)
      this.style.left = (x - pos.left + this.offsetLeft) + 'px';
    else
      x = pos.left;

    if (y >= 0)
      this.style.top = (y - pos.top + this.offsetTop) + 'px';
    else
      y = pos.top;

    return [x, y];
  }
};
