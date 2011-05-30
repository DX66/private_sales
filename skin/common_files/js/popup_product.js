/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup product
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_product.js,v 1.3 2010/06/11 13:57:51 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function popup_product(field_productid, field_product, only_regular) {
  return popupOpen(
    'popup_product.php?field_productid=' + field_productid + '&field_product=' + field_product + '&only_regular=' + only_regular,
    '',
    { 
      width: 800,
      height: 600,
      draggable: true
    }
  );
}
