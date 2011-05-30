/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Cart page js functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: cart.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function updateCartItem(id) {
  if (!document.cartform)
    return false;

  var quantity = document.cartform.elements.namedItem('productindexes[' + id + ']');
  if (!quantity)
    return false;

  var url = 'cart.php?action=update&productindexes[' + id + ']=' + quantity.value;

  /* for Gift Registry module */
  var eventMark = document.cartform.elements.namedItem('event_mark[' + id + ']');
  if (eventMark) {
    url += '&event_mark[' + id + ']=' + eventMark.value;
  }

  if ($.browser.msie) {
    setTimeout(
      function() {
        self.location = url;
      },
      200
    );

  } else {
    self.location = url;
  }

  return false;
}

