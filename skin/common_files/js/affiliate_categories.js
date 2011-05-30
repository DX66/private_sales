/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Expand affiliates tree in admin area
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: affiliate_categories.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function xaffCExpand(obj) {
  var ul = $('ul', obj.parentNode.parentNode).get(0);

  if (!ul)
    return true;

  if (ul.style.display == 'none') {
    ul.style.display = '';
    obj.innerHTML = '-';

  } else {
    ul.style.display = 'none';
    obj.innerHTML = '+';
  }

  return false;
}
