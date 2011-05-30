/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Clean URL functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: check_clean_url.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function checkCleanUrl(field, empty_err, div_style) {
  var err = false;

  if (!field) {
    return true;
  }

  if (typeof empty_err == 'undefined') {
    empty_err = 'Y';
  }

  if (typeof div_style == 'undefined') {
    div_style = 'N';
  }

  if (field.value.length == 0) {
    if (empty_err == 'Y') {
      err = true;
    } else {
      return true;
    }
  }

  if (!err) {
    field.value = field.value.replace(/^\s+/g, '').replace(/\s+$/g, '');
    var html_postfix_regexp = new RegExp('\.html$', 'i');
    if (field.value.search(clean_url_validation_regexp) == -1 || field.value.search(html_postfix_regexp) != -1) {
      err = true;
    }
  }

  if (err) {
    if (div_style == 'N') {
      alert(err_clean_url_wrong_format);
    } else {
      $("#clean_url_error").html(err_clean_url_wrong_format);
    }
    field.focus();
    field.select();
  } else if(div_style != 'N') {
    $("#clean_url_error").html("");
  }

  return !err;
}

function copy_clean_url(from_field, to_field) {
  if (typeof from_field == "undefined" || typeof from_field.value == "undefined") {
    return;
  }

  if (typeof to_field == "undefined" || typeof to_field.value == "undefined") {
    return;
  }

  to_field.value = from_field.value.replace(/[\&]/g, '-and-').replace(/[^a-zA-Z0-9._-]/g, '-').replace(/[-]+/g, '-').replace(/-$/, '');

  return true;
}
