/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup slot products
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_slot_products.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function popupSlotProducts (slot, productid) {
    return window.open("popup_slot_products.php?slot="+slot+"&productid="+productid, "selectproduct", "width=700,height=550,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no");
}

function setDefaultProduct(productid, product_name) {
  if (window.opener && productid) {
    window.opener.document.getElementById('def_pid').value = productid;
    window.opener.document.getElementById('def_name').innerHTML = '<a href="product.php?productid='+ productid + '" target="_blank">' + product_name +'</a>';
    window.opener.document.getElementById('save_msg').innerHTML = lbl_pconf_save_msg;
    window.opener.document.getElementById('change_btn').style.display = '';
    window.opener.document.getElementById('delete_btn').style.display = '';
    window.opener.document.getElementById('choose_btn').style.display = 'none';
    window.close();
  }
}

function removeDefaultProduct() {
    document.getElementById('def_pid').value = '';
    document.getElementById('def_name').innerHTML = lbl_pconf_default_product_not_defined;
    document.getElementById('save_msg').innerHTML = lbl_pconf_save_msg;
    document.getElementById('change_btn').style.display = 'none';
    document.getElementById('delete_btn').style.display = 'none';
    document.getElementById('choose_btn').style.display = '';

}
