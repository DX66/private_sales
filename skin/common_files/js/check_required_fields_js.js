/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Check required fields
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: check_required_fields_js.js,v 1.2.2.1 2010/10/28 12:04:42 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Check required fields
 */
function checkRequired(lFields, id) {
  if (!lFields || lFields.length == 0)
    return true;

  if (id) {
    for (var x = 0; x < lFields.length; x++) {
      if (lFields[x][0] == id) {
        lFields = [lFields[x]];
        break;
      }
    }
  }

  var errFields = [];
  for (var x = 0; x < lFields.length; x++) {
    if (!lFields[x] || (!document.getElementById(lFields[x][0]) && !document.getElementById(lFields[x][0]+'Adv')))
      continue;

    var obj = _getById(lFields[x][0]);
    var objEditor = false;    

    /* For WYSIWYG editor. */
    if (obj && obj.style.display == 'none' && _getById(lFields[x][0]+'Adv')) {
      obj = _getById(lFields[x][0]+'Adv');
      objEditor = get_html_editor(lFields[x][0]);
      var _value = editor_get_xhtml_body(lFields[x][0]);
      if (obj.value != _value) obj.value = _value;
    }

    if ((obj.type == 'text' || obj.type == 'password' || obj.type == 'textarea') && !obj.value.search(/^[\s]*$/gi)) {
      if (is_admin_editor) {
        errFields[errFields.length] = lFields[x][1];

      } else {
        alert(lbl_required_field_is_empty ? substitute(lbl_required_field_is_empty, 'field', lFields[x][1]) : lFields[x][1]);
        if (!obj.disabled && obj.type != 'hidden') {
          checkRequiredShow(obj);
          if (objEditor) {
            obj.focus();
            obj.style.display = 'none';
            objEditor.focus();
          } else {
            obj.focus();
          }
        }
        return false;
      }
    }
  }

  if (errFields.length > 0) {
    return confirm(substitute(txt_required_fields_not_completed, 'fields', "\n\t" + errFields.join(",\n\t") + "\n\n"));
  }

  return true;
}

/**
 * Show hidden element and element's parents
 */
function checkRequiredShow(elm) {
  if (elm.style && elm.style.display == 'none') {

    if (elm.id == 'ship_box' && document.getElementById('ship2diff')) {
      /* Exception for Register page */
      document.getElementById('ship2diff').checked = true;
      document.getElementById('ship2diff').onclick();
      
    } else
      elm.style.display = '';
  }

  if (elm.parentNode)
    checkRequiredShow(elm.parentNode);

}
