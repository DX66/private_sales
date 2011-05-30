/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Check email script
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: check_email_script.js,v 1.3 2010/07/06 06:33:46 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function checkEmailAddress(field, empty_err) {
  var err = false;

  if (!field) {
    return true;
  }

  if (field.value.length == 0) {
    if (empty_err != 'Y') {
      return true;
    } else {
      err = true;
    }
  }

  if (!err && field.value.replace(/^\s+/g, '').replace(/\s+$/g, '').search(email_validation_regexp) == -1) {
    err = true;
  }

  if (err) {
        markErrorField(field);
    xAlert(txt_email_invalid);
    field.focus();
    field.select();
  }

  return !err;
}

