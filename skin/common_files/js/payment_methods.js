/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Payment methods functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: payment_methods.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function markDisabledCB(obj) {
  $(obj).parents('table').eq(0).find(':checkbox:disabled').attr('checked', obj.checked ? 'checked' : '');
}

function changeDisabledOrderBy(obj) {
  $(obj).parents('table').eq(0).find(':checkbox:disabled').parents('tr').eq(0).find(':text').filter(function() { return this.name.search(/orderby/) != -1; }).attr('value', obj.value);
}
