/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Product and category choosing
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: product_n_category.js,v 1.2 2010/05/27 14:09:40 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function add_set(form_name) {

  curr_form = document.forms[form_name];

  curr_form.action.value = 'add_set';
  curr_form.submit();
}

function delete_set(set_id, form_name) {

  curr_form = document.forms[form_name];

  curr_form.setid.value = set_id;
  curr_form.action.value = 'delete_set';
  curr_form.submit();
}

function add_param(param_box, form_name, setid, type, only_regular_products) {
  
  curr_form = document.forms[form_name];

  param_box = form_name + '.' + param_box;
  var wnd_handle = (type == 'C') ? popup_category(param_box) : popup_product(param_box, '', only_regular_products);

  if (wnd_handle) {
    curr_form.setid.value = setid;
    curr_form.action.value = 'add';
    document.form_to_submit = form_name;
  }
}

function delete_param(param_id, form_name) {

  curr_form = document.forms[form_name];

  param_box = 'param_del[' + param_id + ']';
  param_box = curr_form.elements[param_box];

  if (param_box) {
    param_box.checked = 'checked';
    if (checkMarks(curr_form, new RegExp('^param_del', 'gi'))) {
      curr_form.action.value = 'delete';
      curr_form.submit();
    }
  }
}

function set_hidden_value(param_box, form_name, flag) {

  curr_form = document.forms[form_name];
  param_box = curr_form.elements[param_box];

  if (param_box) {
    param_box.value = flag ? 'Y' : 'N';
  }
}

function save_changes(set_id, form_name) {

  curr_form = document.forms[form_name];

  curr_form.setid.value = set_id;
  curr_form.submit();  
}

function use_offer_cnd_sets(flag, suffix) {

  product_sets_box = document.getElementById('product_sets_' + suffix);

  if (product_sets_box) {
    product_sets_box.style.display = flag ? 'none' : '';
  }
}
