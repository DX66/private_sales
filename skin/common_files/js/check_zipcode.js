/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Check zipcode
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: check_zipcode.js,v 1.5 2010/07/26 12:21:16 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// check_zip_code_rules is defined in check_zipcode_js.tpl

function check_zip_code_field(cnt, zip, address) {
  var zip_error = false;
  var rules = {};
  var message = true;

  if (!isset(address))
    var address = '';

  // define country
  var c_code = config_default_country;
  if (cnt && cnt.options) {
    if ((cnt.options.length > 0) && (cnt.selectedIndex < cnt.options.length)) {
      c_code = cnt.options[cnt.selectedIndex].value;
    }
  }

  // show/hide zip4 section
  if (!zip) {
    return true;
  }

  var zip4 = $('#' + $(zip).attr('id').replace(/zipcode/,'zip4'));
  if (zip4 && zip4 != 'undefined') {
    $('#' + $(zip4).attr('id') + '_container').css('display', c_code == 'US' ? '' : 'none');
  }

  if (!zip.value || zip.value == '')
    return true;

  zip.value = zip.value.replace(/^\s+/g, '').replace(/\s+$/g, '');

  // bt:83803 According http://en.wikipedia.org/wiki/Postal_codes#Character_sets
  if (!isset(check_zip_code_rules[c_code]) && c_code != 'UA' && isset(txt_error_common_zip_code)) {
    check_zip_code_rules[c_code] = {
      error: txt_error_common_zip_code, 
      rules: [/^([ a-z0-9-]+)$/gi]
    };
  }

  if (c_code && typeof(window.check_zip_code_rules) != 'undefined' && typeof(check_zip_code_rules[c_code]) != 'undefined') {
    var rule = check_zip_code_rules[c_code];
    if (rule && rule.rules && rule.rules.constructor == Array && rule.rules.length > 0) {
      zip_error = true;
      for (var i = 0; i < rule.rules.length && zip_error; i++) {
        if (zip.value.search(rule.rules[i]) != -1)
          zip_error = false;
      }
    }
  }

  if (zip_error) {
    if (rule && rule.error && rule.error.length > 0) {
      message = rule.error.replace(/{{address}}/, address);
      if (!isset(check_zip_code_posted_alert) || check_zip_code_posted_alert.indexOf('<' + message + zip.value + '>') == -1) {
        markErrorField(zip);
        xAlert(message);
        check_zip_code_posted_alert = check_zip_code_posted_alert + '<' + message + zip.value + '>';
      }
    }  

    if (zip.focus)
      zip.focus();
  }
  
  return !zip_error;
}

function check_zip_code(form) {

  if (!form || typeof(form) != 'object') {
    return true;
  }

  var err = false;

  $('.zipcode', form).each( function() {
    var zip = this;
    var country = $('#' + $(this).attr('id').replace('zipcode', 'country')).get(0);
    var lbl = '';
    if (country) {
      if ($(this).hasClass('billing')) {
        lbl = lbl_billing_address;
      }
      if ($(this).hasClass('shipping')) {
        if ($('#ship2diff').not(':checked').length) {
          return true;
        }
        lbl = lbl_shipping_address;
      }
      if (!check_zip_code_field(country, this, lbl)) {
        err = true;
        return false;
      }
    }
  });

  return !err; 
}
