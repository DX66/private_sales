/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup product functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_product.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function setProduct(productid, product) {
  
  var d = window.opener ? window.opener.document : document;
  var id = document.cat_form.elements.namedItem('id');
  if (id && id.value) {
    id = id.value;

    if (d.getElementById(id))
      d.getElementById(id).value = productid;

    if (d.getElementById(id+'_product'))
      d.getElementById(id+'_product').value = product;
  }

  var pc = getPopupControl(document.cat_form);
  if (pc)
    return pc.close();

  return window.close();
}

function setProductInfo() {
  var obj = document.cat_form.productid;
  if (obj && obj.value != "")
    return setProduct(obj.options[obj.selectedIndex].value, obj.options[obj.selectedIndex].text);

  alert (err_choose_product);

  return false;
}

function checkCategory() {
  if (document.cat_form && document.cat_form.cat && document.cat_form.cat.selectedIndex == -1) {
    alert (err_choose_category);
    return false;
  }

  return true;
}
