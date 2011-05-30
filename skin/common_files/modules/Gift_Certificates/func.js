/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Functions for gift certificate module
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: func.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function check_gc_form() {
  if (document.gccreate.recipient.value == "") {
    document.gccreate.recipient.focus();
    alert (txt_recipient_invalid);
    return false;
  }

  if (document.gccreate.purchaser.value == "") {
    document.gccreate.purchaser.focus();
    alert (txt_gc_enter_mail_address);
    return false;
  }

  var num = convert_number(document.gccreate.amount.value);
  if (!check_is_number(document.gccreate.amount.value) || (num < min_gc_amount || (max_gc_amount > 0 && num > max_gc_amount))) {
    document.gccreate.amount.focus();
    alert (txt_amount_invalid);
    return false;
  }

  if (enablePostMailGC) {
    if (document.gccreate.send_via[0].checked)
      if (document.gccreate.recipient_email.value == '') {
        alert (txt_gc_enter_mail_address);
        document.gccreate.recipient_email.focus();
        return false;

      } else if (!checkEmailAddress(document.gccreate.recipient_email)) {
        document.gccreate.recipient_email.focus();
        return false;
      }

    if (document.gccreate.send_via[1].checked) {
      var was_error = false;

      if (document.gccreate.recipient_firstname.value == "") {
        was_error = true;
        document.gccreate.recipient_firstname.focus();

      } else if (document.gccreate.recipient_lastname.value == "") {
        was_error = true;
        document.gccreate.recipient_lastname.focus();

      } else if (document.gccreate.recipient_address.value == "") {
        was_error = true;
        document.gccreate.recipient_address.focus();

      } else if (document.gccreate.recipient_city.value == "") {
        was_error = true;
        document.gccreate.recipient_city.focus();

      } else if (document.gccreate.recipient_zipcode.value == "") {
        was_error = true;
        document.gccreate.recipient_zipcode.focus();
      }

      if (was_error) {
        alert (txt_gc_enter_mail_address);
        return false;
      }
    }

  } else if (document.gccreate.recipient_email.value == '') {
    alert (txt_gc_enter_mail_address);
    document.gccreate.recipient_email.focus();
    return false;

  } else if (!checkEmailAddress(document.gccreate.recipient_email)) {
    document.gccreate.recipient_email.focus();
    return false;
  }

  return true;
}

function switchPreview() {
  if (!enablePostMailGC)
    return false;

  if (document.gccreate.send_via[0].checked) {
    if (document.getElementById('preview_button'))
      document.getElementById('preview_button').style.display = 'none';
    document.getElementById('preview_template').style.display = 'none';
  }

  if (document.gccreate.send_via[1].checked) {
    if (document.getElementById('preview_button'))
      document.getElementById('preview_button').style.display = '';
    document.getElementById('preview_template').style.display = '';
  }
}

function formPreview() {
  if (!enablePostMailGC)
    return false;

  if (check_gc_form()) {
    document.gccreate.mode.value='preview';
    document.gccreate.target='_blank'
    document.gccreate.submit();

    setTimeout(
      getMethod(
        function() {
          this.mode.value = orig_mode;
          this.target = '';
        },
        document.gccreate
      ),
      500
    );
  }
}


