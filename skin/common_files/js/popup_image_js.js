/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup image
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_image_js.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function imagesNavigatorInit() {
  var o = new imagesNavigator();
  return o.valid;
}

function imagesNavigator() {
  this.bar = $('.images-viewer-list');
  this.list = $('.images-viewer-icons', this.bar);
  this.larrow = $('.left-arrow', this.bar);
  this.rarrow = $('.right-arrow', this.bar);
  this.image = $('#detailed-image');
  this.first_call = true;
  this.iconw_width = false;
  this.images = imagesNavigatorList;
  this.curFrameBegin = 0;
  this.pc = getPopupControl(this.image.get(0));

  this._selectedVisible = true;

  var ml = parseInt($('a', this.list).css('margin-left'));
  var mr = parseInt($('a', this.list).css('margin-right'));

  this.listBorder = (isNaN(ml) ? 0 : ml) + (isNaN(mr) ? 0 : mr);
  this.listMLeft = 0;

  this.msie6 = $.browser.msie && parseInt($.browser.version) < 7;

  if (!this.msie6) {
    this.listMLeft = parseInt(this.list.css('margin-left'));
    if (isNaN(this.listMLeft))
      this.listMLeft = 0;
  }

  this.links = $('a', this.list);
  this.icons_counter = this.links.length;
  var o = this;
  this.valid = true;
  if (this.icons_counter > 1) {

    this.onresize();

    if (this.pc) {
      $(this.pc).unbind('onresize');
      $(this.pc).bind('onresize', function() { o.onresize() });

    } else {
      window.onresize = function() { o.onresize() }
    }
  }

  this.valid = this.list.length && this.images.length && this.bar.length && this.larrow.length && this.rarrow.length && this.images.length > 1;
  if (!this.valid)
    return false;

  if (this.icons_counter > 1) {
    this.links.each(
      function() {
        var m = this.href.match(/page=(\d+)/);
        if (m)
          this.idx = parseInt(m[1]);
        this.href = 'javascript:void(0);';
        this.onclick = o.change_img;
        this.onmouseover = o.img_over;
        this.onmouseout = o.img_out;
        this.imgNavigator = o;
      }
    );

    this.larrow.get(0).onclick = function() {
      if ($('img', this).hasClass('enabled'))
        o.moveList(-1);

      return false;
    }

    this.rarrow.get(0).onclick = function() {
      if ($('img', this).hasClass('enabled'))
        o.moveList(1);

      return false;
    }

  } else {
    this.listLength = this.icons_counter;
  }

  setTimeout(
    function () {
      return o.resize_window();
    },
    500
  );

  if (window._popup)
    $(window._popup).unbind('onload');

  return true;
}

imagesNavigator.prototype.onresize = function() {
  if (!this.valid)
    return false;


  if (!this.iconw_width) {
    this.iconw_width = this.links.width();

    var ml = parseInt(this.links.css('margin-left'));
    if (!isNaN(ml))
      this.iconw_width += ml;

    var mr = parseInt(this.links.css('margin-right'));
    if (!isNaN(mr))
      this.iconw_width += mr;

    this.iconw_width += 2;
  }

  this.list.width('auto');

  var lw = this.bar.width() - this.larrow.width() - this.rarrow.width() - this.listBorder;

  var iw = this.iconw_width;

  this.listLength = lw > iw ? Math.floor(lw / iw) : 1;

  this.moveList(0);

  if (!this.first_call) {

    // Reposition to selected icons
    if (this._selectedVisible) {
      var cLink = $('.selected', this.list).get(0);
      if (cLink && cLink.style.display == 'none') {
        var cPos = 0;
        this.links.each(
          function() {
            if (this.idx == cLink.idx)
              return false;

            cPos++;
          }
        );

        if (cPos < this.curFrameBegin) {
          this.curFrameBegin = cPos;
          this.moveList(0);

        } else if (this.curFrameBegin + this.listLength <= cPos) {
          this.curFrameBegin = Math.max(cPos - this.listLength + 1, 0);
          this.moveList(0);
        }
      }
    }

    // Change image margin-top
    if (this.pc) {
      this.image.css('margin-top', 0);
      var mt = Math.floor((this.pc.getDim().innerBox.height - this.image.get(0).offsetTop - this.image.height()) / 2);
      if (!isNaN(mt) && mt > 0)
        this.image.css('margin-top', mt);
    }
  }

  if (!this.pc) {
    var mt = Math.floor((getDocumentHeight() - $('#header').height() - $('#footer').height() - this.bar.height() - this.image.height()) / 2);
    if (!isNaN(mt) && mt > 0)
      this.image.css('margin-top', mt);
  }

  var ltw = this.listLength * this.iconw_width;
  if (this.msie6)
    ltw += this.listBorder;

  if (this.msie6)
    var m = Math.floor((this.bar.width() - ltw) / 2);
  else
    var m = Math.floor((lw - ltw + this.listBorder) / 2);

  this.list.width(ltw + this.listLength * 1);
  this.larrow.css('left', m);
  this.list.css('margin-left', m + this.listMLeft);

  if (this.icons_counter > this.listLength) {

    this.larrow.removeClass('hidden');
    this.rarrow.removeClass('hidden');

    if (this.msie6)
      this.rarrow.css('left', ltw + m - this.listBorder / 2);
    else
      this.rarrow.css('left', ltw + m + this.listBorder / 2);

  } else {

    this.larrow.addClass('hidden');
    this.rarrow.addClass('hidden');

  }

  return true;
}

imagesNavigator.prototype.reposition2current = function() {
  if (!this.valid)
    return false;

  var cLink = $('.selected', this.list).get(0);
  if (!cLink || cLink.style.display != 'none')
    return false;

  var cPos = 0;
  this.links.each(
    function() {
      if (this.idx == cLink.idx)
        return false;

      cPos++;
    }
  );

  this.curFrameBegin = cPos;
  this.moveList(0);

  return true;
}

imagesNavigator.prototype.change_img = function() {
  var data = this.imgNavigator.images[this.idx];
  if (!this.imgNavigator || !this.imgNavigator.valid || !data)
    return false;

  var o = this.imgNavigator;
  o.image.get(0).onload = function() {
    this.onload = function() { }
    o.resize_window();

    if (data.height > 0) {
      if (o.pc) {
        var mt = Math.floor(($(o.pc._content).height() - (o.image.offset().top - $(o.pc._content).offset().top) - data.height) / 2);

      } else {
        var mt = Math.floor((getDocumentHeight() - $('#header').height() - $('#footer').height() - o.bar.height() - o.image.height()) / 2);
      }

      if (!isNaN(mt) && mt > 0)
        o.image.css('margin-top', mt);

    }

    return true;
  }

  o.image
    .css('margin-top', 0)
    .attr('src', data.url)
    .attr('alt', $('img', this).attr('alt'));

  o._selectedVisible = true;

  if ($.browser.msie)
    o.image.css('width', (data.width > 0 ? data.width : 'auto')).css('height', (data.height > 0 ? data.height : 'auto'));

  $('a.selected', this.parentNode).removeClass('selected');
  $(this).removeClass('over').addClass('selected');

  return false;
}

imagesNavigator.prototype.img_over = function() {
  if (!$(this).hasClass('selected'))
    $(this).addClass('over');

  return true;
}

imagesNavigator.prototype.img_out = function() {
  $(this).removeClass('over');

  return true;
}

imagesNavigator.prototype.resize_window = function() {
  if (!this.valid)
    return false;

  if (this.pc) {
      this.pc.autoResize(true);

  } else if (!this.msie6) {
    var w = $(document).width() + 20;
    var h = $(document).height() + 20;

    w = Math.max(Math.min(w, screen.width - 150), 420);
    h = Math.max(Math.min(h, screen.height - 150), 420);

    if (getWindowOutWidth() < w || getWindowOutHeight() < h)
      window.resizeTo(w, h);
  }

  if (this.first_call) {
    this.reposition2current();
    var bgsrc = this.image.css('background-image');
    if (this.list.length > 0) {
      this.image.removeAttr('width').removeAttr('height');
    }
    this.image.css('background-image', 'none');
    var h = this.links.height();
    if (this.msie6) {
      this.links.height(h + 4);
      h += 2;
    }

    $('img', this.links).each(
      function() {
        var mt = Math.floor((h - this.height) / 2);
        if (mt > 0) {
          if ($.browser.msie && !this.msie6)
            mt -= 1;

          $(this).css('margin-top', mt);
        }
      }
    );

    this.first_call = false;
    if (this.list.length > 0) {
      $('.selected', this.list).trigger('click');

    } else if (bgsrc) {
      this.image.get(0).src = bgsrc.replace(/^url\(/, '').replace(/\)/, '');
    }
  }

  return true;
}

imagesNavigator.prototype.moveList = function(pos) {
  if (!this.valid)
    return false;

  this.curFrameBegin += pos;
  this.curFrameBegin = Math.min(this.curFrameBegin, this.icons_counter - this.listLength);
  this.curFrameBegin = Math.max(this.curFrameBegin, 0);

  var o = this;
  this.links.each(
    function(i) {
      this.style.display = (i < o.curFrameBegin || i >= (o.curFrameBegin + o.listLength)) ? 'none' : '';
    }
  );

  if (pos != 0)
    this._selectedVisible = $('.selected', this.list).get(0).style.display != 'none';

  if (this.icons_counter >= this.listLength) {

    $('img', this.larrow).attr('class', this.curFrameBegin == 0 ? 'disabled' : 'enabled');
    $('img', this.rarrow).attr('class', (this.curFrameBegin + this.listLength) >= this.icons_counter ? 'disabled' : 'enabled');

  } else {

    $('img', this.larrow).attr('class', 'hidden');
    $('img', this.rarrow).attr('class', 'hidden');

  }

  return true;
}

if (window._popup) {
  $(window._popup).bind('onload', imagesNavigatorInit);

} else {
  $.event.add(window, "load", imagesNavigatorInit);
}

