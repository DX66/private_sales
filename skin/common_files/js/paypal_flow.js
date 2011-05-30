/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Paypal flow page
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: paypal_flow.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Add new gateway from select-box to list
 */
function addGateway() {
  var s = document.getElementById('others');
  if (!s) {
    return false;
  }

  var paymentId = s.options[s.selectedIndex].value;
  var payment = s.options[s.selectedIndex].text;

  var isComplex = false;
  for (var i = 0; i < complexProcessors.length && !isComplex; i++) {
    if (complexProcessors[i] == paymentId) {
      isComplex = true;
    }
  }

  if (isComplex) {
    var ul = $('.paypal-flow ul.complex').eq(0);

  } else {
    var ul = $('.paypal-flow ul.gateways').eq(0);
  }
  if (!ul.length) {
    return false;
  }

  var input = document.createElement('INPUT');
  input.type = 'checkbox';
  input.name = 'methods[]';
  input.value = paymentId;
  input.id = paymentId + '_method';
  $(input).click(markMethodLI);

  var label = document.createElement('LABEL');
  label.setAttribute('for', input.id);
  label.innerHTML = payment;

  var li = document.createElement('LI');
  li.appendChild(input);
  li.appendChild(label);

  ul.append(li);

  var sIndex = s.selectedIndex;
  s.options[s.selectedIndex] = null;
  if (sIndex > s.options.length - 1) {
    sIndex = s.options.length - 1;
  }
  s.selectedIndex = sIndex;
  s.options[sIndex].selected = true;

  return li;
}

function markMethodLI() {
  this.parentNode.className = this.checked ? 'selected' : '';
  if (this.id == 'paypal_method') {
    var sub = $('li.sub', this.parentNode.parentNode);

    if (sub.length) {
      $(':radio', sub).attr('disabled', this.checked ? '' : 'disabled');

      if (!this.checked) {

        this.savedRadio = $(':checked', sub).get(0);
        $(':checked', sub).attr('checked', '');
        sub.removeClass('sub-selected');

      } else {

        if (this.savedRadio) {
          this.savedRadio.checked = true;

        } else {
          document.getElementById('paypal_wps').checked = true;
        }

        sub.addClass('sub-selected');

      }
    }
  }
}

$(document).ready(
  function() {
    $('.step2b :checkbox').click(markMethodLI);
    $('.step3 :checkbox').click(markMethodLI);
  }
);
