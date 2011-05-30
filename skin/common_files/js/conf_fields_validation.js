/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Fields validation
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: conf_fields_validation.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function validateFields() {
  var value, res, num, is_int, is_negative, is_nonzero, is_empty;
  if (!document.processform)
    return true;

  for (var i = 0; i < validationFields.length; i++) {
    if (!validationFields[i].name || !document.processform[validationFields[i].name])
      continue;

    value = document.processform[validationFields[i].name].value;
    value = value.replace(/^\s+/g, '').replace(/\s+$/g, '');
    is_empty = value.length == 0;

    res = false;

    switch (validationFields[i].validation) {
      case 'exec':
      case 'url':
        res = true;
        break;

      case 'url:http':
                res = is_empty || value.search(/^http:\/\//) !== -1;
                break;

      case 'url:https':
                res = is_empty || value.search(/^https:\/\//) !== -1;
                break;

      case 'url:ftp':
        res = is_empty || value.search(/^ftp:\/\//) !== -1;
        break;

      case "email":
        res = is_empty || value.search(email_validation_regexp) !== -1;
        break;

            case "emails":
        var emails = value.split(/,/);
        var n = 0;
        for (var m = 0; m < emails.length; m++) {
          if (emails[m].replace(/^\s+/g, '').replace(/\s+$/g, '').search(email_validation_regexp) !== -1)
            n++;

        }
                res = is_empty || (emails.length == n);
                break;

      case "port":
                if (!check_is_number(value))
                    break;

        num = convert_number(value);
        res = num > 0 && num < 65536;
        break;

            case "tz_offset":
                if (!check_is_number(value))
                    break;

        if (is_empty)
          value = 0;

                num = convert_number(value);
                res = num > -25 && num < 25;
                break;
        
      case "int":
            case "uint":
            case "uintz":
            case "double":
            case "udouble":
            case "udoublez":

                if (is_empty) {
                    value = 0;
          num = 0;
          is_int = true;
          is_negative = false;
          is_nonzero = false;

        } else {
          if (!check_is_number(value))
            break;

          num = convert_number(value);
          is_int = Math.floor(num) == num;
          is_negative = num < 0;
          is_nonzero = num != 0;
        }
 
        switch (validationFields[i].validation) {
                case "int":
            res = is_int;
            break;

                case "uint":
                        res = is_int && !is_negative;
                        break;

                case "uintz":
                        res = is_int && !is_negative && is_nonzero;
                        break;

                case "udouble":
                        res = !is_negative;
                        break;

                case "udoublez":
                        res = !is_negative && is_nonzero;
                        break;

          default:
            res = true;

        }
        break;

      default:
        res = value.search(validationFields[i].validation) !== -1;
    }

    if (res) {
      switch (validationFields[i].name) {
        case 'max_nav_pages':
          var max_nav_pages = convert_number(value);
          if (max_nav_pages < 2 || max_nav_pages > 25)
            res = false;
          break;
      }
    }

    if (!res && validationFields[i].comment) {
            if (document.processform[validationFields[i].name].focus)
                document.processform[validationFields[i].name].focus();

      alert(substitute(invalid_parameter_text, 'field', validationFields[i].comment.replace(/<br\s*\/>/gi, "\n")));

      return false;
    }
  }
  return true;
}
