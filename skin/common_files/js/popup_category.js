/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup category
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_category.js,v 1.3 2010/06/11 13:57:51 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function popup_category(field_categoryid, field_category) {

  return popupOpen(
    'popup_category.php?field_categoryid=' + field_categoryid + '&field_category=' + field_category,
    '',
    {
      width: 800,
      height: 600,
      draggable: true
    }
  );
}
