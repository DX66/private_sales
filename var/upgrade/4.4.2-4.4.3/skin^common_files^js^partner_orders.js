/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Partner orders
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: partner_orders.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function markPartnerPayment(obj) {
  if (!obj || !obj.id)
    return false;

  var v = parseFloat(obj.value);

  if (!document.getElementById('partners') || !document.getElementById('update_button') || isNaN(v))
    return false;
  var m = obj.id.match(/^paid_(\d+)_\d+$/);

  if (!m)
    return false;

  var id = -1;
  for (var i = 0; i < ready.length && id < 0; i++) {
    if (ready[i].userid == m[1])
      id = i;
  }

  if (id < 0)
    return false;

  var row = document.getElementById('row_' + ready[id].userid);
  if (!row)
    return false;

  if (!obj.checked)
    v = v * -1;

  ready[id].total += v;

  ready[id].total = round(ready[id].total, 2)

  row.innerHTML = price_format(ready[id].total);

  if (ready[id].total < 0.01) {
    ready[id].total = 0;
    row.parentNode.className = 'zero';

  } else if (ready[id].total >= ready[id].min_paid){
    row.parentNode.className = 'ready';

  } else {
    row.parentNode.className = 'min';

  }

  var is_empty = true;
  var is_complete = true;
  for (var i = 0; i < ready.length; i++) {
    if (ready[i].total > 0) {
      is_empty = false;
      if (ready[i].total < ready[i].min_paid)
        is_complete = false;
    }
  }

  document.getElementById('partners').style.display = is_empty ? 'none' : '';

  document.getElementById('update_button').disabled = !is_complete || is_empty;

  return true;
}
