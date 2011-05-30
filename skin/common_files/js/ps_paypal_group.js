/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Paypal methods configuration
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: ps_paypal_group.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function view_solution(solution) {

  $('#pp_promo').html(pp_promo[solution]);

  if (
    !document.getElementById('sol_ipn')
    || !document.getElementById('sol_pro')
  ) {
    return false;
  }

  if (solution == "ipn") {
    document.getElementById('sol_ipn').style.display = '';
    document.getElementById('sol_pro').style.display = 'none';
    document.getElementById('sol_express').style.display = 'none';
    document.getElementById('sol_uk').style.display = 'none';

  } else if (solution == "uk") {
    document.getElementById('sol_ipn').style.display = 'none';
    document.getElementById('sol_pro').style.display = 'none';
    document.getElementById('sol_express').style.display = 'none';
    document.getElementById('sol_uk').style.display = '';

  } else if (solution == 'pro') {
    document.getElementById('sol_ipn').style.display = 'none';
    document.getElementById('sol_express').style.display = 'none';
    document.getElementById('sol_pro').style.display = '';
    document.getElementById('sol_uk').style.display = 'none';

  } else {
    document.getElementById('sol_ipn').style.display = 'none';
    document.getElementById('sol_express').style.display = '';
    document.getElementById('sol_pro').style.display = 'none';
    document.getElementById('sol_uk').style.display = 'none';

  }
}

function changeExpressMethod()
{
  if (
    !document.getElementById('method_email')
    || !document.getElementById('method_api')
    || (!document.getElementById('method_email').checked && !document.getElementById('method_api').checked)
  ) {
    return false;
  }

  if (document.getElementById('method_email').checked) {
    document.getElementById('express_email').disabled = false;
    $('input', document.getElementById('api_settings')).attr('disabled', 'disabled');
    $('select', document.getElementById('api_settings')).attr('disabled', 'disabled');

  } else {
    document.getElementById('express_email').disabled = true;
    $('input', document.getElementById('api_settings')).attr('disabled', '');
    $('select', document.getElementById('api_settings')).attr('disabled', '');
  }

  return true;
}

$(document).ready(
  function() {
    $('#pp_promo').html(pp_promo[paypal_solution]);
    changeExpressMethod();
  }
);
