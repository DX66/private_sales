/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Functions for the Flyout menus module
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: func.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// $('ul li ul', $('a').filter(function() { return this.href.search(/home\.php\?cat=75/) != -1; }).eq(0).parents('li').eq(0)).eq(0).css('display')

function switchSubcatLayer(obj) {
  $(obj.parentNode).toggleClass('closed');
  return false;
}

$(document).ready(
  function() {
    if ($.browser.msie || $.browser.safari) {

      var isIE6 = $.browser.msie && parseInt($.browser.version) < 7;
      var isStyleControl = $.browser.msie;
      var isManualControl = $.browser.msie && parseInt($.browser.version) >= 7;
      var isOperateIFrame = $.browser.msie && parseInt($.browser.version) < 7;

      // Currenct center block height
      var normalPageHeight = $("#content-container").height() + $("#footer").height();

      var diffHeight = $("#header").height() - 10;
      if (isIE6) {
        diffHeight -= $("#footer").height();
      }

      // Initialization
      $('.fancycat-icons-e ul.fancycat-icons-level-0 li').each(
        function() {
          this._ul = $('ul', this).eq(0);
          if (this._ul.length) {
            this._centerMain = $(this).parents('#center-main').get(0);
          } else {
            this._ul = false;
          }
        }
      );

      // Hover event
      $('.fancycat-icons-e ul.fancycat-icons-level-0 li').hover(
        function() {
          if (isStyleControl) {
            this.className += ' over';
            if (isManualControl && this._ul) {
              this._ul.css('display', 'block');
            }
          }

          if (this._ul) {
            if (!this._iframe) {

              // Calculate required center block height
              var oTop = 0;
              if (isIE6) {
                oTop = this._ul.offset().top;

              } else {
                var obj = this._ul;

                do {
                  oTop += obj.attr('offsetTop');
                  obj = obj.offsetParent();
                } while ( obj.attr('tagName') != 'BODY' );

              }

              this.requiredHeight = this._ul.height() + oTop - diffHeight;

              if (isOperateIFrame) {

                // Create background iframe element
                var pos = this._ul.position();
                this._iframe = this.insertBefore(document.createElement('IFRAME'), this._ul.get(0));
                $(this._iframe)
                  .css('left', pos.left)
                  .css('top', pos.top)
                  .width(this._ul.width())
                  .height(this._ul.height());
              } else {
                this._iframe = true;
              }
            }

            if (this._iframe && isStyleControl) {
              this._iframe.className = 'over';
            }

            if (this.requiredHeight > normalPageHeight) {

              // Extend center block
              $("#content-container").height(this.requiredHeight);

              if (this._centerMain) { // Fashion mosaic skin, home page
                $(this._centerMain).height(this.requiredHeight);
              }
            }
          }
        },
        function() {
          if (isStyleControl) {
            this.className = this.className.replace(/ over/, '');
            if (isManualControl && this._ul) {
              this._ul.css('display', 'none');
            }
          }

          if (this._iframe) {
            if (isStyleControl) {
              this._iframe.className = '';
            }
            if (this.isHighUL) {
              document.getElementById('content-container').style.height = '';
              if (this._centerMain) {
                this._centerMain.style.height = '';
              }
            }
          }
        }
      );

      // Mark first list items
      if ($('ul.fancycat-icons-level-1').parents('li').get(0)) {
        $('ul.fancycat-icons-level-1').parents('li').get(0).isHighUL = true;
      } 
    }

    if (typeof(window.catexp) != 'undefined' && catexp > 0) {
      $('.fancycat-icons-c #cat-layer-' + catexp).parents('li.closed').removeClass('closed');
    }
  }
);
