/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Image preview
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: images_preview.js,v 1.3 2010/07/08 10:24:38 joy Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var ips = {};

// Open images preview by name with specified images
function imagesPreviewShow(name, images, options, currentImage) {
  if (
    typeof(name) == 'undefined' || !name || name.constructor != String || name.length == 0 ||
    typeof(images) == 'undefined' || !images || images.constructor != Array || images.length == 0
  )
    return false;

  if (!ips[name]) {
    ips[name] = new imagesPreview(options);

    for (var i = 0; i < images.length; i++) {
      ips[name].addImage(images[i][0], images[i][1], images[i][2]);
    }
  }

  if (
    typeof(currentImage) != 'undefined' && currentImage.constructor == Number && currentImage >= 0 &&
    ips[name]._ul.childNodes.length > currentImage && ips[name]._ul.childNodes[currentImage]
  ) {
    ips[name]._ul.childNodes[currentImage].select();
  }

  ips[name].show();

  return true;
}

/*
  Images preview widget
*/
function imagesPreview(options) {
  var m;

  this._cache = {};

  // Apply options
  if (typeof(options) != 'undefined' && options.constructor == Object) {

    for (var i in options) {

      if (!hasOwnProperty(options, i))
        continue;

      var val = options[i];

      switch(i) {
        case 'listMode':
          if (val.constructor == String && (val == 'top' || val == 'bottom' || val == 'right' || val == 'left'))
            this.options.listMode = val;
          break;

        case 'iconWidth':
          if (val.constructor == Number && val >= 0)
            this.options.icon.width = val;
          break;

        case 'iconHeight':
          if (val.constructor == Number && val >= 0)
            this.options.icon.height = val;
          break;

        default:
          if (this.options[i].constructor == options[i].constructor && (this.options[i].constructor == Number || this.options[i].constructor == Boolean))
            this.options[i] = options[i];
      }
    }
  }

  var o = this;
  this._msie6 = $.browser.msie && parseInt($.browser.version) < 7;

  this._externalCtrls = $('a,input,select,textarea');

  // Create images preview as DOM subtree
  this._bg = document.body.appendChild(document.createElement('DIV'));
  this._bg.className = 'images-preview-bg';
  this._bg.style.display = 'none';

  // Create background IFRAME (for IE only)
  if (this._msie6) {
    this._bg_iframe = document.body.appendChild(document.createElement('IFRAME'));
    this._bg_iframe.className = 'images-preview-iframe';
    this._bg_iframe.src = images_dir + "/spacer.gif";
    this._bg_iframe.frameBorder = 0;
    this._bg_iframe.frameSpacing = 0;
    this._bg_iframe.marginHeight = 0;
    this._bg_iframe.marginWidth = 0;
    this._bg_iframe.scrolling = 'no';

    this._bg_iframe_modal = document.body.appendChild(document.createElement('IFRAME'));
    this._bg_iframe_modal.className = 'images-preview-iframe-modal';
    this._bg_iframe_modal.contentWindow.document.write(
      '<?xml version="1.0" encoding="iso-8859-1"?>' +
      + '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
      + '<html xmlns="http://www.w3.org/1999/xhtml">'
      + '<body style="background: #000000 none;">'
      + '</body>'
      + '</html>'
    );
    this._bg_iframe_modal.style.display = 'none';
  }

  // Base box
  this._base = document.createElement('DIV');
  this._base.className = 'images-preview list-mode-' + this.options.listMode;
  this._base._imagesPreview = this;
  this._base.style.display = 'none';

  document.body.appendChild(this._base);

  // 'Please wait' splash box
  this._wait = this._base.appendChild(document.createElement('DIV'));
  this._wait.className = 'wait';

  // Close button
  this._close = this._base.appendChild(document.createElement('A'));
  this._close.className = 'close';
  this._close.innerHTML = lbl_close ? lbl_close : 'close';
  this._close.href = "javascript:void(0);";

  if (typeof(window.lbl_close_window) != 'undefined' && lbl_close_window)
    this._close.title = lbl_close_window;

  this._close.onclick = function() {
    o.hide();
    return false;
  }

  // Icons list box with ...
  this._listBox = this._base.appendChild(document.createElement('DIV'));
  this._listBox.className = 'list-box';
  this._listBox.style.display = 'none';

  // ... left arrow
  this._larrow = this._listBox.appendChild(document.createElement('A'));
  this._larrow.className = 'arrow left hidden';

  if (typeof(window.lbl_previous) != 'undefined' && lbl_previous)
    this._larrow.title = lbl_previous;

  this._larrow.onclick = function(e) {
    if (!e)
      e = event;

    if (e.stopPropagation)
      e.stopPropagation();
    else
      e.cancelBubble = true;

    o._prevFocusElm = this;

    if (!$.browser.msie && window.getSelection)
       window.getSelection().removeAllRanges();

    if (!$(this).hasClass('left-disabled') && !$(this).hasClass('hidden'))
      o.setPos(-1);
  }

  // ... internal box
  var div2 = this._listBox.appendChild(document.createElement('DIV'));
  div2.className = 'list-subbox';

  // ... 'unordered list' element
  this._ul = div2.appendChild(document.createElement('UL'));
  this._cache.ulHeight = this.options.icon.height + 2;
  this._ul.style.height = this._cache.ulHeight + 'px';
  div2.style.height = this._cache.ulHeight + 'px';
  this.lastIdx = 0;

  this._ul.onclick = function() {
    if (!$.browser.msie && window.getSelection)
       window.getSelection().removeAllRanges();
  }

  // ... right arrow
  this._rarrow = this._listBox.appendChild(document.createElement('A'));
  this._rarrow.className = 'arrow right hidden';

  if (typeof(window.lbl_next) != 'undefined' && lbl_next)
    this._rarrow.title = lbl_next;

  this._rarrow.onclick = function(e) {
    if (!e)
      e = event;

    if (e.stopPropagation)
      e.stopPropagation();
    else
      e.cancelBubble = true;

    o._prevFocusElm = this;

    if (!$.browser.msie && window.getSelection)
       window.getSelection().removeAllRanges();

    if (!$(this).hasClass('right-disabled') && !$(this).hasClass('hidden'))
      o.setPos(1);
  }

  // Image box
  this._imgBox = this._base.appendChild(document.createElement('DIV'));
  this._imgBox.className = 'box';

  // Image element
  this._img = this._imgBox.appendChild(document.createElement('IMG'));
  this._img.imagesPreview = this;
  this._img.onclick = function() {
    window.open(this.src, 'imagespreview', 'toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');
  }

  // Bind window.onresize event
  jQuery.event.add(
    window,
    "resize",
    function(e) {
      return o.resize(e ? e : event);
    }
  );

  // Collect extrernal controllers
  this._prevFocusElm = this._close;
  if (o._prevFocusElm.focus) {
    try {
      this._prevFocusElm.focus();
    } catch(e) { }
  }

  $('a,input,select,textarea', this._base).bind(
    'focus',
    function() {
      o._prevFocusElm = this;
    }
  );

  this._restoreFocus = function() {
    if (o._prevFocusElm && o._prevFocusElm.focus) {
      var success = false;
      try {
        o._prevFocusElm.focus();
        success = true;
      } catch(e) {
      }

      if (!success) {
        try {
          o._close.focus();
        } catch(e) {
        }
      }
    }
  }
  this._externalCtrls.bind('focus', this._restoreFocus);

  this._tmpImage.imagesPreview = this;

}

// Default options
imagesPreview.prototype.options = {
  isModal: true,
  icon: {
    width: 64,
    height: 52
  },
  useCache: true,
  useWait: true,
  waitTTL: 20,
  waitPeriod: 400,
  listMode: 'top'
};

imagesPreview.prototype._tmpImage = new Image();
imagesPreview.prototype._cache = {};

// Add image
imagesPreview.prototype.addImage = function(src, thumbnail, alt) {
  if (typeof(src) == 'undefined' || src.constructor != String || src.length == 0)
    return false;

  if (typeof(thumbnail) == 'undefined' || !thumbnail || thumbnail.constructor != String)
    thumbnail = src;

  if (typeof(alt) == 'undefined' || !alt || alt.constructor != String)
    alt = null;

  var li = this._ul.appendChild(document.createElement('LI'))

  li.className = 'loading';
  if (alt)
    li.title = alt;

  li.imagesPreview = this;
  li._idx = this.lastIdx;
  this.lastIdx++;

  li._src = src;

  var img = li.appendChild(document.createElement('IMG'));

  li._img = document.createElement('IMG');
  li._img.item = li;
  li._img.onload = this._iconLoaded;
  li._img.src = thumbnail;

  li.onclick = this._itemClick;
  if ($.browser.msie) {
    li.onmouseover = this._itemOver;
    li.onmouseout = this._itemOut;
  }

  li.style.width = this.options.icon.width + 'px';
  li.style.height = this.options.icon.height + 'px';

  li.select = this._select;

  if (this._ul.childNodes.length > 1)
    this._listBox.style.display = '';

  if (this._ul.childNodes.length == 1)
    li.select();

  this.resize();

  return li._idx;
}

// Remove image
imagesPreview.prototype.removeImage = function(idx) {
  if (typeof(idx) == 'undefined' || idx.constructor != Number)
    return false;

  for (var i = 0; this._ul.childNodes.length; i++) {
    if (this._ul.childNodes[i]._idx == idx) {
      var isSelected = $(this._ul.childNodes[i]).hasClass('selected');
      this._ul.removeChild(this._ul.childNodes[i]);
      if (isSelected) {
        if (this._ul.childNodes.length == 0) {
          this.hide();
  
        } else if (this._ul.childNodes.length == 1) {
          this._listBox.style.display = 'none';
          this._ul.childNodes[0].select();

        } else if (isSelected) {
          if (i > 0)
            i--;
          else
            i++;

          this._ul.childNodes[i].select();
        }
      }

      this.resize();

      return true;
    }
  }

  return false;
}

// Show widget
imagesPreview.prototype.show = function() {
  if (this._ul.childNodes.length <= 0 || this.isVisible())
    return false;

  if (!$.browser.msie) {
    $(document).bind('selectstart', this._selectHandler);
  }

  if (this.options.isModal) {
    this._bg.style.display = '';
    if (this._bg_iframe_modal)
      this._bg_iframe_modal.style.display = '';
  }

  if (this._bg_iframe)
    this._bg_iframe.style.display = '';

  this._base.style.display = '';

  var hideWait = false;
  if (this.options.useWait && this._ul.childNodes.length > 1 && !this.isIconsLoaded()) {
    hideWait = true;
    this._wait.style.display = '';
    this._cache.waitTTL = this.options.waitTTL;
    var o = this;
    setTimeout(function() { o.hideWait(); }, this.options.waitPeriod);

  } else {
    this._wait.style.display = 'none';
  }

  this.resize();

  if (!hideWait)
    this.checkImagePosition();

  var st = Math.max(document.documentElement ? document.documentElement.scrollTop : 0, document.body ? document.body.scrollTop : 0);
  var t = Math.min(st + this._cache.wndTop, getDocumentHeight() - $(this._base).height() - this._cache.wndBottom);
  $(this._base).css('top', t + 'px');
  if (this._msie6) {
    $(this._bg_iframe).css('top', t + 'px');
  }

  this._externalCtrls.bind('focus', this._restoreFocus);

  return true;
}

// Hide 'Please wait' splash box
imagesPreview.prototype.hideWait = function() {
  if (!this.options.useWait || this.isIconsLoaded() || this._cache.waitTTL-- < 0) {
    this._wait.style.display = 'none';

    this.checkImagePosition();

    return true;
  }

  var o = this;
  setTimeout(function() { o.hideWait(); }, this.options.waitPeriod);

  return true;
}

// Hide widget
imagesPreview.prototype.hide = function() {
  if (!this.isVisible())
    return false;

  if (this._bg.style.display == '') {
    this._bg.style.display = 'none';
    if (this._bg_iframe_modal)
      this._bg_iframe_modal.style.display = 'none';
  }

  if (this._bg_iframe)
    this._bg_iframe.style.display = 'none';

  this._base.style.display = 'none';

  this._externalCtrls.unbind('focus', this._restoreFocus);

  if (!$.browser.msie) {
    $(document).unbind('selectstart', this._selectHandler);
  }

  return true;
}

// Get widget visibility status
imagesPreview.prototype.isVisible = function() {
  return this._base.style.display == '';
}

// Resize widget (based on currenct window size) and normalize internal sizes on widget's elements
imagesPreview.prototype.resize = function(e) {
  if (!this.isVisible())
    return false;

  if (e && $.browser.msie) {
    var o = this;
    return setTimeout(
      function() {  
        return o.resize();
      },
      200
    );
  }

  var w = getWindowWidth();
  var h = getWindowHeight();

  // Scroll bar width/height compensation
  if (!$.browser.msie) {
    if (getDocumentWidth() > w)
      h -= 20;

    if (getDocumentHeight() > h)
      w -= 20;
  }

  if (!this.options.useCache || !this._cache.wndTop) {
    this._cache.wndTop = parseInt($(this._base).css('top'));
    this._cache.wndRight = parseInt($(this._base).css('right'));
    this._cache.wndBottom = parseInt($(this._base).css('bottom'));
    this._cache.wndLeft = parseInt($(this._base).css('left'));

    this._cache.imgBoxTop = parseInt($(this._imgBox).css('margin-top'));
    this._cache.imgBoxRight = parseInt($(this._imgBox).css('margin-right'));
    this._cache.imgBoxBottom = parseInt($(this._imgBox).css('margin-bottom'));
    this._cache.imgBoxLeft = parseInt($(this._imgBox).css('margin-left'));

    this._cache.wndMinWidth = parseInt($(this._base).css('min-width'));
    if (isNaN(this._cache.wndMinWidth))
      this._cache.wndMinWidth = 0;

    this._cache.wndMinHeight = parseInt($(this._base).css('min-height'));
    if (isNaN(this._cache.wndMinHeight))
      this._cache.wndMinHeight = 0;
  }

  var bw = Math.max(w - this._cache.wndLeft - this._cache.wndRight, this._cache.wndMinWidth);
  var bh = Math.max(h - this._cache.wndTop - this._cache.wndBottom, this._cache.wndMinHeight);
  $(this._base).width(bw);
  $(this._base).height(bh);

  this._bg.style.display = 'none';
  if (this._bg_iframe_modal)
    this._bg_iframe_modal.style.display = 'none';

  if (this.options.isModal) {
    if ($.browser.msie) {
      this._bg.style.width = document.body.scrollWidth + 'px';
      this._bg.style.height = document.body.scrollHeight + 'px';
      if (this._bg_iframe_modal) {
        this._bg_iframe_modal.style.width = document.body.scrollWidth + 'px';;
        this._bg_iframe_modal.style.height = document.body.scrollHeight + 'px';
      }

    } else {
      this._bg.style.width = document.documentElement.scrollWidth + 'px';
      this._bg.style.height = document.documentElement.scrollHeight + 'px';
    }

    this._bg.style.display = '';
    if (this._bg_iframe_modal)
      this._bg_iframe_modal.style.display = '';
  }

  if (this._msie6) {
    this._bg_iframe.style.width = bw + 'px';
    this._bg_iframe.style.height = bh + 'px';
  }

  var ih = bh - this._cache.imgBoxTop - this._cache.imgBoxBottom;
  if (this._ul.childNodes.length > 1) {
    if (!this.options.useCache || !this._cache.listTop)
      this._cache.listTop = parseInt($(this._listBox).css('margin-top'));

    ih -= this._cache.ulHeight + this._cache.listTop;
  }

  this._imgBox.style.height = ih + 'px';

  this._cache.imgBoxWidth = $(this._imgBox).width();
  this._cache.imgBoxHeight = ih;

  if (this._img.height > 0) {
    if (this._img.height < this._img._oheight || this._img.width < this._img._owidth) {
      var n = getProperDimensions(this._img._owidth, this._img._oheight, this._cache.imgBoxWidth, ih, 1);
      if (n[0] != this._img.width || n[1] != this._img.height) {
        this._img.width = n[0];
        this._img.height = n[1];
      }
    }
  }

  this._centerImage();

  if (this._ul.childNodes.length > 1) {
    if (!this.options.useCache || !this._cache.listIsPrepared) {
      var lah = $(this._larrow).height();
      if (isNaN(lah))
        lah = 13;

      var rah = $(this._rarrow).height();
      if (isNaN(rah))
        rah = 13;

      this._larrow.style.top = Math.floor((this._cache.ulHeight - lah) / 2) + 'px';
      this._rarrow.style.top = Math.floor((this._cache.ulHeight - rah) / 2) + 'px';
      this._cache.listIsPrepared = true;
    }

    var cnt = this._ul.childNodes.length;

    var w = this.getItemWidth();

    this._ul.parentNode.style.marginLeft = '0px';

    $(this._ul.parentNode).width(50);

    var lbw = $(this._listBox).width();
    var arrowsHidden = true;
    var arrowsWidth = 0;
  
    if (w * cnt > lbw) {
      $(this._larrow).removeClass('hidden');
      $(this._rarrow).removeClass('hidden');
      arrowsHidden = false;

      if (!this.options.useCache || !this._cache.larrowWidth)
        this._cache.larrowWidth = $(this._larrow).width();

      if (!this.options.useCache || !this._cache.rarrowWidth)
        this._cache.rarrowWidth = $(this._rarrow).width();

      arrowsWidth = this._cache.larrowWidth + this._cache.rarrowWidth;

    } else {
      $(this._larrow).addClass('hidden');
      $(this._rarrow).addClass('hidden');

      $(this._ul.parentNode).css('margin-left', 0);
    }

    var cnt2 = Math.floor((lbw - arrowsWidth) / w);

    // IE6 fix
    var __brdr = this._msie6 ? 4 : 1;
    $(this._ul).width((w + __brdr) * cnt);

    var cur = typeof(this._cache.pos) == 'undefined' ? 0 : this._cache.pos;
    if (cur + cnt2 >= this._ul.childNodes.length) {
      var dw = $(this._ul.lastChild).position().left + w - $(this._ul.childNodes[cur]).position().left;
    } else {
      var dw = $(this._ul.childNodes[cur + cnt2]).position().left - $(this._ul.childNodes[cur]).position().left;
    }

    this._cache.frameLength = cnt2;

    if (this._msie6)
      $(this._ul.parentNode).width(dw - 9);
    else
      $(this._ul.parentNode).width(dw);

    var m = lbw > dw ? Math.floor((lbw - dw) / 2) : 0;

    if (!$.browser.msie || this._msie6)
      $(this._ul.parentNode).css('margin-left', m);

    if (!arrowsHidden) {
      this._larrow.style.left = (m - this._cache.larrowWidth) + 'px';

      m -= this._cache.rarrowWidth;
      if (this._msie6)
        m += 50;

      this._rarrow.style.right = m + 'px';
    }

    this.setPos(0);

  }

  return true;
}

// Check selected image position in images list after popup open
imagesPreview.prototype.checkImagePosition = function() {
  if ($('li.selected', this._ul).length == 0)
    return false;

  var c = $('li.selected', this._ul).get(0)._idx;
  var pos = false;

  for (var i = 0; i < this._ul.childNodes.length && pos === false; i++) {
    if (this._ul.childNodes[i]._idx == c)
      pos = i;
  }

  if (pos === false)
    return false;

  var cur = typeof(this._cache.pos) == 'undefined' ? 0 : this._cache.pos;
  if (typeof(this._cache.frameLength) == 'undefiend') {
    this.getItemWidth();
    var cnt2 = round($(this._ul.parentNode).width() / this._cache.itemWidth);
  } else {
    var cnt2 = this._cache.frameLength;
  }

  if (pos < cur || pos > cur + cnt2)
    this.setPos(pos, true);

  return true;
}

// Switch to selected image (by image idx)
imagesPreview.prototype.switchImage = function(idx) {
  if (typeof(idx) == 'undefined' || idx.constructor != Number)
    return false;

  for (var i = 0; i < this._ul.childNodes.length; i++) {
    if (this._ul.childNodes[i]._idx == idx) {
      return this._ul.childNodes[i].select();
    }
  }

  return false;
}

// Set frame of visibility position in images list
imagesPreview.prototype.setPos = function(pos, absolute) {
  var cur = typeof(this._cache.pos) == 'undefined' ? 0 : this._cache.pos;

  if (typeof(this._cache.frameLength) == 'undefiend') {
    this.getItemWidth();
    var cnt2 = round($(this._ul.parentNode).width() / this._cache.itemWidth);
  } else {
    var cnt2 = this._cache.frameLength;
  }

  var len = Math.max(this._ul.childNodes.length - cnt2, 0);

  if (!absolute)
    pos += cur;

  pos = Math.min(Math.max(0, pos), len);

  if (pos === this._cache.pos)
    return true;

  var left = -1 * $(this._ul.childNodes[pos]).position().left;

  this._ul.style.left = left + 'px';

  var ml = parseInt($(this._ul.childNodes[pos]).css('margin-left'));
  if (isNaN(ml))
    ml = 0;

  var pl = $(this._ul.childNodes[pos]).offset().left - ml;
  var bl = $(this._ul.parentNode).offset().left;
  if (pl != bl)
    this._ul.style.left = (left - (pl - bl)) + 'px';

  this._cache.pos = pos;

  if (pos == 0) {
    if (this._cache.larrowStatus != 2) {
      $(this._larrow).addClass('left-disabled');
      this._cache.larrowStatus = 2;
    }

  } else if (this._cache.larrowStatus != 1) {
    $(this._larrow).removeClass('left-disabled');
    this._cache.larrowStatus = 1;
  }

  if (pos == len) {
    if (this._cache.rarrowStatus != 2) {
      $(this._rarrow).addClass('right-disabled');
      this._cache.rarrowStatus = 2;
    }

  } else if (this._cache.rarrowStatus != 1) {
    $(this._rarrow).removeClass('right-disabled');
    this._cache.rarrowStatus = 1;
  }

  return true;
}

// Check icons list loading status
imagesPreview.prototype.isIconsLoaded = function() {
  return $('li.loading', this._ul).length == 0;
}

// Get maximum image size
imagesPreview.prototype.getImageMaxSize = function() {
  if (!this._cache.imgBoxWidth)
    this._cache.imgBoxWidth = $(this._imgBox).width();

  if (!this._cache.imgBoxHeight)
    this._cache.imgBoxHeight = $(this._imgBox).height();

  return [this._cache.imgBoxWidth, this._cache.imgBoxHeight];
}

// Get average list item width in images list
imagesPreview.prototype.getItemWidth = function() {
  if (this.options.useCache && typeof(this._cache.itemWidth) != 'undefined')
    return this._cache.itemWidth;

  var li = $(this._ul.childNodes[0]);

  var w = false;
  if (this._msie6) {
    var w = parseInt(li.get(0).style.width);
    if (isNaN(w) || w < 1)
      w = false;
  }

  if (!w)
    w = li.width();

  var bl = parseInt(li.css('border-left-width'));
  if (!isNaN(bl))
    w += bl;

  var br = parseInt(li.css('border-right-width'));
  if (!isNaN(br))
    w += br;

  var ml = parseInt(li.css('margin-left'));
  if (!isNaN(ml))
    w += ml;

  var mr = parseInt(li.css('margin-right'));
  if (!isNaN(mr))
    w += mr;

  this._cache.itemWidth = w;

  return this._cache.itemWidth;
}

/*
  Private methods
*/
// Select image
imagesPreview.prototype._select = function() {
  $('li.selected', this.parentNode).removeClass('selected');

  $(this).addClass('selected');

  $(this.imagesPreview._imgBox).addClass('loading');
  this.imagesPreview._img.style.display = 'none';
  
  this.imagesPreview._tmpImage.onload = this.imagesPreview._tmpImageLoaded;
  this.imagesPreview._tmpImage.src = this._src;

  if (this.title)
    this.imagesPreview._img.alt = this.imagesPreview._img.title = this.title;

  return true;
}

// List item onclick event handler
imagesPreview.prototype._itemClick = function() {
  return this.select();
}

// List item onmouseover event handler
imagesPreview.prototype._itemOver = function() {
  $(this).addClass('over');
  return true;
}

// List item onmouseout event handler
imagesPreview.prototype._itemOut = function() {
  $(this).removeClass('over');
  return true;
}

// Vertical image align
imagesPreview.prototype._centerImage = function(h) {
  var bh = this._cache.imgBoxHeight ? this._cache.imgBoxHeight : $(this._imgBox).height();
  var ih = h ? h : this._img.height;

  var m = 0;
  if (bh > 0 && ih > 0 && ih < bh)
    m = Math.floor((bh - ih) / 2)

  $(this._img).css('margin-top', m);

  return true;
}

// Temporary image onload event handler
imagesPreview.prototype._tmpImageLoaded = function() {
  var max = this.imagesPreview.getImageMaxSize();
  var n = getProperDimensions(this.width, this.height, max[0], max[1], 1);

  var o = this.imagesPreview;

  this.imagesPreview._img.onload = function() {
    return o._imageLoaded.call(this, n[0], n[1]);
  }

  this.imagesPreview._img.src = this.src;

  this.imagesPreview._img._owidth = this.width;
  this.imagesPreview._img._oheight = this.height;

  this.imagesPreview._img.width = n[0];
  this.imagesPreview._img.height = n[1];

  $(this.imagesPreview._imgBox).removeClass('loading');

  return true;
}

// Image onload event handler
imagesPreview.prototype._imageLoaded = function(w, h) {
  this.imagesPreview._centerImage(h);

  this.style.display = '';

  return true;
}

// Temporary icon onload event handler
imagesPreview.prototype._iconLoaded = function() {
  var n = getProperDimensions(this.width, this.height, this.item.imagesPreview.options.icon.width - 2, this.item.imagesPreview.options.icon.height - 2);
  var img = $('img', this.item).get(0);
  var o = this.item.imagesPreview;

  img.onload = function() {
    o._iconLoaded2.call(this, n[0], n[1]);
  }
  img.src = this.src;
  img.width = n[0];
  img.height = n[1];

  return true;
}

// Icon onload event handler
imagesPreview.prototype._iconLoaded2 = function(w, h) {
  this.width = w;
  this.height = h;
  this.style.width = w + 'px';
  this.style.height = h + 'px';

  var mt = Math.max(0, Math.floor((this.parentNode.imagesPreview.options.icon.height - h) / 2));
  if (mt > 0)
    this.style.marginTop = mt + 'px';

  $(this.parentNode).removeClass('loading');

  if (this.parentNode.imagesPreview.isIconsLoaded())
    $(this.parentNode.imagesPreview).trigger('iconsLoaded');

  return true;
}

imagesPreview.prototype._selectHandler = function() {
  return false;
}

