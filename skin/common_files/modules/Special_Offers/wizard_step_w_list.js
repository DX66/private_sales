/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Offers creation/modification wizard
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: wizard_step_w_list.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function box_visible(index, visible) {
  var def = items_def[index];

  var mark = document.getElementById(def[1]);
  var item = document.getElementById(def[2]);
  var box  = document.getElementById(def[3]);
  var status_box  = document.getElementById(def[4]+'_box');

  if (visible) {
    box.style.display = '';
    item.style.fontWeight = 'bold';
    status_box.style.display = 'none';

  } else {
    box.style.display = 'none';
    item.style.fontWeight = 'normal';
    status_box.style.display = mark.checked ? '' : 'none';
  }
}

function select_item(id, index, event) {
  var select_current = false;
  var def = items_def[index];
  var mark = document.getElementById(def[1]);

  document.forms.wizardform.last_item_type.value = index;
  select_current = true;

  for (var i in items_def) {
    if (!hasOwnProperty(items_def, i))
      continue;

    if (i == index) {
      box_visible(index, select_current);
      continue;
    }

    box_visible(i, false);
  }

  change_selected_row(active_row_id, 'item_row_'+index, select_current);

  return true;
}

function select_row(row, select) {
  if (!isset(row))
    return;

  if (row.id == active_row_id)
    row.classname = 'SubHeaderGreyLine';

  else if (select) {
    row.classname = 'TableSubHead';
  }
}

function change_selected_row(id_old, id_new, final_select) {
  active_row_id = (final_select && id_new != '') ? id_new : '';

  if (id_old != '') {
    row  = document.getElementById(id_old);
    select_row(row, false);
  }

  if (id_new != '') {
    row  = document.getElementById(id_new);
    select_row(row, true);
  }
}
