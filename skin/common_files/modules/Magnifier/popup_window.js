/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup window functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_window.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/*
$Id: popup_window.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*/

function ResizeFlashMagnifier() {

  var fm = document.getElementById("flash_magnifier");
  if (fm) {

    var window_width = $(window).width();
    var window_height = $(window).height();

    if (window_width > 390)
      fm.width = window_width;

    if (window_height > 405)
      fm.height = window_height;
  }

  return true;
}

$.event.add(
  window,
  'load',
  function() {
    window.focus();
    ResizeFlashMagnifier();
    if ($.browser.msie && parseInt($.browser.version) == 7) {
      $(document.body).css('min-width', 'auto');
    }
  }
);

$.event.add(
  window,
  'resize',
  ResizeFlashMagnifier
);

