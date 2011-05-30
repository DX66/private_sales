/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * JS used in Gift certificates -> Bonus points conversion
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: bonuses_points2gc.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function check_gc_form() {
  goodAmount=document.gccreate.amount.value.search(/^[0-9]+(\.[0-9][0-9]?)?$/);
  if (document.gccreate.recipient.value == "") {
    document.gccreate.recipient.focus();
    alert (txt_recipient_invalid);
    return false;
  }
  if (goodAmount==-1 || document.gccreate.amount.value<offers_bp_min) {
    document.gccreate.amount.focus();
    alert (txt_amount_invalid);
    return false;
  }
  if (!checkEmailAddress(document.gccreate.recipient_email)) {
    document.gccreate.recipient_email.focus();
        return false;
  }
  return true;
}

function formSubmit() {
  if (check_gc_form()) {
    document.gccreate.submit();
  }
}

function price_format(price, precision) {
  var x, cnt, top, botom;
  precision = Math.pow(10, precision);
  price = Math.round(price*precision)/precision;
  top = Math.floor(price);
  bottom = Math.round((price-top)*precision)+precision;
  top = top+"";
  bottom = bottom+"";
  cnt = 0;
  for (x = top.length; x >= 0; x--) {
    if(cnt%3 == 0 && cnt > 0 && x > 0)
    top = top.substr(0,x)+","+top.substr(x,top.length);
    cnt++;
  }
  price = top+"."+bottom.substr(1,bottom.length);
  return price;
}

function conv_amount() {
  var amount = document.getElementById('bp_amount').value;
  var errmsg = '';
  if (amount < offers_bp_min) {
    errmsg = msg_not_enough;
  }
  else if (amount > offers_bp_max) {
    errmsg = msg_too_low;
  }
  else {
    document.getElementById('converted_amount').innerHTML = price_format(amount * offers_bp_rate,2);
  }

  document.getElementById('bp_conv_err').innerHTML = errmsg;
}

