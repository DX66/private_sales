/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Pop up users
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_users_open.js,v 1.2.2.1 2011/04/27 10:37:10 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function open_popup_users(form, format, force_submit) {
  return window.open ("popup_users.php?form="+form+"&format="+escape(format)+'&force_submit='+(force_submit ? "Y" : ""), "selectusers", "width=700,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
