/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Reset form
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: reset.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function reset_form(formname, localDef) {
  var x, y, z, localDef, hash_radio;
  var form = document.forms.namedItem(formname);
  if (!form)
    return false;

  var hash_radio = [];

  for (x = 0; x < form.elements.length; x++) {
    var obj = form.elements[x];
    if ((obj.tagName == 'INPUT' || obj.tagName == 'SELECT' || obj.tagName == 'TEXTAREA') && obj.name != '' && obj.type != 'hidden') {
      var changed = false;
      var reset_value = '';
      var found = false;
      for (y = 0; y < localDef.length && !found; y++) {
        if (obj.name == localDef[y][0] || obj.id == localDef[y][0]) {
          reset_value = localDef[y][1];
          found = true;
        }
      }

      if (!found)
        continue;

      if (obj.tagName == 'SELECT') {
        reset_value = reset_value.valueOf();
        var selectedItems = [];
        if (reset_value.length > 0)
          selectedItems = reset_value.split(',');

                for (z = 0; z < obj.options.length && !changed; z++) {
                    for (y = 0; y < selectedItems.length && !changed; y++) {
                        if ((obj.options[z].value == selectedItems[y] || obj.options[z].text == selectedItems[y]) && !obj.options[z].selected)
              changed = true;
          }
        }

        obj.selectedIndex = obj.multiple ? -1 : 0;

        for (z = 0; z < obj.options.length; z++) {
          for (y = 0; y < selectedItems.length; y++) {
            if (obj.options[z].value == selectedItems[y] || obj.options[z].text == selectedItems[y]) {
              obj.options[z].selected = true;
            }
          }
        }

      } else if (obj.tagName == 'INPUT' && obj.type == 'radio') {
        var is_found = false;
        for (z = 0; z < hash_radio.length; z++) {
          if (hash_radio[z][0] == obj.name) {
            is_found = hash_radio[z][1];
            break;
          }
        }
        if (is_found == 'F')
          continue;

        if ((obj.value == reset_value || is_found === false) && !obj.checked)
          changed = true;

        obj.checked = (obj.value == reset_value || is_found === false);
        hash_radio[hash_radio.length] = [obj.name, (obj.value == reset_value) ? "F" : ""];

      } else if (obj.tagName == 'INPUT' && obj.type == 'checkbox') {
        if (obj.checked != reset_value)
          changed = true;

        obj.checked = reset_value;
        
      } else {
        if (obj.value != reset_value)
          changed = true;

        obj.value = reset_value;
      }

      if (!obj.disabled && changed) {
        if (obj.onclick)
          obj.onclick.call(obj);
        if (obj.onchange)
          obj.onchange.call(obj);
      }
    }
  }
}
