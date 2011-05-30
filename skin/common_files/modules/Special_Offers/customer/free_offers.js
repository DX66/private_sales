/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Add/remove free offers
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: free_offers.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function add_remove_free_offer(offerid, flag) {

  bp_balance += (flag ? -1 : 1) * cart_free_offers[offerid];

  for (var x in cart_free_offers) {
    if (hasOwnProperty(cart_free_offers, x) && x != offerid) {
      var offer_box = document.cartform.elements['free_offers[' + x + ']'];
      if (offer_box)
        offer_box.disabled = cart_free_offers[x] > bp_balance && !offer_box.checked;
    }
  }

  var remained_bp_box = document.getElementById('remained_bp');
  if (remained_bp_box)
    remained_bp_box.innerHTML = bp_balance;

  return true;
}

function apply_free_offers() {
  document.cartform.action.value = 'apply_free_offers';
  document.cartform.submit();
}
