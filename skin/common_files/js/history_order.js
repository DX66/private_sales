/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * History order scripts
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: history_order.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/* $Id: history_order.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $ */

function switch_details_mode(edit_mode, cur_btn, old_btn) {
  var dv = document.getElementById("details_view");
  var de = document.getElementById("details_edit");

  if (!dv || !de || edit_mode == details_mode)
    return;

  if (edit_mode) {
    dv.style.display = 'none';
    de.style.display = '';

  } else {
      var rval = de.value;
      for (var of in details_fields_labels) {
      if (hasOwnProperty(details_fields_labels, of))
            rval = rval.replace(new RegExp(of, "g"), details_fields_labels[of]);
      }
      dv.value = rval;

    dv.style.display = '';
    de.style.display = 'none';
  }

  details_mode = edit_mode;
  cur_btn.style.fontWeight = 'bold';
  old_btn.style.fontWeight = '';
}
